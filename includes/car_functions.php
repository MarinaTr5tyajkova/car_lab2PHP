<?php
/**
 * Получить все автомобили из базы
 * @param mysqli $conn
 * @return array
 */
function getAllCars($conn) {
    $sql = "SELECT * FROM cars ORDER BY price ASC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Получить автомобиль по ID
 * @param mysqli $conn
 * @param int $car_id
 * @return array|null
 */
function getCarById($conn, $car_id) {
    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $stmt->close();
    return $car ?: null;
}

/**
 * Обновить статус автомобиля
 * @param mysqli $conn
 * @param int $car_id
 * @param string $status
 * @return bool
 */
function updateCarStatus($conn, $car_id, $status) {
    $stmt = $conn->prepare("UPDATE cars SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $car_id);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}
