<?php
session_start();

// Dummy database (erstatt med ekte database)
$users = [
    'student1' => 'password123', // Brukernavn => Passord
    'lecturer1' => 'securepass',
];

// Hent innsendte data fra skjema
$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);

// Valider brukernavn og passord
if (isset($users[$username]) && $users[$username] === $password) {
    // Opprett økt for brukeren
    $_SESSION['logged_in_user'] = $username;
    echo "<h2>Login Successful</h2>";
    echo "Welcome, " . htmlspecialchars($username) . "!";
    echo "<br><a href='welcome.php'>Go to Dashboard</a>";
} else {
    // Feilmelding ved feil brukernavn eller passord
    echo "<h2>Login Failed</h2>";
    echo "Incorrect username or password.";
    echo "<br><a href='Login.php'>Go back to Login Page</a>";
}
?>
