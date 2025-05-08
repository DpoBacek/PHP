<?php
session_start();
require_once '../layout/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    header('Location: cart.php');
    exit();
} else {
    header('Location: ../index.php');
    exit();
}