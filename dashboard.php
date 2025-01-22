<?php
session_start();

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if (!isset($_SESSION['lecturer_id'])) {
    die("Du må logge inn for å se denne siden.");
}

// Simulerte meldinger
$messages = [
    ["id" => 1, "content" => "Hvordan kan jeg forbedre koden min?"],
    ["id" => 2, "content" => "Kan du forklare mer om sikkerhet?"]
];
?>

<h1>Velkommen til dashboardet</h1>
<h2>Meldinger</h2>
<ul>
    <?php foreach ($messages as $message): ?>
        <li>
            <?= sanitize($message['content']) ?>
            <a href="messages.php?message_id=<?= $message['id'] ?>">Svar</a>
        </li>
    <?php endforeach; ?>
</ul>
