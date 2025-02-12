<?php
session_start();
require 'db_connect.php'; // Kobling til databasen

$success = "";
$error = "";

// 📌 ***REGISTRERING AV BRUKER***
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $study_program = ($role === "student" && !empty($_POST['study_program'])) ? htmlspecialchars($_POST['study_program']) : NULL;
    $year = ($role === "student" && !empty($_POST['year'])) ? $_POST['year'] : NULL;

    $emnekode = ($role === "foreleser" && !empty($_POST['emnekode'])) ? htmlspecialchars($_POST['emnekode']) : NULL;
    $emnenavn = ($role === "foreleser" && !empty($_POST['emnenavn'])) ? htmlspecialchars($_POST['emnenavn']) : NULL;
    $pin_kode = ($role === "foreleser" && !empty($_POST['pin_kode'])) ? htmlspecialchars($_POST['pin_kode']) : NULL;

    // 📌 **Bildelagring på serveren (Ikke i databasen)**
    $imagePath = NULL;
    if ($role === "foreleser" && isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Sørger for at mappen finnes
        }

        $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $error = "Feil ved opplasting av bildet.";
            error_log("move_uploaded_file() feilet! Sjekk rettigheter for uploads/. TMP: " . $_FILES["image"]["tmp_name"]);
            $imagePath = NULL;
        }
    }

    // Sjekk om e-posten allerede finnes i databasen
    $check_email = $conn->prepare("SELECT id FROM brukere WHERE e_post = ?");
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
            $foreleser_id = $stmt->insert_id;

            // Hvis foreleser, opprett emne automatisk
            if ($role === "foreleser" && $emnekode && $emnenavn && $pin_kode) {
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

// 📌 ***INNLOGGING AV BRUKER***
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Sjekk om e-posten finnes i databasen
    $stmt = $conn->prepare("SELECT id, navn, e_post, passord_hash, rolle, bilde FROM brukere WHERE e_post = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifiser passord
        if (password_verify($password, $user['passord_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['navn'];
            $_SESSION['user_email'] = $user['e_post'];
            $_SESSION['user_role'] = $user['rolle'];
            $_SESSION['user_image'] = $user['bilde']; // Lagre bildesti i session

            // Omdiriger basert på rolle
            if ($user['rolle'] === "foreleser") {
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: student.php");
                exit();
            }
        } else {
            $error = "Feil passord. Prøv igjen.";
        }
    } else {
        $error = "E-postadressen finnes ikke.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logg inn / Registrering</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Registrering</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Navn" required><br>
        <input type="email" name="email" placeholder="E-post" required><br>
        <select name="role" id="role" required>
            <option value="">Velg rolle</option>
            <option value="student">Student</option>
            <option value="foreleser">Foreleser</option>
        </select><br>

        <div id="lecturer-fields">
            <input type="file" name="image" accept="image/*"><br>
            <input type="text" name="emnekode" placeholder="Emnekode"><br>
            <input type="text" name="emnenavn" placeholder="Emnenavn"><br>
            <input type="text" name="pin_kode" placeholder="PIN-kode (4 sifre)" pattern="\d{4}"><br>
        </div>

        <input type="password" name="password" placeholder="Passord" required><br>
        <button type="submit" name="register">Registrer</button>
    </form>

    <h2>Logg inn</h2>
    <form method="post">
        <input type="email" name="email" placeholder="E-post" required><br>
        <input type="password" name="password" placeholder="Passord" required><br>
        <button type="submit" name="login">Logg inn</button>
    </form>
    <p><a href="glemt_passord.php">Glemt passord?</a></p>
    <p><a href="index.php" class="btn">Gjestebruker</a></p>

    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
</body>
</html>
