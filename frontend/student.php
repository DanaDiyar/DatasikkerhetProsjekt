<?php
session_start();

$host = '158.39.188.205';
$dbname = 'Datasikkerhet';
$username = 'datasikkerhet';
$password = 'DittPassord';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Emneliste
$subjects = [
    1 => "Emne 1",
    2 => "Emne 2",
    3 => "Emne 3",
    4 => "Emne 4"
];

if (isset($_POST['send_message'])) {
    $subject_id = $_POST['subject_id'];
    $message_text = htmlspecialchars($_POST['message_text']);

    // Sett inn melding i databasen
    $stmt = $conn->prepare("INSERT INTO meldinger (subject, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $subjects[$subject_id], $message_text);

    if ($stmt->execute()) {
        $success = "Din melding er sendt og lagret i databasen!";
    } else {
        $error = "Feil ved sending av melding: " . $conn->error;
    }

    $stmt->close();
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
                <option value="<?= $id ?>"><?= htmlspecialchars($subject) ?></option>
            <?php endforeach; ?>
        </select><br>
        Melding: <textarea name="message_text" required></textarea><br>
        <input type="submit" name="send_message" value="Send anonym melding">
    </form>

    <?php if (isset($success)): ?>
        <p style="color: green;"> <?= $success ?> </p>
    <?php endif; ?>
</body>
</html>
