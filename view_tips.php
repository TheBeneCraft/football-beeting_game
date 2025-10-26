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

// Abrufen der Spiele aus der Datenbank, die bereits stattgefunden haben oder gerade laufen
$stmt = $mysql->prepare("SELECT m.id, c1.name AS team1, c2.name AS team2, m.match_date 
                         FROM matches m
                         JOIN countries c1 ON m.team1_id = c1.id
                         JOIN countries c2 ON m.team2_id = c2.id
                         WHERE m.match_date <= NOW()
                         ORDER BY m.match_date");
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>EM Tippspiel - Tipps ansehen</title>
    <link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
    <link rel="icon" type="image/x-icon" href="favico.co.png">
</head>
<body>
    <h1>EM Tippspiel - Tipps ansehen</h1>
    <p>Willkommen, <?php echo htmlspecialchars($_SESSION['vorname']) . " " . htmlspecialchars($_SESSION['nachname']); ?>!</p>
    <p><a href="tippspiel.php">Zurück zum Tippspiel</a></p>
    <p><a href="punktestand.php">Punktestand</a></p>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="admin.php">Admin-Bereich</a>
    <?php endif; ?>

    <h2>Tipps für ausgewähltes Spiel</h2>
    <form method="POST" action="view_tips.php">
        <label for="match_id">Spiel auswählen:</label>
        <select id="match_id" name="match_id">
            <?php foreach ($matches as $match): ?>
                <option value="<?php echo htmlspecialchars($match['id']); ?>">
                    <?php echo htmlspecialchars($match['team1'] . ' vs ' . $match['team2']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Tipps anzeigen</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])): ?>
        <?php
        $match_id = $_POST['match_id'];

        // Abrufen der Tipps für das ausgewählte Spiel
        $stmt = $mysql->prepare("SELECT t.tip_team1, t.tip_team2, a.username
                                 FROM tips t
                                 JOIN accounts a ON t.user_id = a.id
                                 WHERE t.match_id = :match_id");
        $stmt->bindParam(':match_id', $match_id);
        $stmt->execute();
        $tips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <h3>Tipps der anderen Benutzer:</h3>
        <table>
            <tr>
                <th>Nutzername</th>
                <th>Tipp für Team 1</th>
                <th>Tipp für Team 2</th>
            </tr>
            <?php foreach ($tips as $tip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tip['username']); ?></td>
                    <td><?php echo htmlspecialchars($tip['tip_team1']); ?></td>
                    <td><?php echo htmlspecialchars($tip['tip_team2']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <footer>
        <p><a href="impressum.php">Impressum</a></p><br>
        <p><a href="logout.php">Logout</a></p>
    </footer>
</body>
</html>
