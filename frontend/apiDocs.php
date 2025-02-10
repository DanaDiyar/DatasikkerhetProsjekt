<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>API Dokumentasjon</title>
</head>
<body>
    <h1>API Dokumentasjon</h1>

    <h2>Hente alle emner</h2>
    <p><strong>URL:</strong> GET /api.php</p>
    <p><strong>Beskrivelse:</strong> Returnerer en liste over tilgjengelige emner.</p>

    <h2>Send anonym melding</h2>
    <p><strong>URL:</strong> POST /api.php</p>
    <p><strong>Beskrivelse:</strong> Sender en anonym melding til et valgt emne.</p>
    <p><strong>Body (JSON):</strong></p>
    <pre>{
    "subject_id": 1,
    "message_text": "Dette er en anonym melding"
}</pre>

    <h2>Slette alle meldinger</h2>
    <p><strong>URL:</strong> DELETE /api.php</p>
    <p><strong>Beskrivelse:</strong> Sletter alle meldinger i systemet.</p>

</body>
</html>
