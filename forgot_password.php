<?php
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);

    // Simulated email check
    $fake_email = "lecturer@example.com";

    if ($email === $fake_email) {
        echo "Instruksjoner for tilbakestilling sendt til $email (Simulert).";
    } else {
        echo "E-post ikke funnet.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Reset Password</button>
</form>
