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
