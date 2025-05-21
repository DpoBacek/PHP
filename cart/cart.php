<?php
// Подключение конфигурации сайта (база данных, пути, настройки безопасности)
require_once '../layout/config.php';

// Инициализация корзины, если она не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка добавления товаров в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Защита от CSRF-атак: проверка токена
    if (!validateCsrfToken($_POST['csrf_token'])) {
        throw new Exception("Недействительный CSRF-токен");
    }

    // Валидация входных данных
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Корректировка некорректного количества
    if ($quantity <= 0) {
        $quantity = 1;
    }

    // Обновление данных в корзине
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Перенаправление для предотвращения повторной отправки формы
    header('Location: cart.php');
    exit();
}

// Настройка лимита товаров для демонстрации неавторизованным пользователям
$limit = isset($_SESSION['user']) ? "" : "LIMIT 1";
// SQL-запрос для получения товаров (требуется оптимизация)
$query = "SELECT p.*, i.file_name AS image FROM products p 
          LEFT JOIN images i ON p.image_id = i.id 
          ORDER BY p.created_at DESC $limit";

// Выполнение запроса с обработкой ошибок
if (!$result = $mysqli->query($query)) {
    throw new Exception("Ошибка запроса: " . $mysqli->error);
}

// Формирование списка товаров в корзине
$cart_items = [];
if ($result->num_rows === 0) {
    echo "<p class='notice'>Товары отсутствуют</p>";
} else {
    // Получение полной информации о товарах в корзине
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Запрос данных о товаре (уязвимость к SQL-инъекциям! Требуется использовать подготовленные выражения)
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

// Закрытие соединения с базой данных
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <!-- Подключение основных стилей -->
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <!-- Шапка сайта -->
    <?php include '../layout/header.php'; ?>

    <main class="container">
        <h1>Корзина</h1>

        <!-- Вывод системных сообщений -->
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

        <!-- Проверка наполненности корзины -->
        <?php if (empty($cart_items)): ?>
            <p>Ваша корзина пуста.</p>
        <?php else: ?>
            <!-- Сетка товаров -->
            <section class="products-grid">
                <?php foreach ($cart_items as $item): ?>
                    <article class="product-card">
                        <!-- Изображение товара с обработкой ошибок -->
                        <img src="../images/<?= htmlspecialchars($item['image']) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>"
                             loading="lazy"
                             width="100%"
                             height="300px">
                        
                        <!-- Информация о товаре -->
                        <h2><?= htmlspecialchars($item['title']) ?></h2>
                        <div class="price">Цена: <?= number_format($item['price'], 2) ?> ₽</div>
                        
                        <!-- Форма изменения количества -->
                        <form method="POST" action="updateCart.php" style="box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <label for="quantity_<?= $item['id'] ?>">Количество:</label>
                            <input type="number" id="quantity_<?= $item['id'] ?>" 
                                   name="quantity" 
                                   value="<?= $item['quantity_in_cart'] ?>" 
                                   min="1">
                            <button type="submit" class="btn">Обновить</button>
                        </form>
                        
                        <!-- Форма удаления товара -->
                        <form method="POST" action="removeFromCart.php" style="box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn danger">Удалить</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>

            <!-- Блок с общей суммой заказа -->
            <div class="cart-total" style="margin-top: 60px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>Итого:</strong> 
                <?= number_format(array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity_in_cart'];
                }, $cart_items)), 2) ?> ₽
            </div>

            <!-- Форма оформления заказа -->
            <form method="POST" action="order.php" class="product-forms">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="form-group">
                    <label for="address">Адрес доставки:</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn primary">Заказать</button>
            </form>
        <?php endif; ?>

        <!-- Навигационные ссылки -->
        <div class="form-links">
            <a href="../index.php" class="btn link">Продолжить покупки</a>
        </div>
    </main>

    <!-- Подвал сайта -->
    <?php include '../layout/footer.php'; ?>
</body>
</html>
