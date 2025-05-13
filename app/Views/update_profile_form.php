<div class="form-container">
    <form class="form-content" action="update_profile.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div>
            <label for="email">Email</label>
            <input type="email" name="user[email]" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div>
            <label for="name">Name</label>
            <input type="text" name="user[name]" id="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required>
        </div>

        <div>
            <label for="avatar">Avatar</label>
            <input type="text" name="user[avatar]" id="avatar" value="<?php echo htmlspecialchars($profile['avatar']); ?>">
        </div>

        <div>
            <button type="submit">Update Profile</button>
        </div>
    </form>
</div>