<?php
session_start();
require 'includes/functions.php';

if (!isset($_SESSION['lecturer_id'])) {
    echo "Du må logge inn for å endre passord.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = sanitize($_POST['old_password']);
    $new_password = sanitize($_POST['new_password']);

    // Simulated password check
    $fake_password_hash = hashPassword("password123");

    if (verifyPassword($old_password, $fake_password_hash)) {
        $new_password_hash = hashPassword($new_password);
        echo "Passord endret (Simulert uten database).";
    } else {
        echo "Feil gammelt passord.";
    }
}
?>

<form method="POST">
    <input type="password" name="old_password" placeholder="Old Password" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Change Password</button>
</form>
