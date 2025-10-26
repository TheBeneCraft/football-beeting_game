<?php
session_start();
require('mysql.php');

if (isset($_POST['submit'])) {
    // Debugging: Inhalt des $_POST-Arrays anzeigen
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user AND is_admin = 1");
        $stmt->bindParam(":user", $_POST['username']);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row && password_verify($_POST['password'], $row['password'])) {
            $_SESSION['admin'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: admin.php");
            exit();
        } else {
            echo "Login fehlgeschlagen!";
        }
    } else {
        echo "Bitte alle Felder ausfüllen!";
    }
} else {
    echo "Ungültiger Zugriff";
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
<link rel="stylesheet" href="https://matcha.mizu.sh/matcha.css">
<link rel="icon" type="image/x-icon" href="favico.co.png">
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <h1>Admin Login</h1>
    <form action="admin_login.php" method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Passwort" required><br>
        <button type="submit" name="submit">Einloggen</button>
    </form>
    <footer>
    <p><a href="impressum.php">Impressum</a></p><br>
    <p><a href="logout.php">Logout</a></p>
</footer>
</body>
</html>
