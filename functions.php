<?php
// Rydder input for å unngå XSS (Cross-Site Scripting) og andre sikkerhetsproblemer
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Hasher passord med BCRYPT
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verifiserer passord mot lagret hash
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Enkel redirect-funksjon
function redirect($url) {
    header("Location: $url");
    exit();
}
?>
