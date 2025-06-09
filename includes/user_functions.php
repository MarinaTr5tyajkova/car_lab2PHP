<?php
/**
 * Получить пользователя по логину
 * @param mysqli $conn
 * @param string $login
 * @return array|null
 */
function getUserByLogin($conn, $login) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user ?: null;
}

/**
 * Зарегистрировать нового пользователя
 * @param mysqli $conn
 * @param string $login
 * @param string $passwordHash
 * @param string $full_name
 * @param string $email
 * @param string $phone
 * @return int|false ID нового пользователя или false
 */
function registerUser($conn, $login, $passwordHash, $full_name, $email, $phone) {
    $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, 'user')");
    $stmt->bind_param("sssss", $login, $passwordHash, $full_name, $email, $phone);
    $res = $stmt->execute();
    if ($res) {
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }
    $stmt->close();
    return false;
}
