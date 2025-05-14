<?php
// menu.php
$user = $_SESSION['user'] ?? null;
?>

<div class="navbar">
    <div class="left">
        <?php if ($user): ?>
            <a href="home.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
        <?php endif; ?>
        <?php if ($user['role'] === 'root'): ?>
            <a href="phpinfo.php">PHP Info</a>
        <?php endif; ?>
    </div>

    <div class="right">
        <?php if ($user): ?>
            <div class="dropdown">
                <button class="dropdown-button">
                    <?= htmlspecialchars($user['email']) ?> (<?= $user['role'] ?>)
                </button>
                <div class="dropdown-content">
                    <a href="user_logs.php">User Logs</a>
                    <a href="change_password.php">Change Password</a>
                    <a href="update_profile.php">Update Profile</a>
                    <a href="view_profile.php">View Profile</a>
                    <?php if (in_array($user['role'], ['admin', 'root'])): ?>
                        <a href="view_users.php">View Users</a>
                    <?php endif; ?>

                    <?php if ($user['role'] === 'root'): ?>
                        <a href="root_panel.php">Root Panel</a>
                        <a href="delete_account.php" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
                        <a href="deleted_users.php">Deleted Users</a>                        
                    <?php endif; ?>

                    <form action="logout.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <button type="submit" style="width: 100%; background: none; border: none; padding: 10px; text-align: left;">Logout</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>


<!-- 🔧 TEST / DEBUG (nu se șterge automat) -->
<div class="container">
    <div class="token">
        <h1>CSRF TOKEN</h1>
        <p><?= htmlspecialchars($_SESSION['csrf_token'] ?? 'N/A') ?> :csrf_token</p>
        <p><?= $_SESSION['csrf_token'] ?? 'N/A' ?> :SESSION['csrf_token']</p>
    </div>
    <div>
        <a class="chatgpt" target="_blank" href="https://chatgpt.com/c/6822376b-a06c-8000-9849-4fef057a0af5">
            ChatGPT : Analiza arhiva utilizator user_system_25_05_02
        </a>
    </div>
</div>