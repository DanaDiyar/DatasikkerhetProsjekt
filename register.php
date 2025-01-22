<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $subject = sanitize($_POST['subject']);
    $pin_code = sanitize($_POST['pin_code']);
    $profile_image = $_FILES['profile_image'];

    // Hash password
    $password_hash = hashPassword($password);

    // Handle image upload
    $image_path = 'uploads/' . basename($profile_image['name']);
    if (move_uploaded_file($profile_image['tmp_name'], $image_path)) {
        echo "Registrering vellykket (oppdater med database senere).";
    } else {
        echo "Feil ved opplasting av bildet.";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="subject" placeholder="Subject" required>
    <input type="text" name="pin_code" placeholder="PIN Code (4 digits)" required>
    <input type="file" name="profile_image" required>
    <button type="submit">Register</button>
</form>
