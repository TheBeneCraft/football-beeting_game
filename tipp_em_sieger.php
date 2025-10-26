<?php
session_start();
require('mysql.php');

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $winner = $_POST['winner'];
    $runner_up = $_POST['runner_up'];

    // Tipp speichern
    $stmt = $mysql->prepare("INSERT INTO em_tips (user_id, winner, runner_up) VALUES (:user_id, :winner, :runner_up)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':winner', $winner);
    $stmt->bindParam(':runner_up', $runner_up);

    if ($stmt->execute()) {
        header("Location: tippspiel.php");
        exit();
    } else {
        echo "Fehler beim Speichern des Tipps.";
    }
}

// Länder aus der Datenbank abrufen
$stmt = $mysql->prepare("SELECT id, name FROM countries ORDER BY name");
$stmt->execute();
$countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
<link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
<link rel="icon" type="image/x-icon" href="favico.co.png">
    <meta charset="UTF-8">
    <title>EM Gewinner Tipp</title>
</head>
<body>
    <h1>EM Gewinner Tipp</h1>
    <p>Willkommen, <?php echo htmlspecialchars($_SESSION['vorname']) . ' ' . htmlspecialchars($_SESSION['nachname']); ?>!</p>
    <form action="tipp_em_sieger.php" method="post">
        <label for="winner">Wer gewinnt die EM?</label>
        <select name="winner" id="winner" required>
            <?php foreach ($countries as $country): ?>
                <option value="<?php echo htmlspecialchars($country['id']); ?>"><?php echo htmlspecialchars($country['name']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="runner_up">Wer wird Zweiter?</label>
        <select name="runner_up" id="runner_up" required>
            <?php foreach ($countries as $country): ?>
                <option value="<?php echo htmlspecialchars($country['id']); ?>"><?php echo htmlspecialchars($country['name']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" name="submit">Tipp abgeben</button>
    </form>
</body>
</html>
