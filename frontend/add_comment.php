<?php
include 'db_connection.php';

if (isset($_POST['send_comment'])) {
    $melding_id = $_POST['melding_id'];
    $comment_text = htmlspecialchars($_POST['comment_text']);

    $stmt = $conn->prepare("INSERT INTO kommentarer (melding_id, innhold) VALUES (?, ?)");
    $stmt->bind_param("is", $melding_id, $comment_text);

    if ($stmt->execute()) {
        header("Location: dashboard.php?comment_success=1");
        exit();
    } else {
        echo "Feil ved lagring av kommentar: " . $conn->error;
    }

    $stmt->close();
}
?>
