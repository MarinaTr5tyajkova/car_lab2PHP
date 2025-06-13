<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

$user_id = $_SESSION['user_id'];

$sql = "SELECT r.*, c.brand, c.model, c.id as car_id, 
               r.reservation_date as date  /* используем reservation_date как date */
        FROM reservations r 
        JOIN cars c ON r.car_id = c.id 
        WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include 'includes/header.php';
include 'templates/my_reservations.php';
include 'templates/footer.php';
