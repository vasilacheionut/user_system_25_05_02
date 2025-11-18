<h2>Change Password</h2>

<?php if ($msg = get_flash('error')): ?>
    <div class="error"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($msg = get_flash('success')): ?>
    <div class="success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="form-container">
    <form class="form-content" method="POST" action="change_password.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <label for="old_password">Old Password:</label>
        <input type="password" name="old_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Update Password</button>
    </form>
</div>