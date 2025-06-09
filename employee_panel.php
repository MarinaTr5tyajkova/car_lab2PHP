<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/car_functions.php';
require_once 'includes/reservation_functions.php';

checkAuth(['employee', 'admin']); // Работник и админ могут сюда заходить

// Получаем список всех автомобилей
$cars = getAllCars($conn);

// Логика добавления/редактирования автомобилей и работы с заявками

include 'includes/header.php';
include 'templates/employee_panel.php';
include 'templates/footer.php';
