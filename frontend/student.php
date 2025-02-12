<?php
session_start();

$host = '158.39.188.205';
$dbname = 'Datasikkerhet';
$username = 'datasikkerhet';
$password = 'DittPassord';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Databaseforbindelse feilet: " . $conn->connect_error);
}

// Hente emner og deres forelesere fra databasen
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

if (isset($_POST['send_message'])) {
    $subject_id = $_POST['subject_id'];
    $message_text = htmlspecialchars($_POST['message_text']);
    $student_id = $_SESSION['student_id'] ?? null; // Hent student-ID fra session

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
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="stylestudent.css">
</head>
<body>
    <h1>Velkommen!</h1>

    <h2>Send anonym melding</h2>
    <form method="post">
        Velg emne:
        <select name="subject_id" required>
            <?php foreach ($subjects as $id => $subject): ?>
                <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($subject['name']) ?></option>
            <?php endforeach; ?>
        </select><br>
        Melding: <textarea name="message_text" required></textarea><br>
        <input type="submit" name="send_message" value="Send anonym melding">
    </form>

    <?php if (isset($success)): ?>
        <p style="color: green;"> <?= $success ?> </p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"> <?= $error ?> </p>
    <?php endif; ?>
</body>
</html>
