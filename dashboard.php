<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'meldingssystem.php'; // Sørg for at dette peker på riktig fil

try {
    // Hent meldinger fra databasen (Endret 'forelesere' til 'brukere')
    $stmt = $conn->prepare("SELECT m.id, m.content, m.created_at, b.email AS bruker_email 
                            FROM meldinger m
                            JOIN brukere b ON m.lecturer_id = b.id
                            ORDER BY m.created_at DESC");
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Feil ved henting av meldinger: " . $e->getMessage());
}

// Håndtering av svar på meldinger
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    try {
        $message_id = $_POST['message_id'];
        $reply = $_POST['reply'];
        $lecturer_id = 1; // Midlertidig statisk ID

        // Bruker 'svar' tabellen og riktige kolonnenavn
        $stmt = $conn->prepare("INSERT INTO svar (melding_id, bruker_id, innhold) VALUES (?, ?, ?)");
        $stmt->execute([$message_id, $lecturer_id, $reply]);

        echo "<p style='color: green;'>Svar lagret!</p>";
    } catch (PDOException $e) {
        die("Feil ved lagring av svar: " . $e->getMessage());
    }
}

// Håndtering av passordbytte
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    try {
        $old_password = $_POST['old_password'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $lecturer_id = 1; // Midlertidig statisk ID

        $stmt = $conn->prepare("UPDATE brukere SET password_hash = ? WHERE id = ?");
        $stmt->execute([$new_password, $lecturer_id]);

        echo "<p style='color: green;'>Passordet er oppdatert!</p>";
    } catch (PDOException $e) {
        die("Feil ved oppdatering av passord: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forelesers Dashboard</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<header>
    <h1>Forelesers Dashboard</h1>
</header>

<div class="container">
    <h2>Meldinger</h2>
    <ul>
        <?php foreach ($messages as $message): ?>
            <li>
                <strong>Melding #<?= $message['id'] ?>:</strong> <?= htmlspecialchars($message['content']) ?>
                <p><small>Sendt av foreleser: <?= htmlspecialchars($message['lecturer_email']) ?></small></p>
                <button onclick="document.getElementById('reply-form-<?= $message['id'] ?>').style.display='block'">
                    Svar
                </button>
                <!-- Skjema for svar -->
                <div id="reply-form-<?= $message['id'] ?>" class="reply-form" style="display:none;">
                    <form method="POST">
                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                        <textarea name="reply" placeholder="Skriv svaret ditt her..." required></textarea>
                        <button type="submit">Send svar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Knapp for å vise passordendringsskjema -->
    <h2>Endre passord</h2>
    <button onclick="document.getElementById('change-password-form').style.display='block'">Bytt passord</button>
    <div id="change-password-form" style="display:none;">
        <form method="POST">
            <input type="hidden" name="change_password" value="1">
            <input type="password" name="old_password" placeholder="Nåværende passord" required>
            <input type="password" name="new_password" placeholder="Nytt passord" required>
            <button type="submit">Endre passord</button>
        </form>
    </div>
</div>
</body>
</html>
