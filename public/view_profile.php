<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';

// 🔐 Verificare autentificare
if (!isset($_SESSION['user'])) {
    flash('error', 'You must be logged in to view your profile');
    header('Location: login.php');
    exit;
}

$pdo = Database::connect();
$user_id = $_SESSION['user']['id'];

// Obține email din tabela users
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obține date profil din tabela profiles
$stmt = $pdo->prepare("SELECT name, avatar FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Dacă profilul nu există încă, setează valori implicite
if (!$profile) {
    $profile = ['name' => '', 'avatar' => ''];
}

// Include view
include '../app/Views/view_profile_content.php';

// Template principal
$content = ob_get_clean();
$title = "View Profile";
include 'template.php';
