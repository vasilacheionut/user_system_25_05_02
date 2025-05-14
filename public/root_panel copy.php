<?php
session_start();
ob_start(); // Start output buffering
require_once '../app/Core/Database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'root') {
    die("Access denied.");
}

$pdo = Database::connect();

// Schimbare rol (dacă se trimite formularul)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];

    // Prevenire auto-modificare
    if ($userId == $_SESSION['user']['id']) {
        echo "You cannot change your own role.<br>";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$newRole, $userId]);

        // Logare acțiune
        $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $log->execute([$_SESSION['user']['id'], "Changed role of user ID $userId to $newRole"]);
    }
}

// Afișare toți utilizatorii
$users = $pdo->query("SELECT id, email, role FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Root</h1>
<h2>Panel</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Role</th>
        <th>Change Role</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                    <form method="POST" style="display:inline;" class="role-form">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="new_role">
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                            <option value="root" <?= $user['role'] === 'root' ? 'selected' : '' ?>>root</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                <?php else: ?>
                    (you)
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean(); // Capture content
$title = "Root Panel";
include 'template.php';
