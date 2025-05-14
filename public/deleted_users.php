<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'root') {
    flash('error', 'Access denied');
    header('Location: dashboard.php');
    exit;
}

$pdo = Database::connect();

$stmt = $pdo->query("
    SELECT d.id, d.original_user_id, d.email, d.role, d.deleted_by, d.reason, d.deleted_at,
           u.email AS deleted_by_email
    FROM deleted_users d
    LEFT JOIN users u ON d.deleted_by = u.id
    ORDER BY d.deleted_at DESC
");

$deletedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="view-container">
    <h1>Deleted Users</h1>
    <p>Backup of deleted accounts</p>

    <?php display_flash(); ?>

    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Original User ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Deleted By</th>
                <th>Reason</th>
                <th>Deleted At</th>
                <th>Actions</th> <!-- Noua coloană pentru Restore -->
            </tr>
        </thead>
        <tbody>
        <?php foreach ($deletedUsers as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['original_user_id'] ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td><?= htmlspecialchars($user['deleted_by_email'] ?? 'Deleted user') ?></td>
                <td><?= htmlspecialchars($user['reason']) ?></td>
                <td><?= $user['deleted_at'] ?></td>
                <td>
                    <form method="POST" action="restore_user.php" onsubmit="return confirm('Restore this user?');" style="display:inline;">
                        <input type="hidden" name="deleted_user_id" value="<?= $user['id'] ?>">
                        <button type="submit">Restore</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="root_panel.php">← Back to Root Panel</a></p>
</div>

<?php
$content = ob_get_clean();
$title = "Deleted Users";
include 'template.php';
