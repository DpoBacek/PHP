<?php
session_start();
require_once '../layout/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0) {
        $quantity = 1;
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    header('Location: cart.php');
    exit();
}

$limit = isset($_SESSION['user']) ? "" : "LIMIT 1";
    $query = "SELECT p.*, i.file_name AS image FROM products p 
              LEFT JOIN images i ON p.image_id = i.id 
              ORDER BY p.created_at DESC $limit";

    if (!$result = $mysqli->query($query)) {
        throw new Exception("Ошибка запроса: " . $mysqli->error);
    }
    if (!$result->num_rows === 0) {
        echo "<p class='notice'>Товары отсутствуют</p>";
    } else {
        $cart_items = [];

        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $query = "SELECT p.*, i.file_name AS image FROM products p 
                      LEFT JOIN images i ON p.image_id = i.id 
                      WHERE p.id = $product_id";
            $result = $mysqli->query($query);
            if ($result && $result->num_rows > 0) {
                $item = $result->fetch_assoc();
                $item['quantity_in_cart'] = $quantity;
                $cart_items[] = $item;
            }
        }
    }
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <?php include '../layout/header.php'; ?>

    <main class="container">
        <h1>Корзина</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <p>Ваша корзина пуста.</p>
        <?php else: ?>
            <section class="products-grid">
                <?php foreach ($cart_items as $item): ?>
                    <article class="product-card">
                        <img src="../images/<?= htmlspecialchars($item['image']) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>"
                             loading="lazy"
                             width="100%"
                             height="300px">
                        <h2><?= htmlspecialchars($item['title']) ?></h2>
                        <div class="price">Цена: <?= number_format($item['price'], 2) ?> ₽</div>
                        <form method="POST" action="updateCart.php" style="box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <label for="quantity_<?= $item['id'] ?>">Количество:</label>
                            <input type="number" id="quantity_<?= $item['id'] ?>" name="quantity" value="<?= $item['quantity_in_cart'] ?>" min="1">
                            <button type="submit" class="btn">Обновить</button>
                        </form>
                        <form method="POST" action="removeFromCart.php" style="box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn danger">Удалить</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>

            <div class="cart-total" style="margin-top: 60px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>Итого:</strong> <?= number_format(array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity_in_cart'];
                }, $cart_items)), 2) ?> ₽
            </div>

            <form method="POST" action="order.php" class="product-forms">
                <div class="form-group">
                    <label  for="address">Адрес доставки:</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn primary">Заказать</button>
            </form>
        <?php endif; ?>

        <div class="form-links">
            <a href="../index.php" class="btn link">Продолжить покупки</a>
        </div>
    </main>

    <?php include '../layout/footer.php'; ?>
</body>
</html>