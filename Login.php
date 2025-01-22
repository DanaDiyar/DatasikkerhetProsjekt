<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal for Studenter og Forelesere</title>
    <link rel="stylesheet" href="style.css"> <!-- Kobling til CSS -->
</head>
<body>
<?php
session_start();

// Dummy database (erstatt med ekte database)
$users = [
    'students' => [],
    'lecturers' => [],
];
$subjects = [];

// Håndtering av registrering
if (isset($_POST['register_student'])) {
    $name = htmlspecialchars($_POST['student_name']);
    $email = htmlspecialchars($_POST['student_email']);
    $study_program = htmlspecialchars($_POST['study_program']);
    $year = htmlspecialchars($_POST['student_year']);
    $password = password_hash($_POST['student_password'], PASSWORD_DEFAULT); // Hash passord

    $_SESSION['users']['students'][$email] = [
        'name' => $name,
        'email' => $email,
        'study_program' => $study_program,
        'year' => $year,
        'password' => $password,
    ];

    $success = "Student registrert: $name ($email)";
}

if (isset($_POST['register_lecturer'])) {
    $name = htmlspecialchars($_POST['lecturer_name']);
    $email = htmlspecialchars($_POST['lecturer_email']);
    $department = htmlspecialchars($_POST['department']);
    $subject = htmlspecialchars($_POST['subject']);
    $pin_code = htmlspecialchars($_POST['pin_code']);
    $password = password_hash($_POST['lecturer_password'], PASSWORD_DEFAULT); // Hash passord
    $image = $_FILES['lecturer_image'];

    $imagePath = 'uploads/' . basename($image['name']);
    if (move_uploaded_file($image['tmp_name'], $imagePath)) {
        $_SESSION['users']['lecturers'][$email] = [
            'name' => $name,
            'email' => $email,
            'department' => $department,
            'subject' => $subject,
            'pin_code' => $pin_code,
            'image' => $imagePath,
            'password' => $password,
        ];

        $success = "Foreleser registrert: $name ($email), Emne: $subject med PIN-kode.";
    } else {
        $error = "Feil ved opplasting av bildet.";
    }
}

// Håndtering av innlogging
if (isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']); // 'student' eller 'lecturer'
    $password = $_POST['password'];

    $user = null;
    if ($role === 'student' && isset($_SESSION['users']['students'][$email])) {
        $user = $_SESSION['users']['students'][$email];
    } elseif ($role === 'lecturer' && isset($_SESSION['users']['lecturers'][$email])) {
        $user = $_SESSION['users']['lecturers'][$email];
    }

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_name'] = $user['name'];
        header("Location: " . ($role === 'student' ? "student_welcome.php" : "lecturer_welcome.php"));
        exit();
    } else {
        $error = "Feil e-post eller passord.";
    }
}

// Håndtering av glemt passord
if (isset($_POST['forgot_password'])) {
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']); // 'student' eller 'lecturer'

    $user = null;
    if ($role === 'student' && isset($_SESSION['users']['students'][$email])) {
        $user = $_SESSION['users']['students'][$email];
    } elseif ($role === 'lecturer' && isset($_SESSION['users']['lecturers'][$email])) {
        $user = $_SESSION['users']['lecturers'][$email];
    }

    if ($user) {
        $newPassword = "newPass" . rand(1000, 9999); // Generer et midlertidig passord
        $_SESSION['users'][$role . 's'][$email]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        $success = "Nytt passord for $email er: $newPassword";
    } else {
        $error = "E-post ikke funnet.";
    }
}
?>
<h2>Registrering for Studenter</h2>
<form method="post">
    Navn: <input type="text" name="student_name" required><br>
    E-post: <input type="email" name="student_email" required><br>
    Studieretning: <input type="text" name="study_program" required><br>
    Studiekull: <input type="number" name="student_year" required><br>
    Passord: <input type="password" name="student_password" required><br>
    <button type="submit" name="register_student">Registrer Student</button>
</form>

<h2>Registrering for Forelesere</h2>
<form method="post" enctype="multipart/form-data">
    Navn: <input type="text" name="lecturer_name" required><br>
    E-post: <input type="email" name="lecturer_email" required><br>
    Institutt: <input type="text" name="department" required><br>
    Emne: <input type="text" name="subject" required><br>
    PIN-kode: <input type="text" name="pin_code" required><br>
    Passord: <input type="password" name="lecturer_password" required><br>
    Bilde: <input type="file" name="lecturer_image" accept="image/*" required><br>
    <button type="submit" name="register_lecturer">Registrer Foreleser</button>
</form>

<h2>Logg inn</h2>
<form method="post">
    E-post: <input type="email" name="email" required><br>
    Passord: <input type="password" name="password" required><br>
    Rolle:
    <select name="role" id="role-select" required>
        <option value="">Velg rolle</option>
        <option value="student">Student</option>
        <option value="lecturer">Foreleser</option>
    </select><br>
    <button type="submit" name="login">Logg inn</button>
</form>

<!-- Glemt passord-knapp -->
<form method="post" id="forgot-password-form" style="display: none;">
    <input type="hidden" name="role" id="hidden-role">
    <input type="hidden" name="email" id="hidden-email">
    <button type="submit" name="forgot_password">Glemt passord</button>
</form>

<script>
    // Dynamisk visning av "Glemt passord"-knappen
    const roleSelect = document.getElementById("role-select");
    const forgotPasswordForm = document.getElementById("forgot-password-form");
    const hiddenRoleInput = document.getElementById("hidden-role");

    roleSelect.addEventListener("change", function () {
        if (roleSelect.value) {
            hiddenRoleInput.value = roleSelect.value;
            forgotPasswordForm.style.display = "block";
        } else {
            forgotPasswordForm.style.display = "none";
        }
    });
</script>
</body>
</html>