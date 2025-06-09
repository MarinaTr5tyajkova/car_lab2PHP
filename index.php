<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/car_functions.php';
require_once 'includes/reservation_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$cars = getAllCars($conn);
$user_reservations = [];

if (isset($_SESSION['user_id'])) {
    $user_reservations = getUserActiveReservations($conn, $_SESSION['user_id']);
}

include 'includes/header.php';
include 'templates/cars.php';
include 'templates/footer.php';
