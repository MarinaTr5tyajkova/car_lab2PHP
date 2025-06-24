<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/car_functions.php';
require_once 'includes/reservation_functions.php';

// Запускаем сессию, если она ещё не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Получаем список всех машин
$cars = getAllCars($conn);

// Инициализируем массив с активными бронированиями пользователя
$user_reservations = [];

if (isset($_SESSION['user_id'])) {
    $user_reservations = getUserActiveReservations($conn, $_SESSION['user_id']);
}

// Подключаем шаблон с хедером
include 'includes/header.php';

// Подключаем шаблон с содержимым страницы
include 'templates/cars.php';

// Подключаем шаблон с футером (если есть)
include 'includes/footer.php';
