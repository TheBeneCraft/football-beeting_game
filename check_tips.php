<?php
require('mysql.php');

// Funktion zur Berechnung der Punkte für einen Tipp
function calculatePoints($match_id, $tip_team1, $tip_team2) {
    global $mysql;
    $stmt = $mysql->prepare("SELECT result_team1, result_team2 FROM results WHERE match_id = :match_id");
    $stmt->bindParam(':match_id', $match_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $points = 0;

    if ($result) {
        $actual_team1 = $result['result_team1'];
        $actual_team2 = $result['result_team2'];

        // Punkteberechnung
        if ($tip_team1 == $actual_team1 && $tip_team2 == $actual_team2) {
            // Genaues Ergebnis
            $points = 3;
        } elseif (($tip_team1 - $tip_team2) == ($actual_team1 - $actual_team2)) {
            // Richtige Differenz
            $points = 2;
        } elseif (($tip_team1 > $tip_team2 && $actual_team1 > $actual_team2) || 
                  ($tip_team1 < $tip_team2 && $actual_team1 < $actual_team2) || 
                  ($tip_team1 == $tip_team2 && $actual_team1 == $actual_team2)) {
            // Richtige Tendenz
            $points = 1;
        }
    } else {
        // Fehlermeldung, falls keine Ergebnisse gefunden wurden
        echo "Keine Ergebnisse für Match ID: $match_id gefunden.";
    }

    return $points;
}

// Abrufen der Spiele, für die Ergebnisse vorhanden sind und die noch nicht bewertet wurden
$stmt = $mysql->prepare("SELECT m.id, m.team1_id, m.team2_id 
                         FROM matches m
                         JOIN results r ON m.id = r.match_id
                         WHERE EXISTS (SELECT 1 FROM tips t WHERE t.match_id = m.id AND t.evaluated = 0)");
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Überprüfen und Auswerten der Tipps für jedes Spiel
foreach ($matches as $match) {
    $match_id = $match['id'];

    // Abrufen der Tipps für das aktuelle Spiel, die noch nicht bewertet wurden
    $stmt = $mysql->prepare("SELECT * FROM tips WHERE match_id = :match_id AND evaluated = 0");
    $stmt->bindParam(':match_id', $match_id);
    $stmt->execute();
    $tips = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tips as $tip) {
        $tip_team1 = $tip['tip_team1'];
        $tip_team2 = $tip['tip_team2'];
        $user_id = $tip['user_id'];

        // Punkte für den Tipp berechnen
        $points = calculatePoints($match_id, $tip_team1, $tip_team2);

        // Punkte zum Benutzerkonto hinzufügen
        $stmt = $mysql->prepare("UPDATE accounts SET points = points + :points WHERE id = :user_id");
        $stmt->bindParam(':points', $points);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Tipp als bewertet markieren
        $stmt = $mysql->prepare("UPDATE tips SET evaluated = 1 WHERE id = :tip_id");
        $stmt->bindParam(':tip_id', $tip['id']);
        $stmt->execute();
    }
}

echo "Alle relevanten Tipps wurden überprüft und ausgewertet.";
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tipps überprüfen</title>
    <link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
</head>
<body>
    <h1>Tipps überprüfen</h1>
    <p>Alle relevanten Tipps wurden überprüft und ausgewertet.</p>
    <footer>
        <p><a href="impressum.php">Impressum</a></p><br>
        <p><a href="logout.php">Logout</a></p>
    </footer>
</body>
</html>
