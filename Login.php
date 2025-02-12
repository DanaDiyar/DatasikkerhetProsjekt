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

    // Bare studenter får studieretning og studiekull
    $study_program = ($role === "student" && isset($_POST['study_program']) && !empty($_POST['study_program'])) ? htmlspecialchars($_POST['study_program']) : NULL;
    $year = ($role === "student" && isset($_POST['year']) && !empty($_POST['year'])) ? $_POST['year'] : NULL;

    // Bare forelesere får emneinfo
    $emnekode = ($role === "foreleser" && isset($_POST['emnekode']) && !empty($_POST['emnekode'])) ? htmlspecialchars($_POST['emnekode']) : NULL;
    $emnenavn = ($role === "foreleser" && isset($_POST['emnenavn']) && !empty($_POST['emnenavn'])) ? htmlspecialchars($_POST['emnenavn']) : NULL;
    $pin_kode = ($role === "foreleser" && isset($_POST['pin_kode']) && !empty($_POST['pin_kode'])) ? htmlspecialchars($_POST['pin_kode']) : NULL;

    // Håndtering av bildeopplasting for forelesere
    $imagePath = NULL;
    if ($role === "foreleser" && isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadDir = "uploads/";
        $imagePath = $uploadDir . basename($_FILES["image"]["name"]);

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $error = "Feil ved opplasting av bildet.";
            error_log("Feil ved opplasting av bildet: " . $_FILES["image"]["error"]);
            $imagePath = NULL;
        }
    }// Behandle innlogging
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Sjekk om e-posten finnes
    $stmt = $conn->prepare("SELECT id, navn, e_post, passord_hash, rolle FROM brukere WHERE e_post = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifiser passord
        if (password_verify($password, $user['passord_hash'])) {
            // Lagre brukerdata i sesjonen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['navn'];
            $_SESSION['user_email'] = $user['e_post'];
            $_SESSION['user_role'] = $user['rolle'];

            // Omdiriger basert på rolle
            if ($user['rolle'] === "foreleser") {
                header("Location: foreleser_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            $error = "Feil passord. Prøv igjen.";
        }
    } else {
        $error = "E-postadressen finnes ikke.";
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
        // Sett inn foreleser eller student i brukere-tabellen
        $stmt = $conn->prepare("INSERT INTO brukere (navn, e_post, passord_hash, rolle, bilde, studieretning, studiekull) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $password, $role, $imagePath, $study_program, $year);

        if ($stmt->execute()) {
            $foreleser_id = $stmt->insert_id; // Hent ID-en til den nye foreleseren

            if ($role === "foreleser" && $emnekode && $emnenavn && $pin_kode) {
                // Opprett et nytt emne i databasen
                $stmt_course = $conn->prepare("INSERT INTO emner (emnekode, emnenavn, foreleser_id, pin_kode) VALUES (?, ?, ?, ?)");
                $stmt_course->bind_param("ssis", $emnekode, $emnenavn, $foreleser_id, $pin_kode);

                if ($stmt_course->execute()) {
                    $success = "Foreleser registrert og emne opprettet: $emnekode - $emnenavn";
                } else {
                    $error = "Feil ved opprettelse av emne: " . $stmt_course->error;
                }
            } else {
                $success = "Bruker registrert: $name ($email) som $role";
            }
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
        <!-- Felter for forelesere -->
<div id="lecturer-fields" class="hidden">
    <label for="image">Profilbilde:</label>
    <input type="file" name="image" id="image" accept="image/*"><br>

    <label for="emnekode">Emnekode:</label>
    <input type="text" name="emnekode" id="emnekode"><br>

    <label for="emnenavn">Emnenavn:</label>
    <input type="text" name="emnenavn" id="emnenavn"><br>

    <label for="pin_kode">PIN-kode (4 sifre):</label>
    <input type="text" name="pin_kode" id="pin_kode" pattern="\d{4}" title="Må være 4 sifre"><br>
</div>


        <label for="password">Passord:</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit" name="register">Registrer</button>
    </form>

    <h2>Logg inn</h2>
    <form method="post">
        <label for="email">E-post:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="password">Passord:</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit" name="login">Logg inn</button>
    </form>

    <p><a href="glemt_passord.php">Glemt passord?</a></p>


    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
</body>
</html>
