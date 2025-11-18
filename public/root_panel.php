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

$pdo = Database::connect();

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $userId = (int)($_POST['user_id'] ?? 0);
    $newRole = $_POST['new_role'] ?? '';

    if ($userId === $_SESSION['user']['id']) {
        flash('error', 'You cannot change your own role.');
    } elseif (!in_array($newRole, ['user', 'admin', 'root'])) {
        flash('error', 'Invalid role selected.');
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$newRole, $userId]);

        $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $log->execute([$_SESSION['user']['id'], "Changed role of user ID $userId to $newRole"]);

        flash('success', "User ID $userId role changed to $newRole");
    }

    header('Location: root_panel.php');
    exit;
}

$users = $pdo->query("SELECT id, email, role FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$csrf_token = generate_csrf_token();
?>

<div class="view-container">
    <h1>Root Panel</h1>
    <p>Manage all users</p>

    <?php display_flash(); // Afișează mesajele flash (success / error)
?>

    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Change Role</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <select name="new_role">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                <option value="root" <?= $user['role'] === 'root' ? 'selected' : '' ?>>root</option>
                            </select>
                            <button type="submit">Update</button>
                            <a href="delete_account.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
                        </form>
                    <?php else: ?>
                        (you)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = "Root Panel";
include 'template.php';
