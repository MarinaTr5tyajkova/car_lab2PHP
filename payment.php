<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

if (!isset($_GET['reservation_id']) || !is_numeric($_GET['reservation_id'])) {
    die("Резервация не найдена");
}

$reservation_id = (int)$_GET['reservation_id'];

// Получаем данные резервации с автомобилем
$sql = "SELECT r.*, c.price FROM reservations r JOIN cars c ON r.car_id = c.id WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();
$stmt->close();

if (!$reservation) die("Резервация не найдена");

// Имитация оплаты
$message = '';
if ($reservation['payment_status'] === 'pending') {
    $payment_id = 'KSMPE_' . uniqid();
    $stmt = $conn->prepare("UPDATE reservations SET payment_status = 'paid', payment_id = ? WHERE id = ?");
    $stmt->bind_param("si", $payment_id, $reservation_id);
    $stmt->execute();
    $stmt->close();
    $message = "Оплата прошла успешно! Номер платежа: $payment_id";
} else {
    $message = "Платеж уже проведён";
}

include 'templates/header.php';
?>

<h2>Оплата</h2>
<p><?= htmlspecialchars($message) ?></p>
<a href="profile.php" class="btn">В профиль</a>

<?php include 'templates/footer.php'; ?>
