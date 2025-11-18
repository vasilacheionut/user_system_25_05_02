<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
include_once __DIR__ . '/../app/Core/SessionHelper.php';
require_once 'csrf.php'; // Funcții CSRF

$csrf_token = generate_csrf_token(); // Obține token stabil din sesiune

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificare CSRF
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF token invalid!");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        // Log login
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

// GET → afișare formular
include '../app/Views/login_form.php';
$content = ob_get_clean();
$title = "Login";
include 'template.php';
exit;
?>
