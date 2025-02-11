<?php
require 'db.php';

$messages = [];
$subjectInfo = null;

/*
  Håndtering av kommentarinnsending.
  Kommentarene skal settes inn i tabellen "kommentarer" (kolonnene: melding_id, innhold, dato_opprettet)
*/
if (isset($_POST['comment_text']) && isset($_POST['message_id'])) {
    $commentText = $_POST['comment_text'];
    $messageId   = $_POST['message_id'];

    $stmtComm = $pdo->prepare("INSERT INTO kommentarer (melding_id, innhold) VALUES (?, ?)");
    $stmtComm->execute([$messageId, $commentText]);
    echo "Kommentar er lagt til!";
}

/*
  Håndtering av innlogging for emne. Brukeren oppgir emnekode og PIN.
  Tabellen "emner" har kolonnene: id, emnekode, emnenavn, foreleser_id, pin_kode.
*/
if (isset($_POST['subject_code']) && isset($_POST['pin_code'])) {
    $subjectCodeInput = $_POST['subject_code'];
    $pinCodeInput     = $_POST['pin_code'];

    // Finn emnet ut fra emnekode
    $stmt = $pdo->prepare("SELECT * FROM emner WHERE emnekode = ?");
    $stmt->execute([$subjectCodeInput]);
    $subject = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subject) {
        // Sjekk PIN
        if ($subject['pin_kode'] == $pinCodeInput) {
            // Hent meldinger for dette emnet.
            // Tabellen "meldinger" har kolonnene: id, emne_id, student_id, innhold, dato_opprettet.
            // Tabellen "svar" (forforelesers svar) har: melding_id, innhold, dato_opprettet.
            // Tabellen "brukere" (for studenter) har: navn (alias student_navn).
            $subjectInfo = $subject;

            $stmtMsg = $pdo->prepare("SELECT m.*, u.navn as student_navn, s.innhold as svar_innhold, s.dato_opprettet as svar_dato 
                                      FROM meldinger m
                                      LEFT JOIN svar s ON m.id = s.melding_id
                                      LEFT JOIN brukere u ON m.student_id = u.id
                                      WHERE m.emne_id = ?
                                      ORDER BY m.dato_opprettet DESC");
            $stmtMsg->execute([$subject['id']]);
            $messages = $stmtMsg->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "Feil PIN-kode!";
        }
    } else {
        echo "Fant ikke emnekode!";
    }
}

/*
  Håndtering av rapportering av en melding.
  I stedet for å sette en "reported"-kolonne i meldinger, settes her en post inn i tabellen "rapporterte_meldinger".
  Denne tabellen har kolonnene: id, melding_id, grunn, dato_rapportert.
  Vi setter en standard grunn, f.eks. "Rapportert via gjesteside".
*/
if (isset($_GET['report'])) {
    $messageId = $_GET['report'];
    $stmtRep = $pdo->prepare("INSERT INTO rapporterte_meldinger (melding_id, grunn) VALUES (?, ?)");
    $stmtRep->execute([$messageId, 'Rapportert via gjesteside']);
    echo "Meldingen er rapportert!";
}
?>