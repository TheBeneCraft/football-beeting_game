# EM Betting Game

This is a web-based betting game developed for the European Championship (EM). Users can register, log in, place their bets on various matches, and view their scores based on correct predictions. The project is built using PHP and MySQL.

## Features

- **User Registration & Authentication:** Secure login system with session management.
- **Betting System:** Users can place bets on ongoing matches before they start.
- **Admin Panel:** Manage matches, users, and results.
- **Points System:** Users earn points for correct predictions.
- **Email Notifications:** Sends users their total points at the end of the championship.

## Installation

1. Clone this repository:
   ```bash
   git clone https://github.com/yourusername/em-betting-game.git
   cd em-betting-game
   ```

2. Import the database schema:
   Use this SQL code to create the Database:
   -- Database: em_betting_game

-- Table structure for table `accounts`
CREATE TABLE `accounts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `vorname` VARCHAR(50),
    `nachname` VARCHAR(50),
    `points` INT DEFAULT 0,
    `is_admin` TINYINT(1) DEFAULT 0
);

-- Table structure for table `matches`
CREATE TABLE `matches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `team1_id` INT NOT NULL,
    `team2_id` INT NOT NULL,
    `match_date` DATETIME NOT NULL,
    `team1_score` INT DEFAULT NULL,
    `team2_score` INT DEFAULT NULL,
    `evaluated` TINYINT(1) DEFAULT 0
);

-- Table structure for table `countries`
CREATE TABLE `countries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL
);

-- Table structure for table `tips`
CREATE TABLE `tips` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `match_id` INT NOT NULL,
    `tip_team1` INT NOT NULL,
    `tip_team2` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `accounts`(`id`),
    FOREIGN KEY (`match_id`) REFERENCES `matches`(`id`)
);

-- Sample data for `countries`
INSERT INTO `countries` (`name`) VALUES 
('Germany'),
('France'),
('Italy'),
('Spain'),
('Portugal');

-- Sample matches data
INSERT INTO `matches` (`team1_id`, `team2_id`, `match_date`) VALUES
(1, 2, '2024-06-12 18:00:00'),
(3, 4, '2024-06-12 21:00:00'),
(2, 5, '2024-06-13 18:00:00');

-- Example user
INSERT INTO `accounts` (`username`, `email`, `password`, `vorname`, `nachname`, `points`, `is_admin`) VALUES 
('admin', 'admin@example.com', 'hashed_password_here', 'John', 'Doe', 0, 1);

4. Configure the database connection:
   - Rename `config.sample.php` to `config.php`.
   - Update the database credentials in `config.php`:
     ```php
     $host = 'your_host';
     $dbname = 'your_database_name';
     $user = 'your_database_user';
     $password = 'your_database_password';
     ```

5. Configure email settings:
   - In `email_config.php`, set up your SMTP settings to enable email notifications.

6. Launch the application:
   - Deploy the project on a local server using XAMPP or a web server of your choice.

## Usage

1. **Register**: Users can create an account to start betting.
2. **Login**: Access your account and view available matches.
3. **Place Bets**: Select a match and place your bet before the game starts.
4. **Admin Controls**: Admins can add or update match details, and update scores.
5. **View Scores**: Users can check their scores and see how they performed.

## Technology Stack

- **Frontend**: HTML, CSS
- **Backend**: PHP, MySQL
- **Libraries**: PDO for database interaction, PHPMailer for sending emails

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -m 'Add some feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Open a pull request.

## License

This project is licensed under the MIT License.

## Contact

For any questions or suggestions, feel free to reach out at: 
- Email: info@benestippspiel.de
If you want to see an example of the Webpage visit:
benestippspiel.de
