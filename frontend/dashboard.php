<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: student.php");
    exit();
}

// Emneliste (dummy data, kan erstattes med en database)
$subjects = [
    1 => "Matematikk",
    2 => "Programmering",
    3 => "Databaser",
    4 => "Maskinlæring"
];

// Håndtering av anonym melding
if (isset($_POST['send_message'])) {
    $subject_id = $_POST['subject_id'];
    $message_text = htmlspecialchars($_POST['message_text']);

    $_SESSION['messages'][] = [
        'subject' => $subjects[$subject_id],
        'message' => $message_text
    ];

    $success = "Din melding er sendt anonymt!";
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Velkommen, <?= htmlspecialchars($_SESSION['student_name']) ?>!</h1>

    <h2>Send anonym melding</h2>
    <form method="post">
        Velg emne:
        <select name="subject_id" required>
            <?php foreach ($subjects as $id => $subject): ?>
                <option value="<?= $id ?>"><?= htmlspecialchars($subject) ?></option>
            <?php endforeach; ?>
        </select><br>
        Melding: <textarea name="message_text" required></textarea><br>
        <input type="submit" name="send_message" value="Send melding">
    </form>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <h2>Sendte meldinger</h2>
    <ul>
        <?php
        if (isset($_SESSION['messages'])) {
            foreach ($_SESSION['messages'] as $message) {
                echo "<li><strong>" . htmlspecialchars($message['subject']) . ":</strong> " . htmlspecialchars($message['message']) . "</li>";
            }
        } else {
            echo "<li>Ingen meldinger sendt ennå.</li>";
        }
        ?>
    </ul>

    <a href="logout.php">Logg ut</a>
</body>
</html>
