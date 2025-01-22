<?php
session_start();
if (!isset($_SESSION['student_name'])) {
    header("Location: student.php");
    exit();
}

// Emner (1 til 5)
$subjects = [
    1 => "Emne 1",
    2 => "Emne 2",
    3 => "Emne 3",
    4 => "Emne 4",
    5 => "Emne 5"
];

// Håndtering av anonym klage
if (isset($_POST['send_complaint'])) {
    $selected_subject = $_POST['subject_id'];
    $complaint_text = htmlspecialchars($_POST['complaint_text']);

    // Lagre meldingen anonymt (lagres i session for enkel testing)
    $_SESSION['complaints'][] = [
        'subject' => $subjects[$selected_subject],
        'complaint' => $complaint_text
    ];

    $success = "Din klage på '{$subjects[$selected_subject]}' er sendt anonymt!";
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velkommen</title>
</head>
<body>
    <h1>Velkommen, <?= htmlspecialchars($_SESSION['student_name']) ?>!</h1>
    
    <h2>Emner</h2>
    <ul>
        <?php foreach ($subjects as $id => $subject): ?>
            <li><?= htmlspecialchars($subject) ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Send anonym klage</h2>
    <form method="post">
        <label for="subject_id">Velg emne:</label>
        <select name="subject_id" required>
            <?php foreach ($subjects as $id => $subject): ?>
                <option value="<?= $id ?>"><?= htmlspecialchars($subject) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="complaint_text">Din klage:</label><br>
        <textarea name="complaint_text" rows="4" cols="50" required></textarea><br><br>

        <input type="submit" name="send_complaint" value="Send klage">
    </form>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <h2>Sendte klager</h2>
    <ul>
        <?php
        if (isset($_SESSION['complaints'])) {
            foreach ($_SESSION['complaints'] as $complaint) {
                echo "<li><strong>" . htmlspecialchars($complaint['subject']) . ":</strong> " . htmlspecialchars($complaint['complaint']) . "</li>";
            }
        } else {
            echo "<li>Ingen klager sendt ennå.</li>";
        }
        ?>
    </ul>

    <br>
    <a href="logout.php">Logg ut</a>
</body>
</html>
