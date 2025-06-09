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

$user_has_reservation = false;
if (isset($_SESSION['user_id'])) {
    $user_has_reservation = userHasActiveReservation($conn, $_SESSION['user_id'], $car_id);
}

include 'includes/header.php';
include 'templates/car_details.php';
include 'templates/footer.php';
