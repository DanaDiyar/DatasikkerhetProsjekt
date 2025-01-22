<?php
session_start();

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

if (!isset($_SESSION['lecturer_id'])) {
    die("Du må logge inn for å endre passord.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = sanitize($_POST['old_password']);
    $new_password = sanitize($_POST['new_password']);

    $fake_password_hash = hashPassword("password123");

    if (verifyPassword($old_password, $fake_password_hash)) {
        $new_password_hash = hashPassword($new_password);
        echo "Passord endret! (Simulert uten database)";
    } else {
        echo "Gammelt passord er feil.";
    }
}
?>

<form method="POST">
    <input type="password" name="old_password" placeholder="Old Password" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Change Password</button>
</form>
