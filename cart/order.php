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

    // Очищаем корзину после успешного заказа
    unset($_SESSION['cart']);
    $_SESSION['success_message'] = "Ваш заказ успешно оформлен!";
    header('Location: cart.php');
    exit();
} else {
    header('Location: index.php');
    exit();
}