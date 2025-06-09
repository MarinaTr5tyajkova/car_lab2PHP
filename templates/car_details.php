<div class="car-details">
    <h2><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h2>

    <?php if (!empty($car['image_path'])): ?>
        <div class="car-image">
            <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="<?= htmlspecialchars($car['brand']) ?>">
        </div>
    <?php endif; ?>

    <ul>
        <li>Год выпуска: <?= $car['production_year'] ?></li>
        <li>Объем двигателя: <?= $car['engine_volume'] ?> л</li>
        <li>Цвет: <?= htmlspecialchars($car['color']) ?></li>
        <li>Пробег: <?= number_format($car['mileage'], 0, '', ' ') ?> км</li>
        <li>Цена: <?= number_format($car['price'], 0, '', ' ') ?> ₽</li>
        <li>Статус:
            <?= $car['status'] === 'available' ? 'Доступен' : ($car['status'] === 'reserved' ? 'Зарезервирован' : 'Продан') ?>
        </li>
    </ul>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($user_has_reservation): ?>
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

    <?php if (!empty($car['description'])): ?>
        <h3>Описание</h3>
        <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
    <?php endif; ?>
</div>
