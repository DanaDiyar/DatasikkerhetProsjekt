<?php
$host = '158.39.188.205';
$dbname = 'Datasikkerhet';
$username = 'datasikkerhet';
$password = 'DittPassord';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
