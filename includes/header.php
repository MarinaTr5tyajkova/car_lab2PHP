<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Автомагазин') ?></title>
    <link rel="stylesheet" href="/car_dealership/assets/style.css?v=0.0.11">
</head>
<body>
<header>
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">
                    <img src="img/logo.png" alt="Логотип" style="width: 150px; height: auto;">
                </a>
            </div>

            <h2 style="font-size: 24px; font-weight: 600; color: black;">Продажа автомобилей по всей России.</h2>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isEmployee()): ?>
                        <a href="/car_dealership/employee_panel.php">Панель сотрудника</a>
                        <a href="/car_dealership/reservations.php">Бронирования</a>
                    <?php elseif (isAdmin()): ?>
                        <a href="/car_dealership/admin_panel.php">Управление сотрудниками</a>
                    <?php endif; ?>
                    <a href="/car_dealership/logout.php">Выйти</a>
                <?php else: ?>
                    <a href="/car_dealership/login.php">Вход</a>
                    <a href="/car_dealership/register.php">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<main class="container">