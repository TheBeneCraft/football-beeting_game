<?php
session_start();
require('db.php');  // Verbindung zur Datenbank

if (!isset($_SESSION['user_id'])) {
    // Wenn der Nutzer nicht eingeloggt ist, weiterleiten
    header("Location: hub_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_SESSION['password'];  // Passwort aus der Session
    $user_id = $_SESSION['user_id'];

    // Überprüfen, ob der Username bereits vergeben ist
    $stmt = $mysql->prepare("SELECT id FROM tippspiel_accounts WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $error = "Dieser Username ist bereits vergeben.";
    } else {
        // Speichern des Nutzers im Tippspiel
        $stmt = $mysql->prepare("INSERT INTO tippspiel_accounts (user_id, username, password) VALUES (:user_id, :username, :password)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Weiterleitung nach erfolgreicher Registrierung
        header("Location: tippspiel.php"); // Nach erfolgreichem Login auf das Dashboard weiterleiten
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tippspiel Registrierung</title>
</head>
<body>
    <h1>Registriere dich für das Tippspiel</h1>
    <?php if (isset($error)): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="tippspiel_registration.php" method="post">
        <label for="username">Username für das Tippspiel:</label>
        <input type="text" id="username" name="username" required><br>
        <button type="submit">Registrieren</button>
    </form>
</body>
</html>
