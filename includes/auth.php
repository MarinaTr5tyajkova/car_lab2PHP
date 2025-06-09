<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function checkAuth($roles = []) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: ../templates/login.php');
        exit();
    }
    if (!empty($roles)) {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        if (!in_array($_SESSION['role'], $roles)) {
            // Если роль не подходит, можно редиректить или показывать ошибку
            header('HTTP/1.1 403 Forbidden');
            echo 'Доступ запрещён.';
            exit();
        }
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employee';
}
