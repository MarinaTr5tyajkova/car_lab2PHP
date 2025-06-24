<h2>Панель администратора</h2>

<div class="admin-sections">

    <?php if ($success): ?>
        <div class="success" style="color: green;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error" style="color: red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section>
        <h3>Добавить сотрудника</h3>
        <form method="POST" novalidate>
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="text" name="full_name" placeholder="ФИО" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="phone" placeholder="Телефон" required>
            <select name="role" required>
                <option value="employee">Сотрудник</option>
                <option value="admin">Администратор</option>
            </select>
            <button type="submit" name="add_employee">Добавить</button>
        </form>
    </section>

    <section>
        <h3>Список сотрудников</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>ФИО</th>
                <th>Логин</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($employees)): ?>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?= htmlspecialchars($employee['full_name']) ?></td>
                        <td><?= htmlspecialchars($employee['login']) ?></td>
                        <td><?= htmlspecialchars($employee['email']) ?></td>
                        <td><?= htmlspecialchars($employee['phone']) ?></td>
                        <td><?= $employee['role'] === 'admin' ? 'Администратор' : 'Сотрудник' ?></td>
                        <td>
                            <a href="edit_employee.php?id=<?= (int)$employee['id'] ?>">Редактировать</a> |
                            <?php if ((int)$employee['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                <a href="admin_panel.php?delete_id=<?= (int)$employee['id'] ?>" onclick="return confirm('Удалить сотрудника?')">Удалить</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Сотрудники не найдены.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

</div>
