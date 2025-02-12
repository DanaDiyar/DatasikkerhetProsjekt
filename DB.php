<?php
// DB.php

// Konfigurer dine databaseinnstillinger:
$host     = '158.39.188.205';  // Host, ofte 'localhost'
$dbname   = 'Datasikkerhet';   // Navnet på databasen din
$username = 'datasikkerhet';   // Databasebrukernavn
$password = 'DittPassord';    // Databasepassord

try {
    // Opprett en ny PDO-kobling
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $username, $password);

    // Sett PDO til å kaste exceptions ved feil
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Sett standard fetch mode til associative arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Koblingen er nå klar til bruk
} catch (PDOException $e) {
    // Dersom tilkoblingen mislykkes, stopp skriptet og vis feilmelding
    die("Kunne ikke koble til databasen: " . $e->getMessage());
}
?>
