<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkAuth($roles = []) {
    if (!isset($_SESSION['user_id'])) {  // Здесь была пропущена закрывающая скобка
        header('Location: /templates/login.php');
        exit();
    }

    if (!empty($roles) && !in_array($_SESSION['role'], (array)$roles)) {
        header('Location: /templates/403.php');
        exit();
    }
}

function isAdmin() {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function isEmployee() {
    return ($_SESSION['role'] ?? '') === 'employee';
}