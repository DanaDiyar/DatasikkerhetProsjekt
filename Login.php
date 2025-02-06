<?php
session_start();
require 'db_connect.php'; // Koble til databasen

$success = "";
$error = "";

// Registrering av bruker
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']); // 'student' eller 'foreleser'
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $study_program = isset($_POST['study_program']) ? htmlspecialchars($_POST['study_program']) : NULL;
    $year = isset($_POST['year']) ? htmlspecialchars($_POST['year']) : NULL;

    // Håndtering av bildeopplasting (valgfritt)
    $imagePath = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadDir = "uploads/";
        $imagePath = $uploadDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Sjekk om e-post allerede finnes
    $check_email = $conn->prepare("SELECT * FROM brukere WHERE e_post = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $error = "E-postadressen er allerede registrert!";
    } else {
        // Sett inn bruker i databasen
        $stmt = $conn->prepare("INSERT INTO brukere (navn, e_post, passord_hash, rolle, bilde, studieretning, studiekull) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $password, $role, $imagePath, $study_program, $year);
        
        if ($stmt->execute()) {
            $success = "Bruker registrert: $name ($email) som $role";
        } else {
            $error = "Feil ved registrering: " . $stmt->error;
        }
    }
}

// Håndtering av innlogging
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Hent bruker fra databasen
    $stmt = $conn->prepare("SELECT * FROM brukere WHERE e_post = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['passord_hash'])) {
        $_SESSION['user_name'] = $user['navn'];
        $_SESSION['role'] = $user['rolle'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Feil e-post eller passord.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studentportal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Registrering</h2>
    <form method="post" enctype="multipart/form-data">
        Navn: <input type="text" name="name" required><br>
        E-post: <input type="email" name="email" required><br>
        Rolle:
        <select name="role" required>
            <option value="student">Student</option>
            <option value="foreleser">Foreleser</option>
        </select><br>
        Studieretning (kun for studenter): <input type="text" name="study_program"><br>
        Studiekull (kun for studenter): <input type="number" name="year"><br>
        Passord: <input type="password" name="password" required><br>
        Profilbilde (valgfritt): <input type="file" name="image" accept="image/*"><br>
        <button type="submit" name="register">Registrer</button>
    </form>

    <h2>Logg inn</h2>
    <form method="post">
        E-post: <input type="email" name="email" required><br>
        Passord: <input type="password" name="password" required><br>
        <button type="submit" name="login">Logg inn</button>
    </form>

    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>

    <h2>Registrerte brukere</h2>
    <?php
    $result = $conn->query("SELECT navn, e_post, rolle FROM brukere");
    echo "<table border='1'><tr><th>Navn</th><th>E-post</th><th>Rolle</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['navn']}</td><td>{$row['e_post']}</td><td>{$row['rolle']}</td></tr>";
    }
    echo "</table>";
    ?>
</body>
</html>
