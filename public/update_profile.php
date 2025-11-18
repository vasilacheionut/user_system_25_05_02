<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';
require_once 'csrf.php';

// 🔐 Verificare autentificare
if (!isset($_SESSION['user'])) {
    flash('error', 'You must be logged in to update your profile');
    header('Location: login.php');
    exit;
}

$pdo = Database::connect();
$user_id = $_SESSION['user']['id'];
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token!");
    }

    $email = trim($_POST['user']['email'] ?? '');
    $name = trim($_POST['user']['name'] ?? '');
    $avatar = trim($_POST['user']['avatar'] ?? ''); // poate fi URL sau path intern

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Invalid email address');
        header('Location: update_profile.php');
        exit;
    }

    // Actualizare email în tabela users
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->execute([$email, $user_id]);

    // Actualizare name și avatar în tabela profiles
    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, name, avatar) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE name = ?, avatar = ?");
    $stmt->execute([$user_id, $name, $avatar, $name, $avatar]);

    // Dacă email-ul s-a schimbat, închide sesiunea
    if ($_SESSION['user']['email'] !== $email) {
        // Distruge sesiunea curentă
        session_destroy();
        session_start(); // Reîncepe sesiunea pentru a preveni conflicte
        flash('success', 'Email updated. Please log in again.');
        header('Location: login.php');
        exit;
    }

    // Actualizare date sesiune (doar dacă email-ul nu s-a schimbat)
    $_SESSION['user']['email'] = $email;  // Actualizează email-ul în sesiune
    $_SESSION['user']['name'] = $name;    // Actualizează numele în sesiune
    $_SESSION['user']['avatar'] = $avatar; // Actualizează avatarul în sesiune
    


    // Log
    $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
    $log->execute([$user_id, 'Profile updated']);

    flash('success', 'Profile updated successfully');
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obține datele actuale din tabela users
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obține datele actuale din tabela profiles
    $stmt = $pdo->prepare("SELECT name, avatar FROM profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // Dacă profilul nu există încă, setează valori implicite goale
    if (!$profile) {
        $profile = ['name' => '', 'avatar' => ''];
    }

    include '../app/Views/update_profile_form.php';
    $content = ob_get_clean();
    $title = "Update Profile";
    include 'template.php';
    exit;
}
