<?php
$host = 'localhost'; // Eller IP-adressen til databasen
$dbname = 'foreleser_system';
$username = 'root'; // Sett ditt MySQL-brukernavn
$password = ''; // Sett passordet ditt (hvis det er satt)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Feil ved tilkobling til database: " . $e->getMessage());
}
?>