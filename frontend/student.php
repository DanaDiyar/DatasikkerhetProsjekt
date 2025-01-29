<?php
session_start();

// Emneliste (dummy data, kan erstattes med en database)
$subjects = [
    1 => "Emne 1",
    2 => "Emne 2",
    3 => "Emne 3",
    4 => "Emne 4"
    
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
        <input type="submit" name="send_message" value="Send anonym meldingen">
    </form>

    <?php if (isset($success)): ?>
        <p style="color: green;"> <?= $success ?> </p>
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
</body>
</html>
