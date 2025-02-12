<?php
session_start();
require 'db_connect.php'; // Kobling til databasen

// Sjekk om bruker er logget inn og er foreleser
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'foreleser') {
    header("Location: login.php");
    exit();
}

$foreleser_id = $_SESSION['user_id'];
$foreleser_email = $_SESSION['user_email'];
$foreleser_navn = $_SESSION['user_name'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hent meldinger fra databasen
try {
    $stmt = $conn->prepare("SELECT m.id, m.innhold, m.dato_opprettet, b.e_post AS bruker_e_post 
                            FROM meldinger m
                            JOIN brukere b ON m.student_id = b.id
                            ORDER BY m.dato_opprettet DESC");
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Feil ved henting av meldinger: " . $e->getMessage());
}

// Håndtering av svar på meldinger
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    try {
        $message_id = $_POST['message_id'];
        $reply = htmlspecialchars($_POST['reply']);

        $stmt = $conn->prepare("INSERT INTO svar (melding_id, bruker_id, innhold) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $message_id, $foreleser_id, $reply);
        $stmt->execute();

        $success = "Svar lagret!";
    } catch (Exception $e) {
        $error = "Feil ved lagring av svar: " . $e->getMessage();
    }
}

// Håndtering av passordbytte
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    try {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        // Hent brukerens lagrede passord
        $stmt = $conn->prepare("SELECT passord_hash FROM brukere WHERE id = ?");
        $stmt->bind_param("i", $foreleser_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($old_password, $user['passord_hash'])) {
            // Oppdater passordet
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE brukere SET passord_hash = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $foreleser_id);
            $update_stmt->execute();

            $success = "Passordet er oppdatert!";
        } else {
            $error = "Feil nåværende passord!";
        }
    } catch (Exception $e) {
        $error = "Feil ved oppdatering av passord: " . $e->getMessage();
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
    <p>Velkommen, <?= htmlspecialchars($foreleser_navn) ?> (<?= htmlspecialchars($foreleser_email) ?>)</p>
    <a href="logout.php">Logg ut</a>
</header>

<div class="container">
    <h2>Meldinger</h2>
    <ul>
        <?php foreach ($messages as $message): ?>
            <li>
                <strong>Melding #<?= $message['id'] ?>:</strong> <?= htmlspecialchars($message['innhold']) ?>
                <p><small>Sendt av: <?= htmlspecialchars($message['bruker_e_post']) ?></small></p>
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
            <input type="password" name="old_password" placeholder="Nåværende passord" required>
            <input type="password" name="new_password" placeholder="Nytt passord" required>
            <button type="submit" name="change_password">Endre passord</button>
        </form>
    </div>

    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
</div>
</body>
</html>
