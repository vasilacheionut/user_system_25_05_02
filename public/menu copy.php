<?php
//menu.php
$user = $_SESSION['user'] ?? null;
?>

<div class="navbar">
    <div class="left">
        <a href="home.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <?php if ($user): ?>
            <a href="user_logs.php">Logs</a>
            <?php if ($user['role'] === 'root'): ?>
                <a href="root_panel.php">Root Panel</a>
                <a href="phpinfo.php">PHP Info</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="right">
        <?php if ($user): ?>
            <span><?= htmlspecialchars($user['email']) ?> (<?= $user['role'] ?>)</span>

            <!-- 🔒 Logout cu CSRF protection -->
            <form action="logout.php" method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit">Logout</button>
            </form>

        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="token">
        <h1>CSFR TOKEN</h1>
        <p><?php echo htmlspecialchars($_SESSION['csrf_token']) . " :csrf_token"; ?></p>
        <p><?php echo $_SESSION['csrf_token'] . " :SESSION['csrf_token']"; ?></p>
    </div>
    <div>
        <a class="chatgpt" target="_blank" href="https://chatgpt.com/c/6822376b-a06c-8000-9849-4fef057a0af5">ChatGPT : Analiza arhiva utilizator user_system_25_05_02</a>
    </div>
</div>