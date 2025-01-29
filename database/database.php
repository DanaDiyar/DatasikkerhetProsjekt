<?php
$host = '127.0.0.1'; // Endret fra 'localhost' til '127.0.0.1'
$dbname = 'foreleser_system';
$username = 'root'; // Sett ditt MySQL-brukernavn
$password = ''; // Sett ditt MySQL-passord

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Feil ved tilkobling til database: " . $e->getMessage());
}
?>
