<?php
session_start();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velkommen</title>
</head>
<body>
    <h1>Velkommen!</h1>
    
    <h2>Send en anonym melding</h2>
    <form action="send_message.php" method="post">
        <label for="subject">Velg emne:</label>
        <select name="subject" id="subject">
            <option value="">--Velg et emne--</option>
            <option value="Matematikk">Matematikk</option>
            <option value="Informatikk">Informatikk</option>
            <option value="Historie">Historie</option>
            <option value="Fysikk">Fysikk</option>
        </select>
        <br><br>
        <label for="message">Melding:</label>
        <textarea name="message" id="message" rows="4" cols="50"></textarea>
        <br><br>
        <button type="submit">Send anonym melding</button>
    </form>
    
    <h2>Sendte meldinger</h2>
    <div id="comments">
        <p>Ingen meldinger sendt ennå.</p>
    </div>
</body>
</html>
