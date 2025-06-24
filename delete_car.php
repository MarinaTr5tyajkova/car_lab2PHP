<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth(['employee', 'admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID автомобиля');
}

$car_id = (int)$_GET['id'];

// Начинаем транзакцию
$conn->begin_transaction();

try {
    // Удаляем резервации, связанные с автомобилем
    $stmt = $conn->prepare("DELETE FROM reservations WHERE car_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $stmt->close();

    // Удаляем автомобиль
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header("Location: employee_panel.php?success_delete=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Ошибка при удалении: " . $e->getMessage());
}
