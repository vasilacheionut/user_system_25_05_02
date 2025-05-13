<div class="form-container">
    <form class="form-content" action="update_profile.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <label for="email">Email</label>
        <input type="email" name="user[email]" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="name">Name</label>
        <input type="text" name="user[name]" id="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required>

        <label for="avatar">Avatar</label>
        <input type="text" name="user[avatar]" id="avatar" value="<?php echo htmlspecialchars($profile['avatar']); ?>">

        <button type="submit">Update Profile</button>
    </form>
</div>