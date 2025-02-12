<?php
session_start();
include 'db_connection.php';

// Emneliste
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

    <h2>Alle meldinger med kommentarer</h2>
    <?php
    $result = $conn->query("SELECT m.id, m.subject, m.message, m.dato_opprettet, k.innhold AS kommentar 
                            FROM meldinger m 
                            LEFT JOIN kommentarer k ON m.id = k.melding_id 
                            ORDER BY m.dato_opprettet DESC");

    $last_melding_id = null;

    while ($row = $result->fetch_assoc()) {
        if ($last_melding_id !== $row['id']) {
            if ($last_melding_id !== null) {
                echo "</ul>"; // Avslutt forrige melding
            }
            echo "<h3>" . htmlspecialchars($row['subject']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['message']) . "</p>";
            echo "<p><small>Opprettet: " . $row['dato_opprettet'] . "</small></p>";
            echo "<h4>Kommentarer:</h4>";
            echo "<ul>";
            $last_melding_id = $row['id'];
        }

        if ($row['kommentar']) {
            echo "<li>" . htmlspecialchars($row['kommentar']) . "</li>";
        }
    }
    echo "</ul>";
    ?>

    <h2>Legg til en kommentar</h2>
    <form method="post" action="add_comment.php">
        Velg melding:
        <select name="melding_id" required>
            <?php
            $result = $conn->query("SELECT id, subject FROM meldinger ORDER BY dato_opprettet DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['subject']) . "</option>";
            }
            ?>
        </select><br>
        Kommentar: <textarea name="comment_text" required></textarea><br>
        <input type="submit" name="send_comment" value="Send kommentar">
    </form>
</body>
</html>
