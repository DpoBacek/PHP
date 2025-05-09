<?php
session_start();
require_once '../layout/config.php';

// Проверяем, является ли пользователь администратором
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Проверяем, был ли отправлен POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Проверяем, что статус является допустимым
    $valid_statuses = ['В обработке', 'Выполнено', 'Отменено'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error_message'] = "Недопустимый статус.";
        header('Location: orders.php');
        exit();
    }

    // Обновляем статус заказа в базе данных
    $stmt = $mysqli->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Статус заказа успешно обновлен.";
    } else {
        $_SESSION['error_message'] = "Не удалось обновить статус заказа.";
    }

    $stmt->close();
    header('Location: orders.php');
    exit();
} else {
    header('Location: orders.php');
    exit();
}?>