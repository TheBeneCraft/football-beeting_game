<?php
session_start();
require('mysql.php'); // Verbindung zur Tippspiel-Datenbank

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzereingaben für Login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Suche den Benutzer in der Tippspiel-Datenbank
    $stmt = $mysql->prepare("SELECT id, username, vorname, nachname, eMail, password FROM accounts WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Benutzer ist authentifiziert
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['vorname'] = $user['vorname'];
        $_SESSION['nachname'] = $user['nachname'];
        $_SESSION['eMail'] = $user['eMail'];

        // Hier werden die Daten zum Hub übertragen
        $hubUrl = 'http://localhost/about-me//transfer_data.php'; // API-Endpunkt im Hub
        $data = [
            'username' => $user['username'],
            'eMail' => $user['eMail'],
            'vorname' => $user['vorname'],
            'nachname' => $user['nachname'],
            'password' => $user['password'],
            'admin' => 0, // Standardmäßig kein Admin, je nach Bedarf anpassen
            'permisson' => 0 // Berechtigungslevel (Normal)
        ];

        // cURL Anfrage an das Hub senden
        $ch = curl_init($hubUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        $_SESSION['redirect_url'] = 'https://dimisphere.de'; 
        // Link für die Weiterleitung nach erfolgreichem Login
        $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'hub.php';

        // Nach erfolgreichem Senden zum Hub weiterleiten
        header("Location: $redirectUrl"); // Nach dem Übertragen zum Hub weiterleiten
        exit();
    } else {
        // Falsche Anmeldedaten
        $error = "Login fehlgeschlagen!";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login - Tippspiel</title>
    <link rel="icon" type="image/x-icon" href="favico.co.png">
</head>
<body>
    <h1>Login - Tippspiel</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Login-Formular -->
    <form action="tippspiel_login.php" method="post">
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <p>Noch keinen Account? <a href="register.php">Jetzt registrieren</a></p>
</body>
</html>
