<?php
session_start();
ob_start(); // Start output buffering

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<h1>Bun venit, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h1>
<h2>Ai fost autentificat cu succes.</h2>

<?php
$content = ob_get_clean(); // Capture content
$title = "Home";
include 'template.php';
