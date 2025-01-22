<?php
session_start();
require 'db.php';  // Sørg for at db.php inneholder riktig tilkobling til databasen

$errors = [];
$success = "";

// Registrering av bruker (navn og e-post)
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
    if ($stmt->execute([$name, $email])) {
        $success = "Registrering vellykket!";
    } else {
        $errors[] = "Feil ved registrering.";
    }
}

// Innlogging med e-post
if (isset($_POST['login'])) {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $_SESSION['student_name'] = $student['name'];
        header("Location: welcome.php");
        exit();
    } else {
        $errors[] = "E-post ikke funnet.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enkel Studentportal</title>
</head>
<body>
    <h1>Enkel Studentportal
