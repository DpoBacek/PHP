<?php
session_start();
require_once '../layout/config.php';
$page_title = "История заказов";
require_once '../layout/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

$query = "
    SELECT o.id AS order_id, o.address, o.order_date, o.status, 
           od.quantity, p.price, i.file_name AS image
    FROM orders o
    JOIN order_details od ON o.id = od.order_id
    JOIN products p ON od.product_id = p.id
    LEFT JOIN images i ON p.image_id = i.id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
";
$result = $mysqli->query($query);
?>

<main class="container">
    <h1>История заказов</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Адрес</th>
                    <th>Дата заказа</th>
                    <th>Статус</th>
                    <th>Товар</th>
                    <th>Количество</th>
                    <th>Цена</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['address']) ?></td>
                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td>
                            <?php if ($order['image']): ?>
                                <img src="../images/<?= htmlspecialchars($order['image']) ?>" alt="Товар" style="width: 100%; height: 100px;"  onerror="this.onerror=null; this.src='../cart/DefaultPicture.png';">
                            <?php else: ?>
                                <span>Без изображения</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($order['quantity']) ?></td>
                        <td><?= htmlspecialchars(number_format($order['price'], 2)) ?> ₽</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>У вас пока нет заказов.</p>
    <?php endif; ?>
</main>

<?php require_once '../layout/footer.php'; ?>