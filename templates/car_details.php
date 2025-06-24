<div class="car-details-page">
    <div class="container">
        <div class="car-details-container">
            <!-- Секция с изображением -->
            <div class="car-image-section">
                <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="<?= htmlspecialchars($car['brand'].' '.$car['model']) ?>" class="car-image-main">

                <div class="thumbnail-container">
                    <!-- Дополнительные миниатюры (если есть) -->
                    <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="Фото 1" class="thumbnail">
                    <!-- Можно добавить больше миниатюр -->
                </div>
            </div>

            <!-- Секция с информацией -->
            <div class="car-info-section">
                <h1 class="car-title"><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h1>
                <h2 class="car-subtitle"><?= $car['production_year'] ?> год, <?= $car['mileage'] ?> км</h2>

                <div class="price-tag"><?= number_format($car['price'], 0, '', ' ') ?> ₽</div>

                <!-- Характеристики -->
                <div class="car-specs">
                    <div class="spec-row">
                        <div class="spec-label">Объем двигателя</div>
                        <div class="spec-value"><?= $car['engine_volume'] ?> л</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Цвет</div>
                        <div class="spec-value"><?= $car['color'] ?></div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Пробег</div>
                        <div class="spec-value"><?= $car['mileage'] ?> км</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Год выпуска</div>
                        <div class="spec-value"><?= $car['production_year'] ?></div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Статус</div>
                        <div class="spec-value">
                            <?= $car['status'] === 'available' ? 'Доступен' :
                                ($car['status'] === 'reserved' ? 'Зарезервирован' : 'Продан') ?>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="car-description">
                    <h3>Описание</h3>
                    <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
                </div>

                <!-- Кнопки действий -->
                <div class="car-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (in_array($car['id'], $user_reservations)): ?>
                            <a href="cancel_reservation.php?car_id=<?= $car['id'] ?>" class="btn-cancel"
                               onclick="return confirm('Отменить резерв на этот автомобиль?')">Отменить резерв</a>
                        <?php elseif ($car['status'] === 'available'): ?>
                            <a href="reserve.php?id=<?= $car['id'] ?>" class="btn-reserve">Зарезервировать</a>
                        <?php else: ?>
                            <div class="status-message">Автомобиль недоступен для резервации</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="status-message">
                            Для резервации автомобиля <a href="login.php" style="color: #E94E1B;">авторизуйтесь</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Обработка резервации
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-reserve')) {
                const button = e.target;
                const carId = button.dataset.carId;
                const carCard = button.closest('.car-card');

                fetch(`reserve.php?car_id=${carId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Обновляем карточку
                            carCard.classList.add('reserved');
                            button.remove();

                            const badge = document.createElement('div');
                            badge.className = 'reserved-badge';
                            badge.textContent = 'Зарезервирован';
                            carCard.querySelector('.car-image').appendChild(badge);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        alert('Ошибка соединения');
                    });
            }
        });
    });

        // Функция для показа модального окна
        function showModal(title, message) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="modal-close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn modal-close-btn">OK</button>
                </div>
            </div>
        `;

            document.body.appendChild(modal);

            // Закрытие модального окна
            const closeBtns = modal.querySelectorAll('.modal-close-btn');
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.body.removeChild(modal);
                });
            });
        }

        // Функция для обновления статуса резервации
        function updateReservationStatus(isReserved) {
            const reserveBtn = document.querySelector('.btn-reserve');
            const cancelBtn = document.querySelector('.btn-cancel');
            const statusMsg = document.querySelector('.status-message');

            if (isReserved) {
                if (reserveBtn) reserveBtn.style.display = 'none';
                if (cancelBtn) cancelBtn.style.display = 'block';
                if (statusMsg) statusMsg.textContent = 'Вы успешно забронировали этот автомобиль';
            }
        }
    });
</script>

<style>.modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        animation: modalFadeIn 0.3s ease;
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        text-align: right;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }</style>