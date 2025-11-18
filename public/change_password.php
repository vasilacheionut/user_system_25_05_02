<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';
require_once 'csrf.php';

// 🔐 Redirecționează dacă nu ești logat
if (!isset($_SESSION['user'])) {
    flash('error', 'You must be logged in to change password');
    header('Location: login.php');
    exit;
}

$csrf_token = generate_csrf_token(); // 🔐 Generează token CSRF dacă nu există
$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token!");
    }

    $user_id = $_SESSION['user']['id'];
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // ✅ Validare parolă nouă
    if (strlen($new_password) < 6) {
        flash('error', 'New password must be at least 6 characters');
        header('Location: change_password.php');
        exit;
    }

    if ($new_password !== $confirm_password) {
        flash('error', 'Passwords do not match');
        header('Location: change_password.php');
        exit;
    }

    // 🔍 Verificare parolă veche
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($old_password, $user['password'])) {
        flash('error', 'Old password is incorrect');
        header('Location: change_password.php');
        exit;
    }

    // 🔐 Actualizare parolă
    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$new_hashed, $user_id]);

    // 📝 Logare acțiune
    $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
    $log->execute([$user_id, 'Password changed']);

    // 🔓 Invalidare sesiune și reautentificare
    session_unset();           // Golește variabilele din sesiune
    session_destroy();         // Distruge sesiunea curentă
    session_start();           // Repornește sesiunea pentru flash
    flash('success', 'Password changed successfully. Please log in again.');
    header('Location: login.php');
    exit;
}

// 👉 Afișare formular (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include '../app/Views/change_password_form.php';
    $content = ob_get_clean();
    $title = "Change Password";
    include 'template.php';
    exit;
}
