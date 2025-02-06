<?php
$host = "158.39.188.205"; // Din MySQL-server IP
$dbname = "Datasikkerhet"; // Ditt databasenavn
$username = "datasikkerhet"; // MySQL-brukernavn
$password = "DittPassord"; // MySQL-passord

// Koble til databasen
$conn = new mysqli($host, $username, $password, $dbname);

// Sjekk om tilkoblingen er vellykket
if ($conn->connect_error) {
    die("Tilkoblingsfeil: " . $conn->connect_error);
}
?>
