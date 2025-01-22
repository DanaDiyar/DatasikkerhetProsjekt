<?php
session_start();
if (!isset($_SESSION['student_name'])) {
    header("Location: student.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Velkommen</title>
</head>
<body>
    <h1>Velkommen, <?= htmlspecialchars($_SESSION['student_name']) ?>!</h1>
    <a href="logout.php">Logg ut</a>
</body>
</html>
