<?php
session_start();
require 'DB.php'; // $conn er MySQLi-tilkoblingen

$error = "";

// Hvis brukeren allerede er innlogget, send videre
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Håndter innlogging
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ Sjekk at begge feltene er fylt ut
    if (empty($email) || empty($password)) {
        $error = "Alle felt må fylles ut.";
    } else {
        // ✅ Bruk prepared statement for å beskytte mot SQL injection
        $stmt = $conn->prepare("SELECT id, navn, rolle, passord_hash FROM brukere WHERE e_post = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['passord_hash'])) {
            // ✅ Lagre brukerdata i session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['navn'];
            $_SESSION['user_role'] = $user['rolle'];

            // 🚀 Send til dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Feil e-post eller passord.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Logg inn</title>
    <link rel="stylesheet" href="style_index.css">
</head>
<body>
    <h1>Logg inn</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="post">
        <label for="email">E-post:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="password">Passord:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" class="btn" value="Logg inn">
    </form>

    <p><a href="index.php">Tilbake til gjesteside</a></p>
</body>
</html>
