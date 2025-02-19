<?php
require 'db_connect.php'; // Inneholder MySQLi-koblingen i variabelen $conn

$messages = [];
$subjectInfo = null;

/* Håndtering av kommentarinnsending */
if (isset($_POST['comment_text']) && isset($_POST['message_id'])) {
    $commentText = $_POST['comment_text'];
    $messageId   = $_POST['message_id'];

    $stmtComm = $conn->prepare("INSERT INTO kommentarer (melding_id, innhold) VALUES (?, ?)");
    $stmtComm->bind_param("is", $messageId, $commentText);
    $stmtComm->execute();
    $stmtComm->close();
    echo "Kommentar er lagt til!";
}

// Hent studentinfo fra sesjonen
$student_id = $_SESSION['user_id'];
$student_email = $_SESSION['user_email'];
$student_navn = $_SESSION['user_name'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hent emner studenten er påmeldt til (hvis aktuelt)
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

/* Håndtering av innlogging for emne */
if (isset($_POST['subject_code']) && isset($_POST['pin_code'])) {
    $subjectCodeInput = $_POST['subject_code'];
    $pinCodeInput     = $_POST['pin_code'];

    // Finn emnet ut fra emnekode
    $stmt = $conn->prepare("SELECT * FROM emner WHERE emnekode = ?");
    $stmt->bind_param("s", $subjectCodeInput);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();

    if ($subject) {
        // Sjekk PIN
        if ($subject['pin_kode'] == $pinCodeInput) {
            $subjectInfo = $subject;
            
            // Hent foreleser-info
            $stmtLect = $conn->prepare("SELECT navn, bilde FROM brukere WHERE id = ?");
            $stmtLect->bind_param("i", $subjectInfo['foreleser_id']);
            $stmtLect->execute();
            $resultLect = $stmtLect->get_result();
            $lecturerData = $resultLect->fetch_assoc();
            $stmtLect->close();

            // Sjekk om foreleser har lastet opp et bilde, ellers vis standardbilde
            $foreleserBilde = !empty($lecturerData['bilde']) ? htmlspecialchars($lecturerData['bilde']) : "uploads/default.png";

            // Hent meldinger for dette emnet
            $stmtMsg = $conn->prepare("SELECT m.*, u.navn as student_navn, s.innhold as svar_innhold, s.dato_opprettet as svar_dato 
                                       FROM meldinger m
                                       LEFT JOIN svar s ON m.id = s.melding_id
                                       LEFT JOIN brukere u ON m.student_id = u.id
                                       WHERE m.emne_id = ?
                                       ORDER BY m.dato_opprettet DESC");
            $stmtMsg->bind_param("i", $subject['id']);
            $stmtMsg->execute();
            $resultMsg = $stmtMsg->get_result();
            $messages = $resultMsg->fetch_all(MYSQLI_ASSOC);
            $stmtMsg->close();
        } else {
            echo "<p style='color:red;'>Feil PIN-kode!</p>";
        }
    } else {
        echo "<p style='color:red;'>Fant ikke emnekode!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gjesteside</title>
    <link rel="stylesheet" href="style_index.css">
</head>
<body>

    <h1>Gjest – vis meldinger for emne</h1>

    <p><a href="Login.php" class="btn">Logg inn</a> (for studenter/forelesere)</p>

    <!-- Skjema for å oppgi emnekode og PIN-kode -->
    <form method="post">
        <!-- Emnekode: <input type="text" name="subject_code" required> -->
        <select name="subject_id" required>
            <?php foreach ($subjects as $id => $subject): ?>
                <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($subject['name']) ?></option>
            <?php endforeach; ?>
        </select><br> 
        PIN-kode: <input type="password" name="pin_code" required maxlength="4">
        <input type="submit" class="btn" value="Vis meldinger">
    </form>
    <hr> 

    <!-- Viser emneinfo, meldinger, svar og kommentarer -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $subjectInfo): ?>
        <h2><?= htmlspecialchars($subjectInfo['emnekode']) ?> - <?= htmlspecialchars($subjectInfo['emnenavn']) ?></h2>
        
        <p>Foreleser: <?= htmlspecialchars($lecturerData['navn']) ?></p>
        <img src="<?= $foreleserBilde ?>" alt="Bilde av foreleser" width="150">

        <hr>

        <?php foreach ($messages as $msg): ?>
            <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
                <p><strong>Melding:</strong> <?= htmlspecialchars($msg['innhold']) ?></p>
                <?php if ($msg['svar_innhold']): ?>
                    <p><strong>Svar fra foreleser:</strong> <?= htmlspecialchars($msg['svar_innhold']) ?></p>
                <?php endif; ?>
                
                <?php
                // Hent kommentarer til denne meldingen
                $stmtComm = $conn->prepare("SELECT innhold, dato_opprettet FROM kommentarer WHERE melding_id = ? ORDER BY dato_opprettet DESC");
                $stmtComm->bind_param("i", $msg['id']);
                $stmtComm->execute();
                $resultComm = $stmtComm->get_result();
                $comments = $resultComm->fetch_all(MYSQLI_ASSOC);
                $stmtComm->close();
                ?>
                <div style="margin-left:20px;">
                    <strong>Kommentarer:</strong><br>
                    <?php foreach ($comments as $c): ?>
                        <p>- <?= htmlspecialchars($c['innhold']) ?> (<?= $c['dato_opprettet'] ?>)</p>
                    <?php endforeach; ?>
                    <form method="post">
                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                        <input type="text" name="comment_text" placeholder="Skriv en kommentar...">
                        <input type="submit" class="btn" value="Legg til">
                    </form>
                </div>
                <p><a href="?report=<?= $msg['id'] ?>">Rapporter upassende melding</a></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
