<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
ob_start(); // Start output buffering

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../app/Core/Database.php';
$pdo = Database::connect();

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM user_logs WHERE user_id = ? ORDER BY log_time DESC");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<h1>Dashboard - <?php echo htmlspecialchars($_SESSION['user']['email']); ?></h1>

<h2>Ultimele tale acțiuni:</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Action</th>
        <th>Time</th>
    </tr>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['user_id'] ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= $log['log_time'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean(); // Capture content
$title = "Dashboard";
include 'template.php';
