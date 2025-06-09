<?php
/**
 * Получить пользователя по логину
 */
function getUserByLogin($conn, $login) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

/**
 * Зарегистрировать нового пользователя
 */
function registerUser($conn, $login, $password, $full_name, $email, $phone, $role = 'user') {
    $hashed_password = md5($password); // Используем MD5
    $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, email, created_at, phone, role) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("ssssss", $login, $hashed_password, $full_name, $email, $phone, $role);
    return $stmt->execute() ? $stmt->insert_id : false;
}

/**
 * Создать администратора
 */
function createAdminAccount($conn) {
    $check = $conn->query("SELECT id FROM users WHERE login = 'admin' LIMIT 1");
    if ($check->num_rows === 0) {
        $password = 'admin123'; // Пароль по умолчанию
        $hashed_password = md5($password);
        $conn->query("INSERT INTO users (login, password, full_name, email, phone, role, created_at) 
                      VALUES ('admin', '$hashed_password', 'Администратор', 'admin@example.com', '+123456789', 'admin', NOW())");
    }
}