<?php
session_start();
require_once '../layout/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0) {
        $quantity = 1;
    }

    // Проверка доступного количества товара
    $stmt = $mysqli->prepare("SELECT quantity, title FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $available_quantity = $product['quantity'];
        $available_title = $product['title'];

        if ($quantity > $available_quantity) {
            $_SESSION['error_message'] = "Вы не можете добавить больше <strong>$available_title</strong>, чем доступно на складе (максимум: <strong>$available_quantity</strong>).";
            $quantity = $available_quantity; // Ограничиваем количество
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    $stmt->close();
    header('Location: cart.php');
    exit();
} else {
    header('Location: ../index.php');
    exit();
}