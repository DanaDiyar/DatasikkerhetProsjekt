<?php
// Funksjoner direkte i filen
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Håndtering av registrering
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = sanitize($_POST['password']);
    $subject = sanitize($_POST['subject']);
    $pin_code = sanitize($_POST['pin_code']);
    $profile_image = $_FILES['profile_image'];

    if (!$email) {
        die("Ugyldig e-postadresse.");
    }

    // Hash passord
    $password_hash = hashPassword($password);

    // Sikker bildeopplasting
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($profile_image['type'], $allowed_types)) {
        die("Kun JPEG, PNG og GIF-bilder er tillatt.");
    }

    $image_path = 'uploads/' . uniqid() . '_' . basename($profile_image['name']);
    if (move_uploaded_file($profile_image['tmp_name'], $image_path)) {
        echo "Registrering vellykket! (Oppdater med database senere)";
    } else {
        die("Feil ved opplasting av bildet.");
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
