<?php
session_start();
require_once '../layout/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);

    if (empty($address)) {
        $_SESSION['error_message'] = "Пожалуйста, укажите адрес доставки.";
        header('Location: cart.php');
        exit();
    }

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $mysqli->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $available_quantity = $product['quantity'];

            if ($quantity > $available_quantity) {
                $_SESSION['error_message'] = "Недостаточно товара на складе для продукта ID: $product_id.";
                header('Location: cart.php');
                exit();
            }

            // Уменьшаем количество товара в базе данных
            $new_quantity = $available_quantity - $quantity;
            $update_stmt = $mysqli->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_quantity, $product_id);
            $update_stmt->execute();
            $update_stmt->close();

            if ($new_quantity == 0) {
                $delete_stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
                $delete_stmt->bind_param("i", $product_id);
                $delete_stmt->execute();
                $delete_stmt->close();
            }
        }

        $stmt->close();
    }

    // Сохраняем заказ в базу данных
    $user_id = $_SESSION['user']['id']; // Предполагается, что ID пользователя хранится в сессии
    $order_date = date('Y-m-d H:i:s');
    $status = 'В обработке'; // Значение по умолчанию для статуса заказа
    $order_stmt = $mysqli->prepare("INSERT INTO orders (user_id, address, order_date, status) VALUES (?, ?, ?, ?)");
    if (!$order_stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    $order_stmt->bind_param("isss", $user_id, $address, $order_date, $status);
    $order_stmt->execute();
    $order_id = $order_stmt->insert_id;

    // Сохраняем детали заказа
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $order_detail_stmt = $mysqli->prepare("INSERT INTO order_details (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $order_detail_stmt->bind_param("iii", $order_id, $product_id, $quantity);
        $order_detail_stmt->execute();
        $order_detail_stmt->close();
    }

    $order_stmt->close();

    // Очищаем корзину после успешного заказа
    unset($_SESSION['cart']);
    $_SESSION['success_message'] = "Ваш заказ успешно оформлен!";
    header('Location: cart.php');
    exit();
} else {
    header('Location: index.php');
    exit();
}