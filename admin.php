<?php
session_start();
require('mysql.php');

// Überprüfen, ob der Benutzer eingeloggt ist und Admin-Rechte hat
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// CSRF-Token generieren und speichern
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Abrufen der Spiele aus der Datenbank, für die noch keine Ergebnisse eingetragen wurden
$stmt = $mysql->prepare("SELECT m.id, c1.name AS team1, c2.name AS team2, m.match_date 
                         FROM matches m
                         JOIN countries c1 ON m.team1_id = c1.id
                         JOIN countries c2 ON m.team2_id = c2.id
                         WHERE m.id NOT IN (SELECT match_id FROM results)
                         ORDER BY m.match_date");
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF-Token überprüfen
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Ungültiges CSRF-Token");
    }

    // Ergebnis speichern
    $match_id = $_POST['match_id'];
    $result_team1 = $_POST['result_team1'];
    $result_team2 = $_POST['result_team2'];

    $stmt = $mysql->prepare("INSERT INTO results (match_id, result_team1, result_team2) VALUES (:match_id, :result_team1, :result_team2)
                             ON DUPLICATE KEY UPDATE result_team1 = :result_team1, result_team2 = :result_team2");
    $stmt->bindParam(':match_id', $match_id);
    $stmt->bindParam(':result_team1', $result_team1);
    $stmt->bindParam(':result_team2', $result_team2);

    if ($stmt->execute()) {
        echo "Ergebnis wurde gespeichert.";
    } else {
        echo "Fehler beim Speichern des Ergebnisses.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Bereich - Ergebnisse eintragen</title>
    <link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
    <link rel="icon" type="image/x-icon" href="favico.co.png">
</head>
<body>
    <h1>Admin Bereich - Ergebnisse eintragen</h1>
    <p>Willkommen, Admin <?php echo htmlspecialchars($_SESSION['vorname']) . " " . htmlspecialchars($_SESSION['nachname']); ?>!</p>
    <p><a href="tippspiel.php">Zurück zum Tippspiel</a></p>
    <p><a href="punktestand.php">Punktestand</a></p>
    <p><a href="logout.php">Logout</a></p>

    <h2>Ergebnis für ein Spiel eintragen</h2>
    <form method="POST" action="admin.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="match_id">Spiel auswählen:</label>
        <select id="match_id" name="match_id" onchange="updateTeamNames(this)">
            <?php foreach ($matches as $match): ?>
                <option value="<?php echo htmlspecialchars($match['id']); ?>"
                        data-team1="<?php echo htmlspecialchars($match['team1']); ?>"
                        data-team2="<?php echo htmlspecialchars($match['team2']); ?>">
                    <?php echo htmlspecialchars($match['team1'] . ' vs ' . $match['team2']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <label for="result_team1">Ergebnis für <span id="team1_name"></span>:</label>
        <input type="number" name="result_team1" id="result_team1" required><br>
        <label for="result_team2">Ergebnis für <span id="team2_name"></span>:</label>
        <input type="number" name="result_team2" id="result_team2" required><br>
        <button type="submit">Ergebnis eintragen</button>
    </form>

    <script>
        function updateTeamNames(select) {
            const selectedOption = select.options[select.selectedIndex];
            const team1 = selectedOption.getAttribute('data-team1');
            const team2 = selectedOption.getAttribute('data-team2');
            document.getElementById('team1_name').innerText = team1;
            document.getElementById('team2_name').innerText = team2;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const matchSelect = document.getElementById('match_id');
            updateTeamNames(matchSelect);
        });
    </script>
</body>
</html>
