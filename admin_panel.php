<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
checkAuth('admin'); // Только администратор может заходить

// Обработка добавления сотрудника
if (isset($_POST['add_employee'])) {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    // Валидация (можно расширить)
    if ($login && $password && $full_name && $email && $phone && in_array($role, ['employee', 'admin'])) {
        // Проверяем, что логин уникален
        $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Пользователь с таким логином уже существует";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $login, $password_hash, $full_name, $email, $phone, $role);
            if ($stmt->execute()) {
                $success = "Сотрудник успешно добавлен";
            } else {
                $error = "Ошибка при добавлении сотрудника: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $error = "Пожалуйста, заполните все поля корректно";
    }
}

// Обработка удаления сотрудника
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Защита: нельзя удалить самого себя
    if ($delete_id === $_SESSION['user_id']) {
        $error = "Вы не можете удалить самого себя";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success = "Сотрудник удалён";
        } else {
            $error = "Ошибка удаления: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Получаем обновлённый список сотрудников
$sql = "SELECT * FROM users WHERE role IN ('employee', 'admin')";
$result = $conn->query($sql);
$employees = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include 'includes/header.php';
include 'templates/admin_panel.php';
include 'templates/footer.php';
