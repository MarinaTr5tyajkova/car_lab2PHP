<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/car_functions.php';
require_once 'includes/reservation_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=Автомобиль не выбран");
    exit();
}

$car_id = (int)$_GET['id'];
$car = getCarById($conn, $car_id);
if (!$car) {
    header("Location: index.php?error=Автомобиль не найден");
    exit();
}

// Инициализируем массив бронирований пользователя, чтобы избежать ошибки в шаблоне
$user_reservations = [];
if (isset($_SESSION['user_id'])) {
    $user_reservations = getUserActiveReservations($conn, $_SESSION['user_id']);
}

// Вычисляем, есть ли активный резерв на данный автомобиль у пользователя
$user_has_reservation = in_array($car['id'], $user_reservations);

include 'includes/header.php';
include 'templates/car_details.php';

