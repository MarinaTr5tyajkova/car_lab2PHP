<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Запускаем сессию, если она ещё не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

checkAuth(['admin']); // Только администратор может заходить

$success = null;
$error = null;

// Обработка добавления сотрудника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    if ($login && $password && $full_name && $email && $phone && in_array($role, ['employee', 'admin'], true)) {
        // Проверка уникальности логина
        $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
        if ($stmt === false) {
            $error = "Ошибка подготовки запроса: " . $conn->error;
        } else {
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Пользователь с таким логином уже существует";
                $stmt->close();
            } else {
                $stmt->close();

                // Хешируем пароль (лучше использовать password_hash)
                $password_hash = md5($password);

                $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, email, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                if ($stmt === false) {
                    $error = "Ошибка подготовки запроса: " . $conn->error;
                } else {
                    $stmt->bind_param("ssssss", $login, $password_hash, $full_name, $email, $phone, $role);

                    if ($stmt->execute()) {
                        $success = "Сотрудник успешно добавлен";
                    } else {
                        $error = "Ошибка при добавлении сотрудника: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    } else {
        $error = "Пожалуйста, заполните все поля корректно";
    }
}

// Обработка удаления сотрудника
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    if ($delete_id === (int)($_SESSION['user_id'] ?? 0)) {
        $error = "Вы не можете удалить самого себя";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt === false) {
            $error = "Ошибка подготовки запроса: " . $conn->error;
        } else {
            $stmt->bind_param("i", $delete_id);

            if ($stmt->execute()) {
                $success = "Сотрудник удалён";
            } else {
                $error = "Ошибка удаления: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Получаем список сотрудников
$sql = "SELECT * FROM users WHERE role IN ('employee', 'admin')";
$result = $conn->query($sql);
$employees = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Подключаем шаблон
include 'includes/header.php';
include 'templates/admin_panel.php';

