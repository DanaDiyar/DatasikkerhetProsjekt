<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Simulert melding (kan erstattes med database)
$message_id = isset($_GET['message_id']) ? sanitize($_GET['message_id']) : null;
$messages = [
    1 => "Hvordan kan jeg forbedre koden min?",
    2 => "Kan du forklare mer om sikkerhet?"
];

if ($message_id && isset($messages[$message_id])) {
    $message_content = $messages[$message_id];
} else {
    die("Du har ikke fått noen meldinger enda Taper.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply = sanitize($_POST['reply']);
    echo "Svar sendt: " . $reply;
    exit();
}
?>

<h1>Svar på melding</h1>
<p><strong>Melding:</strong> <?= $message_content ?></p>
<form method="POST">
    <textarea name="reply" placeholder="Skriv svaret ditt her..." required></textarea>
    <button type="submit">Send svar</button>
</form>
