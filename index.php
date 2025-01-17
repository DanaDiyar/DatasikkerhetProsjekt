<?php
require 'db.php';

$messages = [];
$subjectInfo = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subjectCodeInput = $_POST['subject_code'];
    $pinCodeInput     = $_POST['pin_code'];

    // Finn emnet ut fra emnekode
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_code = ?");
    $stmt->execute([$subjectCodeInput]);
    $subject = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subject) {
        // Sjekk PIN
        if ($subject['pin_code'] == $pinCodeInput) {
            // Hent meldinger for dette emnet
            $subjectInfo = $subject;

            $stmtMsg = $pdo->prepare("SELECT m.*, u.name as student_name, r.reply_text, r.created_at as reply_created 
                                      FROM messages m
                                      LEFT JOIN replies r ON m.id = r.message_id
                                      LEFT JOIN users u ON m.student_id = u.id
                                      WHERE m.subject_id = ?
                                      ORDER BY m.created_at DESC");
            $stmtMsg->execute([$subject['id']]);
            $messages = $stmtMsg->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "Feil PIN-kode!";
        }
    } else {
        echo "Fant ikke emnekode!";
    }
}

// Håndtering av rapportering
if (isset($_GET['report'])) {
    $messageId = $_GET['report'];
    $stmtRep = $pdo->prepare("UPDATE messages SET reported = 1 WHERE id = ?");
    $stmtRep->execute([$messageId]);
    echo "Meldingen er rapportert!";
}

// Håndtering av kommentar
if (isset($_POST['comment_text']) && isset($_POST['message_id'])) {
    $commentText = $_POST['comment_text'];
    $messageId   = $_POST['message_id'];

    $stmtComm = $pdo->prepare("INSERT INTO comments (message_id, comment_text) VALUES (?, ?)");
    $stmtComm->execute([$messageId, $commentText]);
    echo "Kommentar er lagt til!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gjesteside</title>
</head>
<body>
    <h1>Gjest – vis meldinger for emne</h1>

    <form method="post">
        Emnekode: <input type="text" name="subject_code" required>
        PIN-kode: <input type="text" name="pin_code" required maxlength="4">
        <input type="submit" value="Vis meldinger">
    </form>
    <hr> 

    <?php if ($subjectInfo): ?>
        <h2><?= htmlspecialchars($subjectInfo['subject_code']) ?> - <?= htmlspecialchars($subjectInfo['subject_name']) ?></h2>
        <?php
        // Hent foreleser-info
        $stmtLect = $pdo->prepare("SELECT name, image_url FROM users WHERE id = ?");
        $stmtLect->execute([$subjectInfo['lecturer_id']]);
        $lecturerData = $stmtLect->fetch(PDO::FETCH_ASSOC);
        ?>
        <p>Foreleser: <?= htmlspecialchars($lecturerData['name']) ?></p>
        <?php if ($lecturerData['image_url']): ?>
            <img src="<?= htmlspecialchars($lecturerData['image_url']) ?>" alt="Bilde av foreleser" width="100">
        <?php endif; ?>

        <hr>

        <?php foreach ($messages as $msg): ?>
            <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
                <p><strong>Melding (anonym):</strong> <?= htmlspecialchars($msg['message_text']) ?></p>
                <?php if ($msg['reply_text']): ?>
                    <p><strong>Svar fra foreleser:</strong> <?= htmlspecialchars($msg['reply_text']) ?></p>
                <?php endif; ?>
                
                <?php
                // Hent kommentarer til denne meldingen
                $stmtComm = $pdo->prepare("SELECT comment_text, created_at FROM comments WHERE message_id = ? ORDER BY created_at DESC");
                $stmtComm->execute([$msg['id']]);
                $comments = $stmtComm->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div style="margin-left:20px;">
                    <strong>Kommentarer:</strong><br>
                    <?php foreach ($comments as $c): ?>
                        <p>- <?= htmlspecialchars($c['comment_text']) ?> (<?= $c['created_at'] ?>)</p>
                    <?php endforeach; ?>
                    <form method="post">
                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                        <input type="text" name="comment_text" placeholder="Skriv en kommentar...">
                        <input type="submit" value="Legg til">
                    </form>
                </div>
                <p><a href="?report=<?= $msg['id'] ?>">Rapporter upassende melding</a></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><a href="login.php">Logg inn</a> (for studenter/forelesere)</p>
</body>
</html>
