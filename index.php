<?php
session_start();
require('mysql.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysql->prepare("SELECT id, username, vorname, nachname, is_admin, password FROM accounts WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Benutzer ist authentifiziert
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['vorname'] = $user['vorname'];
        $_SESSION['nachname'] = $user['nachname'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: tippspiel.php");
        exit();
    } else {
        // Falscher Benutzername oder falsches Passwort
        $error = "Der Login ist fehlgeschlagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- <link rel="icon" type="image/gif" href="em.png"> -->
    <link rel="icon" type="image/x-icon" href="favico.co.png">
    <!-- <link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css"> -->
    <style>
        /* CSS für das Cookie-Popup */
        #cookieConsent {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background-color: #f1f1f1;
            padding: 20px;
            border: 1px solid #ccc;
            display: none;
        }
    </style>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error)): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="index.php" method="post">
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Noch keinen Account ?</a></p><br>

    <!-- Cookie-Zustimmungspopup -->
    <div id="cookieConsent">
        <p>Wir verwenden Cookies, um Ihre Benutzererfahrung zu verbessern und bestimmte Funktionen bereitzustellen. Indem Sie auf "Zustimmen" klicken, stimmen Sie der Verwendung von Cookies zu. <a href="cookie_policy.html">Mehr erfahren</a></p>
        <button id="acceptCookies">Zustimmen</button>
    </div>

    <script>
        // JavaScript für das Cookie-Popup
        document.addEventListener("DOMContentLoaded", function() {
            if (!document.cookie.includes("cookie_consent=accepted")) {
                document.getElementById("cookieConsent").style.display = "block";
            }

            document.getElementById("acceptCookies").addEventListener("click", function() {
                document.cookie = "cookie_consent=accepted; max-age=" + (10 * 365 * 24 * 60 * 60) + "; path=/";
                document.getElementById("cookieConsent").style.display = "none";
            });
        });
    </script>
</body>
</html>
