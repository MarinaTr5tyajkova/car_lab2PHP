<div class="container">
    <h2>Панель работника</h2>

    <div class="employee-sections">
        <!-- Форма добавления автомобиля -->
        <section class="add-car-form">
            <h3>Добавить новый автомобиль</h3>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Автомобиль успешно добавлен!</div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Марка:</label>
                    <input type="text" name="brand" required>
                </div>

                <div class="form-group">
                    <label>Модель:</label>
                    <input type="text" name="model" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Год выпуска:</label>
                        <input type="number" name="year" min="1900" max="<?= date('Y') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Объем двигателя (л):</label>
                        <input type="number" step="0.1" name="engine" min="0.5" max="10" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Цвет:</label>
                        <input type="text" name="color" required>
                    </div>

                    <div class="form-group">
                        <label>Пробег (км):</label>
                        <input type="number" name="mileage" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Цена (руб):</label>
                    <input type="number" name="price" min="0" step="1000" required>
                </div>

                <div class="form-group">
                    <label>Описание:</label>
                    <textarea name="description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Фото автомобиля:</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <button type="submit" name="add_car" class="btn">Добавить автомобиль</button>
            </form>
        </section>

        <!-- Список автомобилей -->
        <section class="cars-list">
            <h3>Все автомобили (<?= count($cars) ?>)</h3>

            <div class="cars-table">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Марка</th>
                        <th>Модель</th>
                        <th>Год</th>
                        <th>Цена</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?= $car['id'] ?></td>
                            <td><?= htmlspecialchars($car['brand']) ?></td>
                            <td><?= htmlspecialchars($car['model']) ?></td>
                            <td><?= $car['production_year'] ?></td>
                            <td><?= number_format($car['price'], 0, '', ' ') ?> ₽</td>
                            <td>
                                <span class="status-badge <?= $car['status'] ?>">
                                    <?= $car['status'] === 'available' ? 'Доступен' :
                                        ($car['status'] === 'reserved' ? 'Зарезервирован' : 'Продан') ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn-small">Редакт.</a>
                                <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn-small btn-danger"
                                   onclick="return confirm('Удалить этот автомобиль?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>


