<?php
// Настройки подключения к базе данных
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'car_dealership';

// Создаём подключение
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");

// Константы
define('SITE_NAME', 'Автомагазин');
