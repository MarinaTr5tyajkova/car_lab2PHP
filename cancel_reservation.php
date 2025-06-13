<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/reservation_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

if (!isset($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
    header("Location: index.php?error=Неверный запрос");
    exit();
}

$car_id = (int)$_GET['car_id'];
$user_id = (int)$_SESSION['user_id'];пш

try {
    cancelReservation($conn, $user_id, $car_id);
    header("Location: index.php?success=Резервация отменена");
    exit();
} catch (Exception $e) {
    die("Ошибка: " . htmlspecialchars($e->getMessage()));
}
