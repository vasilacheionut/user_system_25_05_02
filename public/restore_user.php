<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';
require_once 'csrf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'root') {
    flash('error', 'Access denied');
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: deleted_users.php');
    exit;
}

// Optional: Poți adăuga validare CSRF dacă ai token
// if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
//     die("Invalid CSRF token");
// }

$deletedUserId = (int)($_POST['deleted_user_id'] ?? 0);
if ($deletedUserId <= 0) {
    flash('error', 'Invalid user ID');
    header('Location: deleted_users.php');
    exit;
}

$pdo = Database::connect();

// Preluăm datele din backup
$stmt = $pdo->prepare("SELECT * FROM deleted_users WHERE id = ?");
$stmt->execute([$deletedUserId]);
$deletedUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$deletedUser) {
    flash('error', 'Deleted user not found');
    header('Location: deleted_users.php');
    exit;
}

// Verificăm dacă există deja un user cu același email (pentru evitarea duplicatelor)
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$deletedUser['email']]);
$existingUser = $stmt->fetch();

if ($existingUser) {
    flash('error', 'User with this email already exists in active users');
    header('Location: deleted_users.php');
    exit;
}

// Restaurăm userul în tabela users
$insert = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
$insert->execute([
    $deletedUser['email'],
    $deletedUser['password'], // presupunem că ai și coloana password în deleted_users, dacă nu, adaptează
    $deletedUser['role']
]);

// Ștergem din backup după restaurare
$delete = $pdo->prepare("DELETE FROM deleted_users WHERE id = ?");
$delete->execute([$deletedUserId]);

// Logăm acțiunea
$log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
$log->execute([
    $_SESSION['user']['id'],
    "Restored deleted user with original ID {$deletedUser['original_user_id']} and email {$deletedUser['email']}"
]);

flash('success', 'User restored successfully');
header('Location: deleted_users.php');
exit;
