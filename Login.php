<?php
session_start();
require 'db_connect.php'; // Kobling til databasen

$success = "";
$error = "";

// Behandle skjemaet når det sendes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']); // 'student' eller 'foreleser'
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $study_program = ($role === "student" && isset($_POST['study_program']) && !empty($_POST['study_program'])) ? htmlspecialchars($_POST['study_program']) : NULL;
    $year = ($role === "student" && isset($_POST['year']) && !empty($_POST['year'])) ? $_POST['year'] : NULL;


    // Håndtering av bildeopplasting for forelesere
    $imagePath = NULL; // Standardverdi hvis ingen bilde lastes opp

// Hvis brukeren er en foreleser og laster opp et bilde
if ($role === "foreleser" && isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $uploadDir = "uploads/";
    $imagePath = $uploadDir . basename($_FILES["image"]["name"]);

    // Flytt bildet til serveren
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        $error = "Feil ved opplasting av bildet.";
        error_log("Feil ved opplasting av bildet: " . $_FILES["image"]["error"]);
        $imagePath = NULL; // Sett bilde til NULL hvis opplastningen feiler
    }
}

// Sjekk om e-posten allerede finnes i databasen
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
        error_log("Feil ved registrering: " . $stmt->error);
    }
}

}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrering</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hidden {
            display: none;
        }
    </style>
    <script>
        function toggleFields() {
            const role = document.getElementById("role").value;
            const studentFields = document.getElementById("student-fields");
            const lecturerFields = document.getElementById("lecturer-fields");

            if (role === "student") {
                studentFields.style.display = "block";
                lecturerFields.style.display = "none";
            } else if (role === "foreleser") {
                studentFields.style.display = "none";
                lecturerFields.style.display = "block";
            } else {
                studentFields.style.display = "none";
                lecturerFields.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <h2>Registrering</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Navn:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="email">E-post:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="role">Rolle:</label>
        <select name="role" id="role" onchange="toggleFields()" required>
            <option value="">Velg rolle</option>
            <option value="student">Student</option>
            <option value="foreleser">Foreleser</option>
        </select><br>

        <!-- Felter for studenter -->
        <div id="student-fields" class="hidden">
            <label for="study_program">Studieretning:</label>
            <input type="text" name="study_program" id="study_program"><br>

            <label for="year">Studiekull:</label>
            <input type="number" name="year" id="year"><br>
        </div>

        <!-- Felter for forelesere -->
        <div id="lecturer-fields" class="hidden">
            <label for="image">Profilbilde:</label>
            <input type="file" name="image" id="image" accept="image/*"><br>
        </div>

        <label for="password">Passord:</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit" name="register">Registrer</button>
    </form>

    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
</body>
</html>
