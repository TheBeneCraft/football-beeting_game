# EM Betting Game

Ein webbasiertes Tippspiel zur Fußball-Europameisterschaft (EM). Benutzer können sich registrieren, Tipps abgeben, Punkte sammeln und die Gesamtauswertung einsehen.

## Funktionen

- Benutzerregistrierung und Login
- Tippsystem für Spiele (vor Spielbeginn)
- Adminbereich zur Verwaltung von Spielen, Ergebnissen und Benutzern
- Punktesystem für korrekte Tipps
- E-Mail-Benachrichtigung nach Turnierabschluss

## Technologie-Stack

- PHP (PDO)
- MySQL
- HTML/CSS
- PHPMailer (E-Mail-Versand)

## Installation

### 1. Repository klonen

git clone https://github.com/TheBeneCraft/em-betting-game.git
cd em-betting-game

### 2. Datenbank importieren

MySQL-Datenbank erstellen und folgendes Schema importieren:

-- Database: em_betting_game

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

CREATE TABLE matches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team1_id INT NOT NULL,
  team2_id INT NOT NULL,
  match_date DATETIME NOT NULL,
  team1_score INT DEFAULT NULL,
  team2_score INT DEFAULT NULL,
  evaluated TINYINT(1) DEFAULT 0
);

CREATE TABLE countries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE tips (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  match_id INT NOT NULL,
  tip_team1 INT NOT NULL,
  tip_team2 INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES accounts(id),
  FOREIGN KEY (match_id) REFERENCES matches(id)
);

INSERT INTO countries (name) VALUES ('Germany'), ('France'), ('Italy'), ('Spain'), ('Portugal');

INSERT INTO matches (team1_id, team2_id, match_date) VALUES
(1, 2, '2024-06-12 18:00:00'),
(3, 4, '2024-06-12 21:00:00'),
(2, 5, '2024-06-13 18:00:00');

INSERT INTO accounts (username, email, password, vorname, nachname, points, is_admin)
VALUES ('admin', 'admin@example.com', 'hashed_password_here', 'John', 'Doe', 0, 1);

### 3. Konfiguration

In config.php Daten eintragen:

$host = 'your_host';
$dbname = 'your_database_name';
$user = 'your_database_user';
$password = 'your_database_password';

SMTP-Konfiguration in email_config.php eintragen.

### 4. Deployment

Die Anwendung kann lokal (z.B. XAMPP) oder auf einem Webserver ausgeführt werden.

## Nutzung

- Account erstellen
- Login
- Spiele anzeigen
- Tipps abgeben (vor Spielbeginn)
- Ergebnisse und Punktestand einsehen
- Admin verwaltet Spiele und Benutzer

## Lizenz

MIT License

## Kontakt

info@benestippspiel.de
Beispiel: benestippspiel.de
