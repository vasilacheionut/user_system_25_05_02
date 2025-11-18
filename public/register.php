<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
include_once __DIR__ . '/../app/Core/SessionHelper.php';
require_once 'csrf.php';

$csrf_token = generate_csrf_token();

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        die("CSRF token invalid!");
    }

    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        flash('error', 'Email already registered.');
        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $password]);

    $user_id = $pdo->lastInsertId();
    $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
    $log->execute([$user_id, 'User registered']);

    flash('success', 'Registration successful.');
    header("Location: login.php");
    exit;
}

// GET → afișare formular
include '../app/Views/register_form.php';
$content = ob_get_clean();
$title = "Register";
include 'template.php';
exit;
?>
