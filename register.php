<?php
session_start();
require('mysql.php');

if (isset($_POST['submit'])) {
    $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user OR email = :email");
    $stmt->bindParam(":user", $_POST['username']);
    $stmt->bindParam(":email", $_POST['email']);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row) {
        echo "Benutzername oder E-Mail bereits vergeben!";
    } else {
        if ($_POST['password'] === $_POST['password2']) {
            $stmt = $mysql->prepare("INSERT INTO accounts (username, email, password, vorname, nachname) VALUES (:user, :email, :password, :vorname, :nachname)");
            $stmt->bindParam(":user", $_POST['username']);
            $stmt->bindParam(":email", $_POST['email']);
            $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $hash);
            $stmt->bindParam(":vorname", $_POST['vorname']);
            $stmt->bindParam(":nachname", $_POST['nachname']);
            $stmt->execute();

            // Benutzer einloggen
            $user_id = $mysql->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['vorname'] = $_POST['vorname'];
            $_SESSION['nachname'] = $_POST['nachname'];

            header("Location: tipp_em_sieger.php");
            exit();
        } else {
            echo "Die Passwörter stimmen nicht überein!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
<link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
<link rel="icon" type="image/x-icon" href="favico.co.png">
    <meta charset="UTF-8">
    <title>Registrierung</title>
</head>
<body>
    <h1>Registrierung</h1>
    <form action="register.php" method="post">
        <label for="username">Benutzername:</label>
        <input type="text" name="username" id="username" required><br>
        <label for="email">E-Mail:</label>
        <input type="email" name="email" id="email" required><br>
        <label for="password">Passwort:</label>
        <input type="password" name="password" id="password" required><br>
        <label for="password2">Passwort wiederholen:</label>
        <input type="password" name="password2" id="password2" required><br>
        <label for="vorname">Vorname:</label>
        <input type="text" name="vorname" id="vorname" required><br>
        <label for="nachname">Nachname:</label>
        <input type="text" name="nachname" id="nachname" required><br>
        <button type="submit" name="submit">Registrieren</button>
    </form>
    <button class="regestrieren" onclick="regaboutTipp()">Über dimisphere.de anmelden</button>
    <script>
                function regaboutTipp() {
                    window.location.href = 'http://localhost/about-me/hub_login.php'; }
</script>
</body>
</html>
