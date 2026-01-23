⚽ EM Betting Game

Ein webbasiertes Tippspiel zur Fußball-Europameisterschaft (EM).
User können sich registrieren, Tipps auf Spiele abgeben, Punkte sammeln und am Ende die Gesamtwertung einsehen.

✨ Features

👤 User Accounts (Registrierung & Login)

🎯 Tippsystem für laufende Spiele — vor Spielbeginn

🛠️ Admin-Panel zur Verwaltung von Spielen, Ergebnissen & Usern

🏆 Punktesystem für korrekte Tipps

✉️ E-Mail Benachrichtigungen nach Turnierende

🔐 Session & Rechteverwaltung

🧱 Tech Stack
Bereich	Technologie
Frontend	HTML, CSS
Backend	PHP (PDO)
Datenbank	MySQL
Email	PHPMailer
🚀 Installation
1️⃣ Repository klonen
git clone https://github.com/yourusername/em-betting-game.git
cd em-betting-game

2️⃣ Datenbank importieren

Erstelle eine MySQL-Datenbank und importiere das SQL-Schema:

-- Database: em_betting_game

-- Table: accounts
CREATE TABLE accounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  vorname VARCHAR(50),
  nachname VARCHAR(50),
  points INT DEFAULT 0,
  is_admin TINYINT(1) DEFAULT 0
);

-- Table: matches
CREATE TABLE matches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team1_id INT NOT NULL,
  team2_id INT NOT NULL,
  match_date DATETIME NOT NULL,
  team1_score INT DEFAULT NULL,
  team2_score INT DEFAULT NULL,
  evaluated TINYINT(1) DEFAULT 0
);

-- Table: countries
CREATE TABLE countries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

-- Table: tips
CREATE TABLE tips (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  match_id INT NOT NULL,
  tip_team1 INT NOT NULL,
  tip_team2 INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES accounts(id),
  FOREIGN KEY (match_id) REFERENCES matches(id)
);

-- Sample countries
INSERT INTO countries (name) VALUES
('Germany'), ('France'), ('Italy'), ('Spain'), ('Portugal');

-- Sample matches
INSERT INTO matches (team1_id, team2_id, match_date) VALUES
(1, 2, '2024-06-12 18:00:00'),
(3, 4, '2024-06-12 21:00:00'),
(2, 5, '2024-06-13 18:00:00');

-- Example admin user
INSERT INTO accounts (username, email, password, vorname, nachname, points, is_admin)
VALUES ('admin', 'admin@example.com', 'hashed_password_here', 'John', 'Doe', 0, 1);

3️⃣ Konfiguration

In config.php Datenbankdaten anpassen:

$host = 'your_host';
$dbname = 'your_database_name';
$user = 'your_database_user';
$password = 'your_database_password';

4️⃣ E-Mail Einstellungen

In email_config.php SMTP-Daten eintragen.

5️⃣ Starten

Das Projekt auf einem lokalen Server (z. B. XAMPP) oder produktivem Webserver deployen.

🖥️ Nutzung
Aktion	Beschreibung
📝 Registrieren	Account erstellen
🔐 Login	Dashboard & Spiele
⚽ Tippen	Tipps vor Spielbeginn setzen
🛠️ Admin	Spiele/Ergebnisse/User verwalten
📊 Scores	Punkte & Ranglisten ansehen
👨‍💻 Contributing

Fork erstellen

Branch anlegen: git checkout -b feature-branch

Änderungen committen

Pushen & Pull Request öffnen

📄 Lizenz

MIT License

📬 Kontakt

📧 info@benestippspiel.de

🌐 Beispiel: benestippspiel.de
