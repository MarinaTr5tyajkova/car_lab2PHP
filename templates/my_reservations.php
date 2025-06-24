<h2>Мои резервации</h2>

<?php if (empty($reservations)): ?>
    <p>У вас нет активных резерваций.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Автомобиль</th>
            <th>Дата резервации</th>
            <th>Статус оплаты</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= htmlspecialchars($res['brand']) ?> <?= htmlspecialchars($res['model']) ?></td>
                <td><?= htmlspecialchars($res['date']) ?></td>
                <td><?= htmlspecialchars($res['payment_status']) ?></td>
                <td>
                    <?php if ($res['payment_status'] === 'pending'): ?>
                        <a href="cancel_reservation.php?car_id=<?= $res['car_id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Отменить резервацию?')">Отменить</a>
                    <?php else: ?>
                        Оплачено
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
