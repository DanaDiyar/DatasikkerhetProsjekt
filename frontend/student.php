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
        Studieretning: <input type="text" name="study_program" required><br>
        Studiekull: <input type="number" name="year" required><br>
        <input type="submit" name="register" value="Registrer">
    </form>

    <?php
    if (isset($_POST['register'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $study_program = htmlspecialchars($_POST['study_program']);
        $year = htmlspecialchars($_POST['year']);

        echo "<p>Registrering vellykket for: $name</p>";
        echo "<p>E-post: $email</p>";
        echo "<p>Studieretning: $study_program</p>";
        echo "<p>Studiekull: $year</p>";
    }
    ?>
</body>
</html>
