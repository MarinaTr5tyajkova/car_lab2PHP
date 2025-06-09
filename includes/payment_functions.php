<?php
/**
 * Обновить статус оплаты резервации
 * @param mysqli $conn
 * @param int $reservation_id
 * @param string $payment_id
 * @return bool
 */
function markReservationPaid($conn, $reservation_id, $payment_id) {
    $stmt = $conn->prepare("UPDATE reservations SET payment_status = 'paid', payment_id = ? WHERE id = ?");
    $stmt->bind_param("si", $payment_id, $reservation_id);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}
