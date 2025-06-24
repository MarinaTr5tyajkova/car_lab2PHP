<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth(['employee', 'admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID автомобиля');
}

$car_id = (int)$_GET['id'];
$error = '';
$success = '';
$show_success_message = false;

// Получаем данные автомобиля
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car_result = $stmt->get_result();
if ($car_result->num_rows === 0) {
    die('Автомобиль не найден');
}
$car = $car_result->fetch_assoc();
$stmt->close();

// Получаем активную резервацию для этого автомобиля (если есть)
$stmt = $conn->prepare("SELECT * FROM reservations WHERE car_id = ? ORDER BY reservation_date DESC LIMIT 1");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$reservation_result = $stmt->get_result();
$reservation = $reservation_result->fetch_assoc();
$stmt->close();

$upload_dir = 'uploads/cars/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $production_year = intval($_POST['production_year']);
    $engine_volume = floatval($_POST['engine_volume']);
    $color = trim($_POST['color']);
    $mileage = intval($_POST['mileage']);
    $price = floatval($_POST['price']); // Цена не обязательна
    $status = $_POST['car_status'];
    $description = trim($_POST['description']);

    $reservation_status = isset($_POST['reservation_status']) ? $_POST['reservation_status'] : null;

    // Обработка загрузки нового фото (если есть)
    $image_path = $car['image_path'];
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
                if ($image_path && file_exists($image_path) && basename($image_path) !== 'default.png') {
                    unlink($image_path);
                }
                $image_path = $destination;
            } else {
                $error = "Ошибка при загрузке изображения";
            }
        }
    }

    // Валидация обязательных полей (без цены)
    if (!$brand || !$model || !$production_year || !$engine_volume || !$color || !$mileage) {
        $error = "Пожалуйста, заполните все обязательные поля";
    }

    // Проверка корректности статуса резервации
    $valid_reservation_statuses = ['pending', 'paid', 'cancelled'];
    if ($reservation_status !== null && !in_array($reservation_status, $valid_reservation_statuses, true)) {
        $error = "Неверный статус резервации";
    }

    if (empty($error)) {
        // Обновляем данные автомобиля
        $stmt = $conn->prepare("UPDATE cars SET brand=?, model=?, engine_volume=?, production_year=?, color=?, mileage=?, price=?, status=?, description=?, image_path=? WHERE id=?");
        $stmt->bind_param("ssdisidsssi", $brand, $model, $engine_volume, $production_year, $color, $mileage, $price, $status, $description, $image_path, $car_id);
        if (!$stmt->execute()) {
            $error = "Ошибка обновления автомобиля: " . $stmt->error;
        }
        $stmt->close();

        // Обновляем статус резервации, если есть и статус валиден
        if ($reservation && $reservation_status !== null && empty($error)) {
            $stmt = $conn->prepare("UPDATE reservations SET payment_status = ? WHERE id = ?");
            $stmt->bind_param("si", $reservation_status, $reservation['id']);
            if (!$stmt->execute()) {
                $error = "Ошибка обновления резервации: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    if (empty($error)) {
        $success = 'Данные успешно обновлены.';
        $show_success_message = true;

        // Обновляем данные для отображения в форме
        $car['brand'] = $brand;
        $car['model'] = $model;
        $car['production_year'] = $production_year;
        $car['engine_volume'] = $engine_volume;
        $car['color'] = $color;
        $car['mileage'] = $mileage;
        $car['price'] = $price;
        $car['status'] = $status;
        $car['description'] = $description;
        $car['image_path'] = $image_path;

        if ($reservation && in_array($reservation_status, $valid_reservation_statuses, true)) {
            $reservation['payment_status'] = $reservation_status;
        }
    }
}

// Показываем сообщение об успехе только если был POST и успешное сохранение
if (!$show_success_message) {
    $success = '';
}

include 'includes/header.php';
?>

    <style>
        .car-details-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        /* Секция с изображением */
        .car-image-section {
            position: relative;
        }

        .car-image-main {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
            max-height: 400px;
            border: 1px solid #e0e0e0;
        }

        .thumbnail-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .thumbnail {
            width: 80px;
            height: 60px;
            border-radius: 4px;
            object-fit: cover;
            cursor: pointer;
            border: 1px solid #ddd;
        }

        .thumbnail:hover {
            border-color: #c50c0c;
        }

        /* Секция с информацией */
        .car-info-section {
            padding: 0 15px;
        }

        .car-title {
            font-size: 28px;
            margin: 0 0 5px 0;
            color: #333;
        }

        .car-subtitle {
            font-size: 18px;
            font-weight: normal;
            margin: 0 0 20px 0;
            color: #666;
        }

        /* Форма редактирования */
        .edit-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #c50c0c;
            outline: none;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Стили для статуса резервации */
        .reservation-status {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .reservation-status h3 {
            margin-top: 0;
            color: #333;
        }

        /* Кнопки */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #c50c0c;
            color: white;
            width: 280px;
        }

        .btn-primary:hover {
            background-color: #c50c0c;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        /* Уведомления */
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .car-details-container {
                grid-template-columns: 1fr;
            }
        }
    </style>


    <div class="car-details-page">
        <div class="container">
            <div class="car-details-container">
                <!-- Секция с изображением -->
                <div class="car-image-section">
                    <?php
                    $img_path = $car['image_path'] ?? 'uploads/cars/default.png';
                    if (!file_exists($img_path)) {
                        $img_path = 'uploads/cars/default.png';
                    }
                    ?>
                    <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($car['brand'].' '.$car['model']) ?>" class="car-image-main">

                    <form method="POST" enctype="multipart/form-data" class="edit-form">
                        <div class="form-group">
                            <label>Заменить фото:</label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                </div>

                <!-- Секция с формой редактирования -->
                <div class="car-info-section">
                    <h1 class="car-title">Редактирование автомобиля</h1>
                    <h2 class="car-subtitle">ID: <?= $car_id ?></h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Марка:</label>
                        <input type="text" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Модель:</label>
                        <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Год выпуска:</label>
                        <input type="number" name="production_year" value="<?= htmlspecialchars($car['production_year']) ?>" min="1900" max="<?= date('Y') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Объем двигателя (л):</label>
                        <input type="number" step="0.1" name="engine_volume" value="<?= htmlspecialchars($car['engine_volume']) ?>" min="0.5" max="10" required>
                    </div>

                    <div class="form-group">
                        <label>Цвет:</label>
                        <input type="text" name="color" value="<?= htmlspecialchars($car['color']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Пробег (км):</label>
                        <input type="number" name="mileage" value="<?= htmlspecialchars($car['mileage']) ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Цена (руб):</label>
                        <input type="number" name="price" value="<?= htmlspecialchars($car['price']) ?>" min="0" step="1000">
                    </div>

                    <div class="form-group">
                        <label>Статус автомобиля:</label>
                        <select name="car_status" required>
                            <option value="available" <?= $car['status'] === 'available' ? 'selected' : '' ?>>Доступен</option>
                            <option value="reserved" <?= $car['status'] === 'reserved' ? 'selected' : '' ?>>Зарезервирован</option>
                            <option value="sold" <?= $car['status'] === 'sold' ? 'selected' : '' ?>>Продан</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea name="description" rows="4"><?= htmlspecialchars($car['description']) ?></textarea>
                    </div>


                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        <a href="employee_panel.php" class="btn btn-secondary">Отмена</a>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


