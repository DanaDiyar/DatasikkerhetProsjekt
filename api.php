<?php
include 'database.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'getMessages') {
            $sql = "SELECT * FROM messages";
            $result = $conn->query($sql);
            $messages = [];
            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }
            echo json_encode($messages);
        } elseif ($_GET['action'] === 'reportMessage' && isset($_GET['messageId'])) {
            $messageId = intval($_GET['messageId']);
            $sql = "UPDATE messages SET reported = 1 WHERE id = $messageId";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['status' => 'Reported']);
            } else {
                echo json_encode(['error' => 'Kunne ikke rapportere melding']);
            }
        } else {
            echo json_encode(['error' => 'Ugyldig handling']);
        }
    } else {
        echo json_encode(['error' => 'Ingen handling spesifisert']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'changePassword') {
        $username = $_POST['username'];
        $oldPassword = $_POST['oldPassword'];
        $newPassword = $_POST['newPassword'];

        $sql = "SELECT * FROM teacher WHERE username = ? AND password = MD5(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $oldPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $updateSql = "UPDATE teacher SET password = MD5(?) WHERE username = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $newPassword, $username);
            $updateStmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Passordet er endret.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Feil brukernavn eller passord.']);
        }
    } else {
        echo json_encode(['error' => 'Ugyldig handling.']);
    }
} else {
    echo json_encode(['error' => 'Ugyldig forespørsel.']);
}

$conn->close();
