<?php
session_start();
require_once '../layout/config.php';
$page_title = "Управление заказами";
require_once '../layout/header.php';
requireAdmin();



// Получаем список заказов
$result = $mysqli->query("SELECT o.*, u.login AS user_login FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
?>

<main class="container">
    <h1>Управление заказами</h1>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Адрес</th>
                <th>Дата заказа</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['user_login']) ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>
                        <form method="POST" action="updateOrderStatus.php">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status">
                                <option value="В обработке" <?= $order['status'] === 'В обработке' ? 'selected' : '' ?>>В обработке</option>
                                <option value="Выполнено" <?= $order['status'] === 'Выполнено' ? 'selected' : '' ?>>Выполнено</option>
                                <option value="Отменено" <?= $order['status'] === 'Отменено' ? 'selected' : '' ?>>Отменено</option>
                            </select>
                            <button type="submit" class="btn primary">Обновить</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php require_once '../layout/footer.php'; ?>
