<?php
session_start();
require('mysql.php');

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Abfrage der Benutzer und deren Punkte, sortiert nach Punkten in absteigender Reihenfolge
$stmt = $mysql->prepare("SELECT username, points FROM accounts ORDER BY points DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Punktestand</title>
    <link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
    <link rel="icon" type="image/x-icon" href="favico.co.png">
</head>
<body>
    <h1>Punktestand</h1>
    <table>
        <thead>
            <tr>
                <th>Rang</th>
                <th>Benutzername</th>
                <th>Punkte</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rang = 1; // Initialisierung der Rangnummer
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $rang . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['points']) . "</td>";
                echo "</tr>";
                $rang++; // Erhöhung der Rangnummer für den nächsten Benutzer
            }
            ?>
        </tbody>
    </table>
    <p><a href="tippspiel.php">Zurück zum Tippspiel</a></p>

    <footer>
    <p><a href="impressum.php">Impressum</a></p><br>
    <p><a href="logout.php">Logout</a></p>
</footer>
</body>
</html>
