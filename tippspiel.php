<?php
session_start();
require('mysql.php');

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// CSRF-Token generieren und speichern
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Abrufen der Spiele aus der Datenbank, die noch nicht getippt wurden und die noch nicht vorbei sind
$stmt = $mysql->prepare("SELECT m.id, c1.name AS team1, c2.name AS team2, m.match_date 
                         FROM matches m
                         JOIN countries c1 ON m.team1_id = c1.id
                         JOIN countries c2 ON m.team2_id = c2.id
                         WHERE NOT EXISTS (SELECT 1 FROM tips t WHERE t.match_id = m.id AND t.user_id = :user_id)
                         AND m.match_date > NOW()
                         ORDER BY m.match_date");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF-Token überprüfen
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Ungültiges CSRF-Token");
    }

    // Tipp speichern
    $user_id = $_SESSION['user_id'];
    $match_id = $_POST['match_id'];
    $tip_team1 = $_POST['tip_team1'];
    $tip_team2 = $_POST['tip_team2'];

    // Überprüfen, ob der Tipp bereits abgegeben wurde
    $stmt = $mysql->prepare("SELECT * FROM tips WHERE user_id = :user_id AND match_id = :match_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':match_id', $match_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Du hast bereits einen Tipp für dieses Spiel abgegeben.";
    } else {
        $stmt = $mysql->prepare("INSERT INTO tips (user_id, match_id, tip_team1, tip_team2) VALUES (:user_id, :match_id, :tip_team1, :tip_team2)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':match_id', $match_id);
        $stmt->bindParam(':tip_team1', $tip_team1);
        $stmt->bindParam(':tip_team2', $tip_team2);

        if ($stmt->execute()) {
            echo "Dein Tipp wurde gespeichert.";
            // Aktualisiere die Seite und wähle das nächste Spiel
            echo "<script>window.location.reload();</script>";
        } else {
            echo "Fehler beim Speichern des Tipps.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>EM Tippspiel</title>
    <link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
    <link rel="icon" type="image/x-icon" href="favico.co.png">
</head>
<body>
    <h1>EM Tippspiel</h1>
    <p>Willkommen, <?php echo htmlspecialchars($_SESSION['vorname']) . " " . htmlspecialchars($_SESSION['nachname']); ?>!</p> <!-- Vorname und Nachname anzeigen -->
    <p><a href="punktestand.php">Punktestand</a></p>
    <p><a href="view_tips.php">Tipps</a></p>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="admin.php">Admin-Bereich</a>
    <?php endif; ?>

    <h2>Tippen :</h2>
    <?php if (count($matches) > 0): ?>
        <form id="tipForm" action="tippspiel.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="match_id">Spiel auswählen:</label>
            <select id="match_id" name="match_id">
                <?php foreach ($matches as $index => $match): ?>
                    <option value="<?php echo htmlspecialchars($match['id']); ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($match['team1'] . ' vs ' . $match['team2'] . ' - ' . $match['match_date']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="tip_team1">Tipp für <span id="team1_name"><?php echo htmlspecialchars($matches[0]['team1']); ?></span>:</label>
            <input type="number" name="tip_team1" id="tip_team1" required><br>
            <label for="tip_team2">Tipp für <span id="team2_name"><?php echo htmlspecialchars($matches[0]['team2']); ?></span>:</label>
            <input type="number" name="tip_team2" id="tip_team2" required><br>
            <button type="submit">Tipp abgeben</button>
        </form>
    <?php else: ?>
        <p>Es gibt keine verfügbaren Spiele zum Tippen.</p>
    <?php endif; ?>

    <footer>
        <p><a href="impressum.php">Impressum</a></p><br>
        <p><a href="logout.php">Logout</a></p>
    </footer>

    <script>
        document.getElementById('match_id').addEventListener('change', function() {
            var selectedIndex = this.selectedIndex;
            var options = this.options;
            var team1 = options[selectedIndex].text.split(' vs ')[0];
            var team2 = options[selectedIndex].text.split(' vs ')[1].split(' - ')[0];
            document.getElementById('team1_name').textContent = team1;
            document.getElementById('team2_name').textContent = team2;
        });
    </script>
</body>
</html>
