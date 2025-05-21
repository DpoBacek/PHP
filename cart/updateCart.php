<?php
// Подключение конфигурации сайта (база данных, настройки безопасности)
require_once '../layout/config.php';

// Обработка только POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация и преобразование входных данных
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Корректировка некорректного количества товара
    if ($quantity <= 0) {
        $quantity = 1; // Минимальное допустимое значение
    }

    // Проверка доступности товара на складе
    $stmt = $mysqli->prepare("SELECT quantity, title FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $available_quantity = $product['quantity'];
        $available_title = $product['title'];

        // Проверка превышения доступного количества
        if ($quantity > $available_quantity) {
            // Формирование сообщения об ошибке
            $_SESSION['error_message'] = "Вы не можете добавить больше <strong>$available_title</strong>, чем доступно на складе (максимум: <strong>$available_quantity</strong>).";
            
            // Установка максимально доступного количества
            $quantity = $available_quantity;
        }

        // Обновление количества в корзине (только если товар уже добавлен)
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    // Закрытие подготовленного запроса
    $stmt->close();
    
    // Перенаправление обратно в корзину
    header('Location: cart.php');
    exit();
} else {
    // Блокировка прямого доступа к скрипту
    header('Location: ../index.php');
    exit();
}
