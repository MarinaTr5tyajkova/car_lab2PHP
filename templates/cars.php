<div class="cars-grid">
    <?php if (empty($cars)): ?>
        <p>Автомобили не найдены.</p>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <div class="car-card" onclick="location.href='car_details.php?id=<?= $car['id'] ?>'">
                <div class="car-image">
                    <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="Фото <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                    <div class="price-label"><?= number_format($car['price'], 0, '', ' ') ?> ₽</div>
                </div>

                <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?>, <?= $car['production_year'] ?></h3>

                <!-- Остальной контент карточки (кнопки, сообщения) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="car-actions">
                        <?php if (in_array($car['id'], $user_reservations)): ?>
                            <a href="cancel_reservation.php?car_id=<?= $car['id'] ?>" class="btn btn-danger"
                               onclick="event.stopPropagation(); return confirm('Отменить резерв на этот автомобиль?')">Отменить резерв</a>
                        <?php elseif ($car['status'] === 'available'): ?>
                            <a href="reserve.php?id=<?= $car['id'] ?>" class="btn" onclick="event.stopPropagation()">Зарезервировать</a>
                        <?php else: ?>
                            <span>Автомобиль недоступен для резервации</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>
