<?php
session_start();
require 'db_connect.php'; // Kobling til databasen

$success = "";
$error = "";

// Behandle skjemaet når det sendes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email = htmlspecialchars($_POST['email']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Sjekk at passordene matcher
    if ($newPassword !== $confirmPassword) {
        $error = "Passordene matcher ikke.";
    } elseif (strlen($newPassword) < 6) {
        $error = "Passordet må være minst 6 tegn.";
    } else {
        // Sjekk om e-posten finnes i databasen
        $check_email = $conn->prepare("SELECT id FROM brukere WHERE e_post = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();

        if ($result->num_rows > 0) {
            // Hash det nye passordet
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Oppdater passordet i databasen
            $update_stmt = $conn->prepare("UPDATE brukere SET passord_hash = ? WHERE e_post = ?");
            $update_stmt->bind_param("ss", $hashedPassword, $email);

            if ($update_stmt->execute()) {
                $success = "Passordet ditt er oppdatert. Du kan nå logge inn med ditt nye passord.";
            } else {
                $error = "Feil ved oppdatering av passordet.";
            }
        } else {
            $error = "E-postadressen er ikke registrert.";
        }
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
    <h2>Tilbakestill passord</h2>
    <form method="post">
        <label for="email">E-post:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="new_password">Nytt passord:</label>
        <input type="password" name="new_password" id="new_password" required><br>

        <label for="confirm_password">Bekreft passord:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br>

        <button type="submit" name="reset_password">Endre passord</button>
    </form>

    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
</body>
</html>
