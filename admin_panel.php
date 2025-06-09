<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

checkAuth('admin'); // Только админ может сюда заходить

// Получаем список сотрудников (employee и admin)
$sql = "SELECT * FROM users WHERE role IN ('employee', 'admin')";
$result = $conn->query($sql);
$employees = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Логика добавления, редактирования, удаления сотрудников
// Например, обработка POST-запросов для добавления нового работника

include 'includes/header.php';
include 'templates/admin_panel.php';
include 'templates/footer.php';
