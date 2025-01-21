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
} else {
    echo json_encode(['error' => 'Ugyldig forespørsel']);
}

$conn->close();
?>
