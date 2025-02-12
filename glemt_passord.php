<?php
session_start();
require 'db_connect.php'; // Kobling til databasen

$success = "";
$error = "";

// Behandle skjemaet når det sendes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email = htmlspecialchars($_POST['email']);

    // Sjekk om e-posten finnes i databasen
    $check_email = $conn->prepare("SELECT id FROM brukere WHERE e_post = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        // Generer nytt midlertidig passord
        $newPassword = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Oppdater passordet i databasen
        $update_stmt = $conn->prepare("UPDATE brukere SET passord_hash = ? WHERE e_post = ?");
        $update_stmt->bind_param("ss", $hashedPassword, $email);

        if ($update_stmt->execute()) {
            $success = "Ditt nye passord er: <strong>$newPassword</strong>. Logg inn og bytt det umiddelbart!";
        } else {
            $error = "Feil ved oppdatering av passordet.";
        }
    } else {
        $error = "E-postadressen er ikke registrert.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glemt passord</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Glemt passord</h2>
    <form method="post">
        <label for="email">E-post:</label>
        <input type="email" name="email" id="email" required><br>

        <button type="submit" name="reset_password">Tilbakestill passord</button>
    </form>

    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
</body>
</html>
