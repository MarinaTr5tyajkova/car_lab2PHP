<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/user_functions.php'; // здесь уже есть getUserByLogin()

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Создаем админа при необходимости
createAdminAccount($conn);

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($login) || empty($password)) {
        $error = 'Все поля обязательны';
    } else {
        $user = getUserByLogin($conn, $login);

        if ($user && md5($password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['login'] = $user['login'];

            header("Location: " . ($user['role'] === 'admin' ? 'admin_panel.php' : 'profile.php'));
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}

include 'includes/header.php';
include 'templates/login.php';
