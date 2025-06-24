<div class="cars-grid">
    <?php if (empty($cars)): ?>
        <p>Автомобили не найдены.</p>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <div class="car-card" onclick="location.href='car_details.php?id=<?= htmlspecialchars($car['id']) ?>'">
                <div class="car-image">
                    <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="Фото <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                    <div class="price-label"><?= number_format($car['price'], 0, '', ' ') ?> ₽</div>
                </div>

                <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?>, <?= (int)$car['production_year'] ?></h3>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="car-actions" onclick="event.stopPropagation();">
                        <?php if (in_array($car['id'], $user_reservations)): ?>
                            <span class="reserved-label">Зарезервирован</span>
                            <a href="cancel_reservation.php?car_id=<?= htmlspecialchars($car['id']) ?>" class="btn btn-danger"
                               onclick="return confirm('Отменить резерв на этот автомобиль?')">Отменить резерв</a>
                        <?php elseif ($car['status'] === 'available'): ?>
                            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'admin'], true)): ?>
                                <button class="btn btn-primary" onclick="openModal(<?= htmlspecialchars($car['id']) ?>)">Зарезервировать</button>

                                <!-- Модальное окно -->
                                <div class="modal" id="modal-<?= htmlspecialchars($car['id']) ?>">
                                    <div class="modal-content" onclick="event.stopPropagation();">
                                        <span class="modal-close" onclick="closeModal(<?= htmlspecialchars($car['id']) ?>)">&times;</span>
                                        <h3>Резервировать автомобиль</h3>
                                        <form method="POST" action="reserve.php" class="reserve-form" onsubmit="return validateForm(<?= htmlspecialchars($car['id']) ?>)">
                                            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['id']) ?>">
                                            <div class="custom-reserve-field">
                                                <label for="client_full_name_<?= htmlspecialchars($car['id']) ?>" class="custom-reserve-label">ФИО клиента:</label>
                                                <input type="text" id="client_full_name_<?= htmlspecialchars($car['id']) ?>" name="client_full_name" required class="custom-reserve-input">
                                            </div>
                                            <div class="custom-reserve-field">
                                                <label for="client_phone_<?= htmlspecialchars($car['id']) ?>" class="custom-reserve-label">Номер телефона:</label>
                                                <input type="tel" id="client_phone_<?= htmlspecialchars($car['id']) ?>" name="client_phone" required pattern="^\+?\d{10,15}$" placeholder="+71234567890" class="custom-reserve-input">
                                            </div>
                                            <div class="custom-reserve-field">
                                                <label for="client_email_<?= htmlspecialchars($car['id']) ?>" class="custom-reserve-label">Email клиента:</label>
                                                <input type="email" id="client_email_<?= htmlspecialchars($car['id']) ?>" name="client_email" required class="custom-reserve-input">
                                            </div>

                                            <button type="submit" class="btn btn-primary">Подтвердить резерв</button>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="reserve.php?car_id=<?= htmlspecialchars($car['id']) ?>" class="btn btn-primary">Зарезервировать</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="unavailable-label">Автомобиль недоступен для резервации</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById('modal-' + id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById('modal-' + id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Закрытие по клику вне контента
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Валидация формы
    function validateForm(id) {
        const fullName = document.getElementById('client_full_name_' + id);
        const phone = document.getElementById('client_phone_' + id);
        const email = document.getElementById('client_email_' + id);

        if (!fullName.value.trim()) {
            alert('Пожалуйста, введите ФИО клиента');
            fullName.focus();
            return false;
        }

        const phonePattern = /^\+?\d{10,15}$/;
        if (!phonePattern.test(phone.value.trim())) {
            alert('Введите корректный номер телефона (10-15 цифр, может начинаться с +)');
            phone.focus();
            return false;
        }

        if (!email.value.trim() || !email.checkValidity()) {
            alert('Введите корректный email');
            email.focus();
            return false;
        }

        return true;
    }
</script>
