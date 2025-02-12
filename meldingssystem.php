<?php
$host = '158.39.188.205'; // Eller IP-adressen til databasen
$dbname = 'Datasikkerhet';
$username = 'datasikkerhet'; // Sett ditt MySQL-brukernavn
$password = 'DittPassord'; // Sett passordet ditt (hvis det er satt)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Feil ved tilkobling til database: " . $e->getMessage());
}
?>