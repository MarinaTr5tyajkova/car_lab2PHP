<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Получаем данные пользователя
$stmt = $conn->prepare("SELECT full_name, email, phone, login, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Пользователь не найден");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $login = trim($_POST['login']);

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Валидация данных
    if (empty($full_name) || empty($email) || empty($login)) {
        $error = "Пожалуйста, заполните обязательные поля";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный формат email";
    } elseif ($new_password !== $confirm_password) {
        $error = "Новый пароль и подтверждение не совпадают";
    } else {
        // Проверка пароля только если введен старый пароль
        if (!empty($old_password)) {
            if (!password_verify($old_password, $user['password'])) {
                $error = "Старый пароль неверен";
            } elseif (empty($new_password)) {
                $error = "Введите новый пароль";
            }
        }

        if (!$error) {
            try {
                $conn->begin_transaction();

                if (!empty($new_password)) {
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, login = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssssi", $full_name, $email, $phone, $login, $new_password_hash, $user_id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, login = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $full_name, $email, $phone, $login, $user_id);
                }

                if ($stmt->execute()) {
                    $conn->commit();
                    $_SESSION['success_message'] = "Данные успешно обновлены";
                    header("Location: profile.php");
                    exit();
                } else {
                    throw new Exception("Ошибка обновления данных: " . $stmt->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                $error = $e->getMessage();
            } finally {
                $stmt->close();
            }
        }
    }
}

// Отображаем сообщения из сессии
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

include 'includes/header.php';
include 'templates/profile.php';
include 'templates/footer.php';
?>