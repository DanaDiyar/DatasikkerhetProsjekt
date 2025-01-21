<?php
// Enkel håndtering av input (bare for testing, ikke for produksjon)
$username = $_POST['username'];
$password = $_POST['password'];

// Bare for å vise innsendte data (ingen backend-sjekk her)
echo "<h2>Submitted Data</h2>";
echo "Username: " . htmlspecialchars($username) . "<br>";
echo "Password: " . htmlspecialchars($password) . "<br>";
?>
