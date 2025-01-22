<?php
session_start();
require 'db.php';  // Sørg for at denne filen inneholder riktig databaseforbindelse

$errors = [];
$success = "";

// Registrering av student
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $study_program = $_POST['study_program'];
    $year = $_POST['year'];

    $stmt = $pdo->prepare("INSERT INTO students (name, email, study_program, year) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $study_program, $year])) {
        $success = "Registrering vellykket!";
    } else {
        $errors[] = "Feil ved registrering.";
    }
}

// Innlogging
if (isset($_POST['login'])) {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $_SESSION['student_name'] = $student['name'];
        header("Location: welcome.php");
        exit();
    } else {
        $errors[] = "E-post ikke funnet.";
    }
}

// Anonym melding
if (isset($_POST['send_message'])) {
    $subject_id = $_POST['subject_id'];
    $message_text = $_POST['message_text'];

    $stmt = $pdo->prepare("INSERT INTO messages (subject_id, message_text) VALUES (?, ?)");
    if ($stmt->execute([$subject_id, $message_text])) {
        $success = "Meldingen er sendt anonymt.";
    } else {
        $errors[] = "Feil ved sending av melding.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studentportal</title>
</head>
<body>
    <h1>Studentportal</h1>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Registrering</h2>
    <form method="post">
        Navn: <input type="text" name="name" required><br>
        E-post: <input type="email" name="email" required><br>
        Studieretning: <input type="text" name="study_program" required><br>
        Studiekull: <input type="number" name="year" required><br>
        <input type="submit" name="register" value="Registrer">
    </form>

    <h2>Logg inn</h2>
    <form method="post">
        E-post: <input type="email" name="email" required><br>
        <input type="submit" name="login" value="Logg inn">
    </form>

    <h2>Send anonym melding</h2>
    <form method="post">
        Velg emne:
        <select name="subject_id">
            <?php
            $stmt = $pdo->query("SELECT id, subject_name FROM subjects");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['subject_name']}</option>";
            }
            ?>
        </select><br>
        Melding: <textarea name="message_text" required></textarea><br>
        <input type="submit" name="send_message" value="Send melding">
    </form>
</body>
</html>
