<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/reservation_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

if (!isset($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
    // Неверный запрос — редиректим на список с ошибкой
    header("Location: index.php.php?error=1");
    exit();
}

$car_id = (int)$_GET['car_id'];
$user_id = $_SESSION['user_id'];

try {
    $reservation_id = createReservation($conn, $user_id, $car_id);

    // После успешной резервации делаем редирект на страницу со списком автомобилей и сообщением об успехе
    header("Location: index.php?success=1&car_id=$car_id");
    exit();

} catch (Exception $e) {
    // При ошибке редиректим с параметром ошибки и сообщением (можно расширить)
    header("Location: index.php?error=1&message=" . urlencode($e->getMessage()));
    exit();
}
