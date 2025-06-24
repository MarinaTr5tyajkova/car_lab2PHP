<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/car_functions.php';

checkAuth(['employee']); // Доступ только сотрудникам

$error = '';
$success = '';

// Обработка добавления нового автомобиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $production_year = intval($_POST['year']);
    $engine_volume = floatval($_POST['engine']);
    $color = trim($_POST['color']);
    $mileage = intval($_POST['mileage']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $status = 'available'; // по умолчанию

    $upload_dir = 'uploads/cars/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_path = $upload_dir . 'default.png'; // дефолтное изображение

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $error = "Недопустимый формат изображения";
        } else {
            $new_name = uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_name;
            if (move_uploaded_file($tmp_name, $destination)) {
                $image_path = $destination;
            } else {
                $error = "Ошибка при загрузке изображения";
            }
        }
    }

    // Простая валидация обязательных полей
    if (!$brand || !$model || !$production_year || !$engine_volume || !$color || !$mileage || !$price) {
        $error = "Пожалуйста, заполните все обязательные поля";
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, engine_volume, production_year, color, mileage, price, status, description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdisidsss", $brand, $model, $engine_volume, $production_year, $color, $mileage, $price, $status, $description, $image_path);

        if ($stmt->execute()) {
            $success = "Автомобиль успешно добавлен";
            $stmt->close();
            // Перезагрузка страницы с параметром success
            header("Location: employee_panel.php?success=1");
            exit();
        } else {
            $error = "Ошибка при добавлении автомобиля: " . $stmt->error;
            $stmt->close();
        }
    }
}

// Получаем список всех автомобилей
$cars = getAllCars($conn);

include 'includes/header.php';
include 'templates/employee_panel.php';

