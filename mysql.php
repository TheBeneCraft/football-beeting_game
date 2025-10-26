<?php
// Here you need to insert your database account
$host = 'localhost';
$dbname = 'em_tippspiel';
$username = 'root';
$password = '';

try {
    $mysql = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage();
    exit();
}
?>
