<!DOCTYPE html>
<html lang="en">
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
    <!-- Meldinger fra studenter -->
    <h2>Meldinger</h2>
    <ul>
        <?php
        // Simulerte meldinger
        $messages = [
            ["id" => 1, "content" => "Hvordan kan jeg forbedre koden min?"],
            ["id" => 2, "content" => "Kan du forklare mer om sikkerhet?"]
        ];

        foreach ($messages as $message): ?>
            <li>
                <strong>Melding #<?= $message['id'] ?>:</strong> <?= htmlspecialchars($message['content']) ?>
                <button onclick="document.getElementById('reply-form-<?= $message['id'] ?>').style.display='block'">
                    Svar
                </button>
                <!-- Skjema for svar -->
                <div id="reply-form-<?= $message['id'] ?>" class="reply-form">
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
    <div id="change-password-form">
        <form method="POST">
            <input type="hidden" name="change_password" value="1">
            <input type="password" name="old_password" placeholder="Gammelt passord" required>
            <input type="password" name="new_password" placeholder="Nytt passord" required>
            <button type="submit">Endre passord</button>
        </form>
    </div>
</div>
</body>
</html>
