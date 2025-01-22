<?php
session_start();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testside</title>
</head>
<body>
    <h1>Velkommen til Studentportalen</h1>
    <form method="post">
        Navn: <input type="text" name="name" required><br>
        E-post: <input type="email" name="email" required><br>
        <input type="submit" name="register" value="Registrer">
    </form>

    <?php
    if (isset($_POST['register'])) {
        echo "<p>Registrering vellykket for: " . htmlspecialchars($_POST['name']) . " med e-post: " . htmlspecialchars($_POST['email']) . "</p>";
    }
    ?>
</body>
</html>
