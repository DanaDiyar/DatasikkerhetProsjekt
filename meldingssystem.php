<?php
$host = '158.39.188.205';
$dbname = 'Datasikkerhet';
$username = 'datasikkerhet';
$password = 'DittPassord';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Tilkobling vellykket!";
} catch (PDOException $e) {
    die("Feil ved tilkobling til database: " . $e->getMessage());
}
?>
