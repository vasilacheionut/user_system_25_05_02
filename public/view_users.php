<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';

// ✅ Verificare autentificare și permisiuni
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'root'])) {
    flash('error', 'Access denied.');
    header('Location: login.php');
    exit;
}

$pdo = Database::connect();

// 🔎 Preluare utilizatori
$stmt = $pdo->query("SELECT id, email, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="view-container">
    <h1>Utilizatori Înregistrați</h1>

    <?php if ($message = get_flash('success')): ?>
        <div class="alert success"><?= htmlspecialchars($message) ?></div>
    <?php elseif ($message = get_flash('error')): ?>
        <div class="alert error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
$title = "View Users";
include 'template.php';
