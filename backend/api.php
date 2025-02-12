<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");

// Dummy data for emner
$subjects = [
    1 => "Emne 1",
    2 => "Emne 2",
    3 => "Emne 3",
    4 => "Emne 4"
];

// Håndtering av API-forespørsler
$method = $_SERVER['REQUEST_METHOD'];

if ($method === "GET") {
    echo json_encode(["subjects" => $subjects]);
    exit;
} elseif ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['subject_id']) || !isset($input['message_text'])) {
        http_response_code(400);
        echo json_encode(["error" => "Manglende data"]);
        exit;
    }

    $subject_id = $input['subject_id'];
    $message_text = htmlspecialchars($input['message_text']);

    if (!isset($subjects[$subject_id])) {
        http_response_code(400);
        echo json_encode(["error" => "Ugyldig emne"]);
        exit;
    }

    $_SESSION['messages'][] = [
        'subject' => $subjects[$subject_id],
        'message' => $message_text
    ];

    echo json_encode(["success" => "Melding lagret anonymt"]);
    exit;
} elseif ($method === "DELETE") {
    $_SESSION['messages'] = [];
    echo json_encode(["success" => "Alle meldinger er slettet"]);
    exit;
} else {
    http_response_code(405);
    echo json_encode(["error" => "Ugyldig HTTP-metode"]);
}
?>
