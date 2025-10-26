<?php
require('mysql.php');

// Funktion zum Abrufen der Benutzer und ihrer Punkte
function getUsersAndPoints($mysql) {
    $stmt = $mysql->prepare("SELECT email, vorname, nachname, points FROM accounts");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funktion zum Versenden der E-Mails
function sendEmail($email, $vorname, $nachname, $punkte) {
    $betreff = "Deine Punkte für das EM Tippspiel";
    $from = "From: EM Tippspiel <>"; // Your E-mail
    $text = "Text you want to sent the person";

    mail($email, $betreff, $text, $from);
}

// Benutzer und Punkte abrufen
$users = getUsersAndPoints($mysql);

// E-Mails senden
foreach ($users as $user) {
    sendEmail($user['email'], $user['vorname'], $user['nachname'], $user['points']);
}

echo "E-Mails wurden erfolgreich an alle Benutzer gesendet.";
?>
