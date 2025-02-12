<?php
// DB.php

// Konfigurer dine databaseinnstillinger:
$host     = '158.39.188.205';  // Host, ofte 'localhost'
$dbname   = 'Datasikkerhet';   // Navnet på databasen din
$username = 'datasikkerhet';   // Databasebrukernavn
$password = 'DittPassord';    // Databasepassord

// Koble til databasen
$conn = new mysqli($host, $username, $password, $dbname);

// Sjekk om tilkoblingen er vellykket
if ($conn->connect_error) {
    die("Tilkoblingsfeil: " . $conn->connect_error);
}
?>
