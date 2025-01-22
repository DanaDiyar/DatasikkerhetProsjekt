<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Simulerte meldinger (kan erstattes med database)
$messages = [
    ["id" => 1, "content" => "Hvordan kan jeg forbedre koden min?"],
    ["id" => 2, "content" => "Kan du forklare mer om sikkerhet?"]
];

// Simulert lagret passord-hash (erstatt med database)
$fake_password_hash = password_hash("password123", PASSWORD_BCRYPT);

// Håndtering av passordbytte
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = sanitize($_POST['old_password']);
    $new_password = sanitize($_POST['new_password']);

    if (password_verify($old_password, $fake_password_hash)) {
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        echo "<p style='color: green;'>Passordet er endret!</p>";
    } else {
        echo "<p style='color: red;'>Gammelt passord er feil.</p>";
    }
}
?>

<h1>Forelesers meldingspanel</h1>

<!-- Meldinger fra studenter -->
<h2>Meldinger</h2>
<ul>
    <?php foreach ($messages as $message): ?>
        <li>
            <?= sanitize($message['content']) ?>
            <a href="messages.php?message_id=<?= $message['id'] ?>">Svar</a>
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
