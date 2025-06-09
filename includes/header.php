<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME ?? 'Автомагазин') ?></title>
    <link rel="stylesheet" href="./assets/style.css?v=0.0.3">
</head>
<body>
<header>
    <div class="container">
        <h1><?= htmlspecialchars(SITE_NAME ?? 'Автомагазин') ?></h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isEmployee() || isAdmin()): ?>
                    <a href="./employee_panel.php">Панель сотрудника</a>
                <?php endif; ?>
                <a href="./profile.php">Профиль</a>
                <?php if (isAdmin()): ?>
                    <a href="./admin_panel.php">Админка</a>
                <?php endif; ?>
                <a href="./logout.php">Выйти</a>
            <?php else: ?>
                <a href="./login.php">Вход</a>
                <a href="./register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
