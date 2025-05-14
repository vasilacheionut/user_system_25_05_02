<?php
session_start();
ob_start();

require_once '../app/Core/Database.php';
require_once '../app/Core/SessionHelper.php';
require_once 'csrf.php';

if (!isset($_SESSION['user'])) {
    flash('error', 'Access denied');
    header('Location: login.php');
    exit;
}

$pdo = Database::connect();
$currentUser = $_SESSION['user'];
$userId = (int)($_GET['id'] ?? 0);

// Verificare CSRF doar pentru POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    // Verificare permisiuni
    if ($currentUser['role'] === 'root' && $userId !== $currentUser['id']) {
        // Root șterge alt user
        $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userToDelete) {
            // Preluăm parola pentru backup
            $stmtPass = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmtPass->execute([$userToDelete['id']]);
            $userPass = $stmtPass->fetchColumn();

            // Backup în tabela deleted_users cu parola inclusă
            $backup = $pdo->prepare("INSERT INTO deleted_users 
                (original_user_id, email, password, role, deleted_by, reason)
                VALUES (?, ?, ?, ?, ?, ?)");
            $backup->execute([
                $userToDelete['id'],
                $userToDelete['email'],
                $userPass,
                $userToDelete['role'],
                $currentUser['id'],
                'Deleted by root'
            ]);

            // Ștergere
            $delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delete->execute([$userId]);

            // Log
            $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
            $log->execute([$currentUser['id'], "Deleted user ID $userId"]);

            flash('success', "User ID $userId deleted successfully");
        } else {
            flash('error', "User not found");
        }

        header('Location: root_panel.php');
        exit;
    }

    // Autodelete
    if ($userId === $currentUser['id']) {
        $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $me = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($me) {
            // Preluăm parola pentru backup
            $stmtPass = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmtPass->execute([$me['id']]);
            $userPass = $stmtPass->fetchColumn();

            // Backup cu parola inclusă
            $backup = $pdo->prepare("INSERT INTO deleted_users 
                (original_user_id, email, password, role, deleted_by, reason)
                VALUES (?, ?, ?, ?, ?, ?)");
            $backup->execute([
                $me['id'],
                $me['email'],
                $userPass,
                $me['role'],
                $me['id'],
                'Self-deleted account'
            ]);

            // Ștergere
            $delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delete->execute([$userId]);

            // Log
            $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
            $log->execute([$userId, "Deleted own account"]);

            session_destroy();
            header('Location: register.php');
            exit;
        } else {
            flash('error', "Account not found");
            header('Location: dashboard.php');
            exit;
        }
    }

    flash('error', 'Unauthorized deletion attempt');
    header('Location: dashboard.php');
    exit;
}

// Dacă nu e POST, afișează formular de confirmare
$csrf_token = generate_csrf_token();
?>

<div class="view-container">
    <h2>Confirm Account Deletion</h2>

    <p>Are you sure you want to delete this account?</p>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <button type="submit">Yes, delete</button>
        <a href="dashboard.php">Cancel</a>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = "Delete Account";
include 'template.php';
