<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/reservation_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=Автомобиль не выбран");
    exit();
}

$car_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

try {
    $reservation_id = createReservation($conn, $user_id, $car_id);
    header("Location: payment.php?reservation_id=$reservation_id");
    exit();
} catch (Exception $e) {
    die("Ошибка: " . htmlspecialchars($e->getMessage()));
}
