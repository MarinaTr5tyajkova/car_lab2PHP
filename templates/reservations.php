<div class="container">
    <h2>Список резерваций</h2>

    <?php if (empty($reservations)): ?>
        <p>Резервации отсутствуют.</p>
    <?php else: ?>
        <table class="table" border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead>
            <tr>
                <th>ID резервации</th>
                <th>Автомобиль</th>
                <th>Год выпуска</th>
                <th>Цена</th>
                <th>Пользователь</th>
                <th>Email</th>
                <th>Дата резервации</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res['reservation_id']) ?></td>
                    <td><?= htmlspecialchars($res['brand'] . ' ' . $res['model']) ?></td>
                    <td><?= htmlspecialchars($res['production_year']) ?></td>
                    <td><?= number_format($res['price'], 0, '', ' ') ?> ₽</td>
                    <td><?= htmlspecialchars($res['full_name']) ?></td>
                    <td><?= htmlspecialchars($res['email']) ?></td>
                    <td><?= htmlspecialchars($res['reservation_date']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
