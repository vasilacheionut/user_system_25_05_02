<div class="view-container">
    <h1>Profilul Meu</h1>

    <?php if ($message = get_flash('success')): ?>
        <div class="alert success"><?= htmlspecialchars($message) ?></div>
    <?php elseif ($message = get_flash('error')): ?>
        <div class="alert error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Nume:</strong> <?= htmlspecialchars($profile['name']) ?></p>

    <?php if (!empty($profile['avatar'])): ?>
        <p>
            <strong>Avatar:</strong><br>
            <img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="Avatar <?= $profile['avatar']?>">
        </p>
    <?php else: ?>
        <p><strong>Avatar:</strong> <em>Nu este setat.</em></p>
    <?php endif; ?>

    <a class="button-link" href="update_profile.php">🔧 Editează profilul</a>
</div>
