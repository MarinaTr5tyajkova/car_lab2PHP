<?php

function getUserActiveReservations($conn, $user_id) {
    $user_id = (int)$user_id;
    $sql = "SELECT car_id FROM reservations WHERE user_id = $user_id AND payment_status = 'pending'";
    $result = $conn->query($sql);
    $reservations = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row['car_id'];
        }
    }
    return $reservations;
}


function createReservation($conn, $user_id, $car_id, $client_full_name = null, $client_passport = null, $client_phone = null) {
    $conn->begin_transaction();

    try {
        // Обновляем статус автомобиля на 'reserved', если он доступен
        $stmt = $conn->prepare("UPDATE cars SET status = 'reserved' WHERE id = ? AND status = 'available'");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception("Автомобиль уже зарезервирован или недоступен");
        }
        $stmt->close();

        $reservation_date = date('Y-m-d H:i:s');
        $expiration_date = date('Y-m-d H:i:s', strtotime('+3 days'));

        $stmt = $conn->prepare("INSERT INTO reservations (car_id, user_id, reservation_date, expiration_date, payment_status, client_full_name, client_passport, client_phone) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?)");
        $stmt->bind_param("iisssss", $car_id, $user_id, $reservation_date, $expiration_date, $client_full_name, $client_passport, $client_phone);
        $stmt->execute();

        $reservation_id = $stmt->insert_id;
        $stmt->close();

        $conn->commit();
        return $reservation_id;

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function cancelReservation($conn, $user_id, $car_id) {
    $conn->begin_transaction();

    try {
        // Получаем ID активной резервации
        $stmt = $conn->prepare("SELECT id FROM reservations WHERE user_id = ? AND car_id = ? AND payment_status = 'pending'");
        $stmt->bind_param("ii", $user_id, $car_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation = $result->fetch_assoc();
        $stmt->close();

        if (!$reservation) {
            throw new Exception("Активная резервация не найдена");
        }

        // Удаляем резервацию
        $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->bind_param("i", $reservation['id']);
        $stmt->execute();
        $stmt->close();

        // Обновляем статус автомобиля на 'available'
        $stmt = $conn->prepare("UPDATE cars SET status = 'available' WHERE id = ?");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}
