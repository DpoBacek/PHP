<?php
// Подключение конфигурации сайта (база данных, пути, настройки безопасности)
require_once '../layout/config.php';
requireAdmin();


/**
 * Получение названия категории по её ID
 * @param int $category_id Идентификатор категории
 * @return string Название категории или заглушка
 */
function getCategoryName($category_id) {
    global $mysqli; // Используем глобальное подключение к БД
    $result = $mysqli->query("SELECT name FROM categories WHERE id = $category_id");
    return $result->num_rows ? $result->fetch_assoc()['name'] : 'Без категории';
}

// Обработка POST-запросов на изменение статуса товаров
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Защита от CSRF-атак: проверка токена
    if (!validateCsrfToken($_POST['csrf_token'])) {
        throw new Exception("Недействительный CSRF-токен");
    }
    
    // Получение и валидация параметров
    $product_id = intval($_POST['product_id']);
    $action = $_POST['action'];

    // Подготовка запроса в зависимости от действия
    if ($action === 'approve') {
        $stmt = $mysqli->prepare("UPDATE products SET status = 'approved' WHERE id = ?");
    } elseif ($action === 'reject') {
        $stmt = $mysqli->prepare("UPDATE products SET status = 'rejected' WHERE id = ?");
    }

    // Выполнение запроса если он был подготовлен
    if (isset($stmt)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
    }

    // Перезагрузка страницы для предотвращения повторной отправки формы
    header('Location: moderation.php');
    exit();
}

// Получение списка товаров, ожидающих модерацию
$result = $mysqli->query("SELECT * FROM products WHERE status = 'on confirmation' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация товаров</title>
    <!-- Подключение стилей сайта -->
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <!-- Включение шапки сайта -->
    <?php include '../layout/header.php'; ?>

    <main class="container">
        <h1>Модерация товаров</h1>

        <?php if ($result->num_rows > 0): ?>
            <!-- Сетка товаров для модерации -->
            <section class="products-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <article class="product-card">
                        <!-- Обработка изображения товара -->
                        <?php
                        $image_result = $mysqli->query("SELECT file_name FROM images WHERE id = '{$row['image_id']}'");
                        $image_file = $image_result && $image_result->num_rows > 0 
                            ? $image_result->fetch_assoc()['file_name'] 
                            : 'cart/DefaultPicture.png'; // Запасное изображение
                        ?>
                        <!-- Вывод изображения с ленивой загрузкой и обработкой ошибок -->
                        <img src="../images/<?= htmlspecialchars($image_file) ?>" 
                             alt="<?= htmlspecialchars($row['title']) ?>"
                             loading="lazy"
                             width="100%"
                             height="300px"
                             onerror="this.onerror=null; this.src='../cart/DefaultPicture.png';">
                        
                        <!-- Основная информация о товаре -->
                        <h2><?= htmlspecialchars($row['title']) ?></h2>
                        <div class="price">Цена: <?= number_format($row['price'], 2) ?> ₽</div>
                        <div class="quantity">Количество: <?= htmlspecialchars($row['quantity']) ?></div>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <div class="category">
                            Категория: <?= htmlspecialchars(getCategoryName($row['category_id'])) ?>
                        </div>
                        
                        <!-- Форма для модерации товара -->
                        <form method="POST" style="display: flex; gap: 10px; margin-top: 10px;">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn primary">Одобрить</button>
                            <button type="submit" name="action" value="reject" class="btn danger">Отклонить</button>
                        </form>
                    </article>
                <?php endwhile; ?>
            </section>
        <?php else: ?>
            <!-- Сообщение при отсутствии товаров -->
            <p>Нет товаров, ожидающих модерации.</p>
        <?php endif; ?>
    </main>

    <!-- Включение подвала сайта -->
    <?php include '../layout/footer.php'; ?>
</body>
</html>
