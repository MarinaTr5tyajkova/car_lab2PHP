<div class="container">
    <div class="auth-form">
        <h2>Авторизация</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-log">Войти</button>

            <div class="link-text">
                Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
            </div>
        </form>
    </div>
</div>
