<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Доступ только сотрудникам и администраторам
checkAuth(['employee', 'admin']);

// Получаем список всех резерваций с данными автомобиля и пользователя
$sql = "
    SELECT r.id AS reservation_id, r.reservation_date, r.expiration_date, r.payment_status, r.payment_id,
           c.id AS car_id, c.brand, c.model, c.production_year, c.price,
           u.id AS user_id, u.full_name, u.email
    FROM reservations r
    JOIN cars c ON r.car_id = c.id
    JOIN users u ON r.user_id = u.id
    ORDER BY r.reservation_date DESC
";

$result = $conn->query($sql);
$reservations = [];
if ($result) {
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
}

include 'includes/header.php';
include 'templates/reservations.php';
?>




