<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);

    // Simulert e-postsjekk
    $fake_email = "lecturer@example.com";

    if ($email === $fake_email) {
        echo "Instruksjoner for tilbakestilling sendt til $email.";
    } else {
        echo "E-post ikke funnet.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Reset Password</button>
</form>
