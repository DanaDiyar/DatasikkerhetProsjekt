<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'getMessages') {
        // Hent meldinger fra databasen
    } elseif ($_GET['action'] === 'reportMessage') {
        // Rapportér melding
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'changePassword') {
        // Endre passord
    }
} else {
    echo json_encode(['error' => 'Ugyldig metode']);
}
?>
