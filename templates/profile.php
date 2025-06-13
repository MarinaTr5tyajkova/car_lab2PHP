<div class="profile-container">
    <!-- Информация о пользователе -->
    <div class="profile-info">
        <h2>Мой профиль</h2>
        <p><strong>Логин:</strong> <?= htmlspecialchars($user['login']) ?></p>
        <p><strong>ФИО:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone'] ?? 'не указан') ?></p>
    </div>

    <!-- Кнопка редактирования -->
    <button id="openProfileModal" class="user-settings-btn">Редактировать профиль</button>

    <!-- Модальное окно редактирования -->
    <div id="profileModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Редактирование профиля</h3>
                <button id="closeProfileModal" class="modal-close-btn">&times;</button>
            </div>

            <div class="modal-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form id="profileForm" method="POST" class="user-settings-form">
                    <div class="form-group">
                        <label class="user-settings-label">
                            Логин*
                            <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>"
                                   required class="user-settings-input">
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="user-settings-label">
                            ФИО*
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>"
                                   required class="user-settings-input">
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="user-settings-label">
                            Email*
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                                   required class="user-settings-input">
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="user-settings-label">
                            Телефон
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   class="user-settings-input">
                        </label>
                    </div>

                    <div class="form-section">
                        <h4>Смена пароля (необязательно)</h4>

                        <div class="form-group">
                            <label class="user-settings-label">
                                Старый пароль
                                <input type="password" name="old_password" autocomplete="off"
                                       class="user-settings-input">
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="user-settings-label">
                                Новый пароль
                                <input type="password" name="new_password" autocomplete="off"
                                       class="user-settings-input">
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="user-settings-label">
                                Подтверждение пароля
                                <input type="password" name="confirm_password" autocomplete="off"
                                       class="user-settings-input">
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="user-settings-btn">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Управление модальным окном
        const modal = document.getElementById('profileModal');
        const openBtn = document.getElementById('openProfileModal');
        const closeBtn = document.getElementById('closeProfileModal');

        openBtn.addEventListener('click', () => modal.style.display = 'flex');
        closeBtn.addEventListener('click', () => modal.style.display = 'none');

        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });

        // Валидация формы
        const form = document.getElementById('profileForm');
        form.addEventListener('submit', function(e) {
            const newPass = form.querySelector('[name="new_password"]').value;
            const confirmPass = form.querySelector('[name="confirm_password"]').value;
            const oldPass = form.querySelector('[name="old_password"]').value;

            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('Пароли не совпадают!');
                return false;
            }

            if ((newPass || confirmPass) && !oldPass) {
                e.preventDefault();
                alert('Для смены пароля введите старый пароль');
                return false;
            }

            return true;
        });
    });
</script>