<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/user_functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'login' => trim($_POST['login'] ?? ''),
        'password' => trim($_POST['password'] ?? ''),
        'confirm_password' => trim($_POST['confirm_password'] ?? ''),
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? '')
    ];

    // Валидация
    if (empty($data['login'])) $errors[] = 'Логин обязателен';
    if (strlen($data['password']) < 6) $errors[] = 'Пароль слишком короткий';
    if ($data['password'] !== $data['confirm_password']) $errors[] = 'Пароли не совпадают';

    if (empty($errors)) {
        if (getUserByLogin($conn, $data['login'])) {
            $errors[] = 'Логин занят';
        } else {
            $user_id = registerUser($conn, $data['login'], $data['password'], $data['full_name'], $data['email'], $data['phone']);
            if ($user_id) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = 'user';
                header("Location: profile.php");
                exit();
            } else {
                $errors[] = 'Ошибка регистрации';
            }
        }
    }
}


include 'includes/header.php';
include 'templates/register.php';

