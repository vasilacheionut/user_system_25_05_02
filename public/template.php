<?php include_once __DIR__ . '/../app/Core/SessionHelper.php'; ?>

<?php
// template.php
$content = $content ?? '';
$title = $title ?? 'Pagina';
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'menu.php'; ?>

    <div class="container">
        <?php if ($msg = get_flash('success')): ?>
            <div class="flash success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php if ($msg = get_flash('error')): ?>
            <div class="flash error"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        
        <?= $content ?>

    </div>

</body>

</html>