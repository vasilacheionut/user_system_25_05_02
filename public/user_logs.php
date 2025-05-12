<?php
session_start();
ob_start(); // Start output buffering

require_once '../app/Core/Database.php';

if (!isset($_SESSION['user'])) {
    die("Access denied.");
}

$pdo = Database::connect();

// Verifică rolul
$isAdmin = in_array($_SESSION['user']['role'], ['admin', 'root']);

if ($isAdmin) {
    // Toate logurile
    $stmt = $pdo->query("SELECT user_logs.id, users.email, user_logs.action, user_logs.log_time 
                         FROM user_logs 
                         JOIN users ON user_logs.user_id = users.id
                         ORDER BY user_logs.log_time DESC");
} else {
    // Doar logurile proprii
    $stmt = $pdo->prepare("SELECT user_logs.id, users.email, user_logs.action, user_logs.log_time 
                           FROM user_logs 
                           JOIN users ON user_logs.user_id = users.id
                           WHERE user_logs.user_id = ?
                           ORDER BY user_logs.log_time DESC");
    $stmt->execute([$_SESSION['user']['id']]);
}

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>User</h1>

<h2>Logs:</h2>
<table>
    <tr>
        <th>ID</th>
        <?php if ($isAdmin): ?><th>Email</th><?php endif; ?>
        <th>Action</th>
        <th>Time</th>
    </tr>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['id'] ?></td>
            <?php if ($isAdmin): ?><td><?= htmlspecialchars($log['email']) ?></td><?php endif; ?>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= $log['log_time'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean(); // Capture content
$title = "User Logs";
include 'template.php';
