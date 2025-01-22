<?php
session_start();

// Dummy database for testing (erstatt med ekte database)
$students = [];

// Håndtering av registrering
if (isset($_POST['register'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $study_program = htmlspecialchars($_POST['study_program']);
    $year = htmlspecialchars($_POST['year']);

    // Lagre studentinfo i en sessionsbasert "database"
    $_SESSION['students'][$email] = [
        'name' => $name,
        'email' => $email,
        'study_program' => $study_program,
        'year' => $year
    ];

    $success = "Registrering vellykket for: $name med e-post: $email";
}

// Håndtering av innlogging
if (isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);

    if (isset($_SESSION['students'][$email])) {
        $_SESSION['student_name'] = $_SESSION['students'][$email]['name'];
        header("Location: welcome.php");
        exit();
    } else {
        $error = "E-post ikke funnet. Vennligst registrer deg først.";
    }
}
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

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <h2>Registrering</h2>
    <form method="post">
        Navn: <input type="text" name="name" required><br>
        E-post: <input type="email" name="email" required><br>
        Studieretning: <input type="text" name="study_program" required><br>
        Studiekull: <input type="number" name="year" required><br>
        <input type="submit" name="register" value="Registrer">
    </form>

    <h2>Logg inn</h2>
    <form method="post">
        E-post: <input type="email" name="email" required><br>
        <input type="submit" name="login" value="Logg inn">
    </form>
</body>
</html>
