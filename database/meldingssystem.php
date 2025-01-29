<?php
$host = "localhost";
$dbname = "meldingssystem";
$username = "root";
$password = "Gressvik03";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>