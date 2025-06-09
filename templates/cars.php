
<h2>Каталог автомобилей</h2>

<div class="cars-grid">
    <?php if (empty($cars)): ?>
        <p>Автомобили не найдены.</p>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <div class="car-card">
                <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                <ul>
                    <li>Год: <?= $car['production_year'] ?></li>
                    <li>Объем: <?= $car['engine_volume'] ?> л</li>
                    <li>Пробег: <?= number_format($car['mileage'], 0, '', ' ') ?> км</li>
                    <li>Цена: <?= number_format($car['price'], 0, '', ' ') ?> ₽</li>
                </ul>

                <a href="car_details.php?id=<?= $car['id'] ?>" class="btn">Подробнее</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (in_array($car['id'], $user_reservations)): ?>
                        <a href="cancel_reservation.php?car_id=<?= $car['id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Отменить резерв на этот автомобиль?')">Отменить резерв</a>
                    <?php elseif ($car['status'] === 'available'): ?>
                        <a href="reserve.php?id=<?= $car['id'] ?>" class="btn">Зарезервировать</a>
                    <?php else: ?>
                        <span>Автомобиль недоступен для резервации</span>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Для резервации <a href="login.php">войдите</a> в систему</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
