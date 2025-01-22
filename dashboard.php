<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Simulerte meldinger (kan erstattes med database)
$messages = [
    ["id" => 1, "content" => "Hvordan kan jeg forbedre koden min?"],
    ["id" => 2, "content" => "Kan du forklare mer om sikkerhet?"]
];

// Simulert lagret passord-hash (erstatt med database)
$fake_password_hash = hashPassword("password123");

// Håndtering av passordbytte
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = sanitize($_POST['old_password']);
    $new_password = sanitize($_POST['new_password']);

    if (verifyPassword($old_password, $fake_password_hash)) {
        $new_password_hash = hashPassword($new_password);
        echo "<p style='color: green;'>Passordet er endret!</p>";
    } else {
        echo "<p style='color: red;'>Gammelt passord er feil.</p>";
    }
}

// Håndtering av svar på meldinger
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $message_id = sanitize($_POST['message_id']);
    $reply = sanitize($_POST['reply']);
    echo "<p style='color: green;'>Svar sendt for melding #$message_id: $reply</p>";
}
?>

<h1>Forelesers meldingspanel</h1>

<!-- Meldinger fra studenter -->
<h2>Meldinger</h2>
<ul>
    <?php foreach ($messages as $message): ?>
        <li>
            <strong>Melding #<?= $message['id'] ?>:</strong> <?= sanitize($message['content']) ?>
            <!-- Knapp for å svare på melding -->
            <button onclick="document.getElementById('reply-form-<?= $message['id'] ?>').style.display='block'">Svar</button>
            <!-- Skjema for å svare på melding -->
            <div id="reply-form-<?= $message['id'] ?>" style="display: none; margin-top: 10px;">
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

<!-- Passordendringsskjema -->
<div id="change-password-form" style="display: none; margin-top: 20px;">
    <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <input type="password" name="old_password" placeholder="Gammelt passord" required>
        <input type="password" name="new_password" placeholder="Nytt passord" required>
        <button type="submit">Endre passord</button>
    </form>
</div>
