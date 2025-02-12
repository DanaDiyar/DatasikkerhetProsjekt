<?php
session_start();
require 'db_connect.php';

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

// Hent emner som er opprettet av denne foreleseren
try {
    $stmt_emner = $conn->prepare("SELECT id, emnekode, emnenavn FROM emner WHERE foreleser_id = ?");
    $stmt_emner->bind_param("i", $foreleser_id);
    $stmt_emner->execute();
    $emner = $stmt_emner->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Feil ved henting av emner: " . $e->getMessage());
}

// Hent meldinger knyttet til emner foreleseren underviser i
try {
    $stmt_meldinger = $conn->prepare("
        SELECT m.id, m.innhold, m.dato_opprettet, b.e_post AS student_email, e.emnenavn 
        FROM meldinger m
        JOIN brukere b ON m.student_id = b.id
        JOIN emner e ON m.emne_id = e.id
        WHERE e.foreleser_id = ?
        ORDER BY m.dato_opprettet DESC
    ");
    $stmt_meldinger->bind_param("i", $foreleser_id);
    $stmt_meldinger->execute();
    $meldinger = $stmt_meldinger->get_result()->fetch_all(MYSQLI_ASSOC);

    // Debugging: Skriv ut SQL-resultater
    if (empty($meldinger)) {
        error_log("Ingen meldinger funnet for foreleser_id: " . $foreleser_id);
    }
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
    <h2>Emnene dine</h2>
    <ul>
        <?php if (!empty($emner)): ?>
            <?php foreach ($emner as $emne): ?>
                <li><strong><?= htmlspecialchars($emne['emnekode']) ?> - <?= htmlspecialchars($emne['emnenavn']) ?></strong></li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Du har ikke opprettet noen emner ennå.</p>
        <?php endif; ?>
    </ul>

    <h2>Meldinger til dine emner</h2>
    <ul>
        <?php if (!empty($meldinger)): ?>
            <?php foreach ($meldinger as $melding): ?>
                <li>
                    <strong><?= htmlspecialchars($melding['emnenavn']) ?></strong>
                    <p>Melding #<?= $melding['id'] ?>: <?= htmlspecialchars($melding['innhold']) ?></p>
                    <p><small>Sendt av: <?= htmlspecialchars($melding['student_email']) ?> - <?= $melding['dato_opprettet'] ?></small></p>
                    <button onclick="document.getElementById('reply-form-<?= $melding['id'] ?>').style.display='block'">
                        Svar
                    </button>
                    <!-- Skjema for svar -->
                    <div id="reply-form-<?= $melding['id'] ?>" class="reply-form" style="display:none;">
                        <form method="POST">
                            <input type="hidden" name="message_id" value="<?= $melding['id'] ?>">
                            <textarea name="reply" placeholder="Skriv svaret ditt her..." required></textarea>
                            <button type="submit">Send svar</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Ingen meldinger funnet for dine emner.</p>
        <?php endif; ?>
    </ul>

    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
</div>
</body>
</html>
