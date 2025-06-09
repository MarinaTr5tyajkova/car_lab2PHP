<?php
require_once 'includes/config.php';
require_once 'includes/user_functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($login)) $errors[] = 'Логин обязателен';
    if (empty($password)) $errors[] = 'Пароль обязателен';
    if ($password !== $confirm_password) $errors[] = 'Пароли не совпадают';
    if (empty($full_name)) $errors[] = 'ФИО обязательно';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
    if (empty($phone)) $errors[] = 'Телефон обязателен';

    // Проверка уникальности логина
    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = 'Логин уже занят';
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_id = registerUser($conn, $login, $hashed_password, $full_name, $email, $phone);
        if ($user_id) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = 'user';
            $_SESSION['full_name'] = $full_name;
            header("Location: profile.php");
            exit();
        } else {
            $errors[] = 'Ошибка при регистрации';
        }
    }
}

include 'includes/header.php';
include 'templates/register.php';
include 'templates/footer.php';

