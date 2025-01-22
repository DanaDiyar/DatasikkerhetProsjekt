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

    $_SESSION['users']['students'][$email] = [
        'name' => $name,
        'email' => $email,
        'study_program' => $study_program,
        'year' => $year,
        'password' => 'student123', // Standard passord for demo
    ];

    $success = "Student registrert: $name ($email)";
}

if (isset($_POST['register_lecturer'])) {
    $name = htmlspecialchars($_POST['lecturer_name']);
    $email = htmlspecialchars($_POST['lecturer_email']);
    $department = htmlspecialchars($_POST['department']);
    $subject = htmlspecialchars($_POST['subject']);
    $pin_code = htmlspecialchars($_POST['pin_code']);
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
            'password' => 'lecturer123', // Standard passord for demo
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

    if ($role === 'student' && isset($_SESSION['users']['students'][$email])) {
        $_SESSION['user_name'] = $_SESSION['users']['students'][$email]['name'];
        header("Location: student_welcome.php");
        exit();
    } elseif ($role === 'lecturer' && isset($_SESSION['users']['lecturers'][$email])) {
        $_SESSION['user_name'] = $_SESSION['users']['lecturers'][$email]['name'];
        header("Location: lecturer_welcome.php");
        exit();
    } else {
        $error = "E-post ikke funnet. Vennligst registrer deg først.";
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
        $_SESSION['users'][$role . 's'][$email]['password'] = $newPassword;
        $success = "Nytt passord for $email er: $newPassword";
    } else {
        $error = "E-post ikke funnet.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal for Studenter og Forelesere</title>
</head>
<body>
    <h1>Velkommen til portalen</h1>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <h2>Registrering for Studenter</h2>
    <form method="post">
        Navn: <input type="text" name="student_name" required><br>
        E-post: <input type="email" name="student_email" required><br>
        Studieretning: <input type="text" name="study_program" required><br>
        Studiekull: <input type="number" name="student_year" required><br>
        <button type="submit" name="register_student">Registrer Student</button>
    </form>

    <h2>Registrering for Forelesere</h2>
    <form method="post" enctype="multipart/form-data">
        Navn: <input type="text" name="lecturer_name" required><br>
        E-post: <input type="email" name="lecturer_email" required><br>
        Institutt: <input type="text" name="department" required><br>
        Emne: <input type="text" name="subject" required><br>
        PIN-kode: <input type="text" name="pin_code" required><br>
        Bilde: <input type="file" name="lecturer_image" accept="image/*" required><br>
        <button type="submit" name="register_lecturer">Registrer Foreleser</button>
    </form>

    <h2>Logg inn</h2>
    <form method="post">
        E-post: <input type="email" name="email" required><br>
        Rolle:
        <select name="role" required>
            <option value="student">Student</option>
            <option value="lecturer">Foreleser</option>
        </select><br>
        <button type="submit" name="login">Logg inn</button>
    </form>

    <h2>Glemt Passord</h2>
    <form method="post">
        E-post: <input type="email" name="email" required><br>
        Rolle:
        <select name="role" required>
            <option value="student">Student</option>
            <option value="lecturer">Foreleser</option>
        </select><br>
        <button type="submit" name="forgot_password">Glemt Passord</button>
    </form>
</body>
</html>
