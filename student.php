<?php
session_start();
require 'db_connect.php';

// Sjekk om student er logget inn
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Hent studentinfo fra sesjonen
$student_id = $_SESSION['user_id'];
$student_email = $_SESSION['user_email'];
$student_navn = $_SESSION['user_name'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hent emner studenten er påmeldt til (hvis aktuelt)
$subjects = [];
$query = "SELECT id, emnenavn, foreleser_id FROM emner";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subjects[$row['id']] = [
            'name' => $row['emnenavn'],
            'foreleser_id' => $row['foreleser_id']
        ];
    }
} else {
    $subjects[0] = ['name' => "Ingen emner funnet", 'foreleser_id' => null];
}

// Håndtering av meldinger
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $subject_id = $_POST['subject_id'];
    $message_text = htmlspecialchars($_POST['message_text']);

    if (isset($subjects[$subject_id])) {
        $foreleser_id = $subjects[$subject_id]['foreleser_id'];

        if ($foreleser_id) {
            $stmt = $conn->prepare("INSERT INTO meldinger (emne_id, student_id, innhold) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $subject_id, $student_id, $message_text);

            if ($stmt->execute()) {
                $success = "Din melding er sendt til foreleseren!";
            } else {
                $error = "Feil ved sending av melding: " . $conn->error;
            }

            $stmt->close();
        } else {
            $error = "Ingen foreleser er tilknyttet dette emnet.";
        }
    } else {
        $error = "Ugyldig emne valgt.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" type="text/css" href="stylestudent.css">
</head>
<body>
<header>
    <h1>Student Dashboard</h1>
    <p>Velkommen, <?= htmlspecialchars($student_navn) ?> (<?= htmlspecialchars($student_email) ?>)</p>
    <p><a href="Login.php" class="btn">Logg ut</a></p>
</header>

<div class="container">
    <h2>Send melding til foreleser</h2>
    <form method="post">
        <label for="subject">Velg emne:</label>
        <select name="subject_id" required>
            <?php foreach ($subjects as $id => $subject): ?>
                <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($subject['name']) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="message_text">Melding:</label>
        <textarea name="message_text" required></textarea><br>

        <input type="submit" name="send_message" value="Send melding">
    </form>

    <?php if (isset($success)): ?>
        <p style="color: green;"> <?= $success ?> </p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"> <?= $error ?> </p>
    <?php endif; ?>
</div>

</body>
</html>
