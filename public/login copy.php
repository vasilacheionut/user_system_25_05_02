<?php
session_start();
ob_start();
require_once '../app/Core/Database.php';
include_once __DIR__ . '/../app/Core/SessionHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = Database::connect();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $log->execute([$user['id'], 'User logged in']);

        flash('success', 'Login successful');
        header('Location: dashboard.php');
        exit;
    } else {
        flash('error', 'Invalid credentials');
        header('Location: login.php');
        exit;        
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include '../app/Views/login_form.php';
    $content = ob_get_clean(); // Capture content
    $title = "Login";
    include 'template.php';
    exit;
}
?>
