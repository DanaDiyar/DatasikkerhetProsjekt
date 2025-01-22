<?php
session_start();

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if (!isset($_SESSION['lecturer_id'])) {
    die("Du må logge inn for å se denne siden.");
}

$message_id = isset($_GET['message_id']) ? sanitize($_GET['message_id']) : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply = sanitize($_POST['reply']);
    echo "Svar sendt: " . $reply . " (Simulert uten database)";
}
?>

<h1>Svar på melding</h1>
<form method="POST">
    <textarea name="reply" placeholder="Skriv svaret ditt her..." required></textarea>
    <button type="submit">Send</button>
</form>
