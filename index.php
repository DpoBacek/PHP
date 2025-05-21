<?php
session_start();
// Установка заголовка страницы
$page_title = "Главная страница";
// Подключение конфигурации и шапки сайта
require_once 'layout/config.php';
require_once 'layout/header.php';
?>

<!-- Основной контент страницы -->
<main class="container" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 100vh;">

<?php

// Отображение ошибок из сессии
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['error']);

try {

    // Ограничение вывода товаров для неавторизованных пользователей
    $limit = isset($_SESSION['user']) ? "" : "LIMIT 1";
    
    // SQL-запрос для получения товаров с изображениями
    $query = "SELECT p.*, i.file_name AS image FROM products p 
              LEFT JOIN images i ON p.image_id = i.id 
              ORDER BY p.created_at DESC $limit";

    if (!$result = $mysqli->query($query)) {
        throw new Exception("Ошибка запроса: " . $mysqli->error);
    }

    // Обработка случаев когда товары отсутствуют
    if ($result->num_rows === 0) {
        echo "<p class='notice'>Товары отсутствуют</p>";
    }
    else {
        ?>
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <!-- Сетка для отображения товаров -->
        <section class="products-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
        <article class="product-card" >
            <!-- Блок статуса товара -->
            <?php if ($row['status'] === 'on confirmation' || $row['status'] === 'rejected'): ?>
                <div class="status-overlay">
                    <p><?php if ($row['status'] === 'on confirmation') { 
                        echo 'На подтверждении <br> администрацией'; 
                    } elseif ($row['status'] === 'rejected') {
                        // Особый интерфейс для администратора для отклоненных товаров
                        if ($_SESSION['user']['role'] === 'admin'): ?>
                            <div class="admin-actions">
                                <a href="admin/delete.php?id=<?= $row['id'] ?>" 
                                class="delete-btn"
                                onclick="return confirm('Удалить товар?')">Удалить с базы данных</a>
                            </div>
                        <?php else: echo 'Товар удалён'; ?>                           
                        <?php endif; 
                    } ?></p>
            <?php endif; ?>

                </div>
            <!-- Основной блок товара с эффектами для разных статусов -->
            <div class="product-div" style="<?php 
                if ($row['status'] === 'on confirmation') { 
                    echo 'filter: blur(5px); '; 
                } elseif ($row['status'] === 'rejected') { 
                    echo 'filter: grayscale(100%);'; 
                } ?> position: relative; z-index: 0;">
                
                <!-- Изображение товара с обработкой ошибок -->
                <img src="images/<?= htmlspecialchars($row['image']) ?>" 
                     alt="<?= htmlspecialchars($row['title']) ?>"
                     loading="lazy"
                     width="100%"
                     height="300px"
                     onerror="this.onerror=null; this.src='cart/DefaultPicture.png';">
                
                <!-- Информация о товаре -->
                <h2><?= htmlspecialchars($row['title']) ?></h2>
                <div class="price"><?= number_format($row['price'], 2) ?> ₽</div>
                <div class="quantity">Количество: <?= htmlspecialchars($row['quantity']) ?></div>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <div class="category">
                    Категория: <?= htmlspecialchars(getCategoryName($row['category_id'])) ?>
                </div>
                <time datetime="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
                    Добавлено: <?= date('d.m.Y H:i', strtotime($row['created_at'])) ?>
                </time>

                <!-- Панель действий для администратора -->
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <div class="admin-actions">
                        <a href="admin/edit.php?id=<?= $row['id'] ?>" class="edit-btn">✏️</a>
                        <a href="admin/delete.php?id=<?= $row['id'] ?>" 
                           class="delete-btn"
                           onclick="return confirm('Удалить товар?')">🗑️</a>
                    </div>
                <?php endif; ?>

                <!-- Форма добавления в корзину -->
                <form method="POST" action="cart/cart.php"  style="box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" style="width: 100%; margin: 0; padding: 10px; font-size: 16px;">В корзину</button>
                </form>
            </div>
        </article>
        <?php endwhile; ?>
        </section>
        <?php
    }
}
catch(Exception $e) {
    echo "<div class='error'>" . $e->getMessage() . "</div>";
}

// Подключение подвала сайта
require_once 'layout/footer.php';

// Закрытие соединения с базой данных
if (isset($mysqli) && $mysqli instanceof mysqli) {
    $mysqli->close();
}

/**
 * Безопасно получает название категории по ID
 * @param int $category_id Идентификатор категории
 * @return string Название категории или сообщение об ошибке
 */
function getCategoryName($category_id) {
    global $mysqli;
    
    // Проверка типа входных данных
    if (!is_numeric($category_id)) {
        return 'Ошибка: Неверный формат категории';
    }

    // Подготовленный запрос для защиты от SQL-инъекций
    $stmt = $mysqli->prepare("SELECT name FROM categories WHERE id = ?");
    
    if (!$stmt) {
        return 'Ошибка подготовки запроса';
    }

    // Привязка параметров и выполнение запроса
    $stmt->bind_param("i", $category_id);
    
    if (!$stmt->execute()) {
        $stmt->close();
        return 'Ошибка выполнения запроса';
    }

    // Обработка результатов
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return 'Без категории';
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    
    return htmlspecialchars($row['name']); // Экранирование вывода
}
?>
</main>
