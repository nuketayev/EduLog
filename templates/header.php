<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= defined('APP_NAME') ? APP_NAME : 'EduLog' ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
</head>
<body>

<header>
    <div class="container navbar">
        <a href="index.php" class="logo"><?= defined('APP_NAME') ? APP_NAME : 'EduLog' ?></a>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php">Moje Úkoly</a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" class="text-admin">Admin</a>
                <?php endif; ?>

                <a href="create.php" class="btn btn-success">+ Nový úkol</a>
                <a href="logout.php" class="text-danger">Odhlásit</a>
            <?php else: ?>
                <a href="login.php">Přihlášení</a>
                <a href="register.php" class="btn btn-primary">Registrace</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
    <?php 
    if (function_exists('get_flash')) {
        $msg = get_flash();
        if ($msg): ?>
            <div class="alert alert-<?= $msg['type'] ?>">
                <?= h($msg['msg']) ?>
            </div>
        <?php endif; 
    }
    ?>