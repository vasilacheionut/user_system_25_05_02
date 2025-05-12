<?php
include_once __DIR__ . '/../app/Core/SessionHelper.php'; // dacă folosești sesiuni în meniu
$title = 'Instalare sistem';
ob_start();

// -- începem codul normal de instalare --
$host = 'localhost';
$db = 'user_system';
$user = 'root';
$pass = 'Pogimamoru1@';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET $charset COLLATE utf8mb4_general_ci");
    echo "Database '$db' checked/created.<br>";

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin', 'root') NOT NULL DEFAULT 'user'
        )
    ");
    echo "Table 'users' created.<br>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action TEXT NOT NULL,
            log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "Table 'user_logs' created.<br>";

    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'root'");
    $check->execute();
    if ($check->fetchColumn() == 0) {
        $email = 'root@admin.com';
        $password = password_hash('rootpass', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'root')");
        $insert->execute([$email, $password]);
        echo "Root user added: <strong>$email / rootpass</strong><br>";
    } else {
        echo "Root user already exists.<br>";
    }

    echo "<br><strong>Installation complete!</strong>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// -- capturăm outputul în content și apelăm template-ul --
$content = ob_get_clean();
include __DIR__ . '/template.php';
