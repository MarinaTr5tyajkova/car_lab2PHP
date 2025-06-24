<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Проверяет авторизацию пользователя и роль.
 *
 * @param array $roles Список разрешённых ролей. Если пустой — проверяется только авторизация.
 */
function checkAuth(array $roles = []): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /templates/login.php');
        exit();
    }

    if (!empty($roles)) {
        // Проверяем, что роль установлена и входит в список разрешённых
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true)) {
            header('Location: /templates/403.php');
            exit();
        }
    }
}

/**
 * Проверяет, является ли пользователь администратором.
 *
 * @return bool
 */
function isAdmin(): bool {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

/**
 * Проверяет, является ли пользователь сотрудником.
 *
 * @return bool
 */
function isEmployee(): bool {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'employee');
}

/**
 * Проверяет, является ли пользователь обычным пользователем.
 *
 * @return bool
 */
function isUser(): bool {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'user');
}
?>
