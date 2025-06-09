<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

checkAuth('admin');

if (!isset($_GET['id'])) {
    die("ID сотрудника не указан");
}

$id = intval($_GET['id']);

// Получаем данные сотрудника
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    die("Сотрудник не найден");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    // Валидация (можно расширить)
    if ($full_name && $email && $phone && in_array($role, ['employee', 'admin'])) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $role, $id);

        if ($stmt->execute()) {
            $success = "Данные сотрудника обновлены";
            // Обновляем данные для формы
            $employee['full_name'] = $full_name;
            $employee['email'] = $email;
            $employee['phone'] = $phone;
            $employee['role'] = $role;
        } else {
            $error = "Ошибка обновления: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Пожалуйста, заполните все поля корректно";
    }
}

include 'includes/header.php';
?>

<h2>Редактирование сотрудника</h2>

<?php if (isset($success)): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <label>
        ФИО:<br>
        <input type="text" name="full_name" value="<?= htmlspecialchars($employee['full_name']) ?>" required>
    </label><br><br>

    <label>
        Email:<br>
        <input type="email" name="email" value="<?= htmlspecialchars($employee['email']) ?>" required>
    </label><br><br>

    <label>
        Телефон:<br>
        <input type="tel" name="phone" value="<?= htmlspecialchars($employee['phone']) ?>" required>
    </label><br><br>

    <label>
        Роль:<br>
        <select name="role" required>
            <option value="employee" <?= $employee['role'] === 'employee' ? 'selected' : '' ?>>Сотрудник</option>
            <option value="admin" <?= $employee['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
        </select>
    </label><br><br>

    <button type="submit">Сохранить изменения</button>
</form>

<p><a href="admin_panel.php">← Вернуться в панель администратора</a></p>

<?php
include 'includes/footer.php';
?>
