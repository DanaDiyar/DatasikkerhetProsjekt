<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    // Simulated database validation
    $fake_password_hash = hashPassword("password123");
    $fake_email = "lecturer@example.com";

    if ($email === $fake_email && verifyPassword($password, $fake_password_hash)) {
        session_start();
        $_SESSION['lecturer_id'] = 1;
        echo "Pålogging vellykket!";
    } else {
        echo "Feil e-post eller passord.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
