<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/user_functions.php'; // здесь уже есть getUserByLogin()

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    $user = getUserByLogin($conn, $login);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            header("Location: profile.php");
            exit();
        } else {
            $error = 'Неверный пароль';
        }
    } else {
        $error = 'Пользователь с таким логином не найден';
    }
}

include 'includes/header.php';
include 'templates/login.php';
include 'includes/footer.php';
