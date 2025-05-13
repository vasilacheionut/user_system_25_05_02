<h2>Change Password</h2>

<?php if ($msg = get_flash('error')): ?>
    <div class="error"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($msg = get_flash('success')): ?>
    <div class="success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<form method="POST" action="change_password.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <label for="old_password">Old Password:</label><br>
    <input type="password" name="old_password" required><br><br>

    <label for="new_password">New Password:</label><br>
    <input type="password" name="new_password" required><br><br>

    <label for="confirm_password">Confirm New Password:</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Update Password</button>
</form>


