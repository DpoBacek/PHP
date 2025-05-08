<?php
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $mysqli = new mysqli("localhost", "root", "", "project");
    
    $check_sql = "SELECT id, image_id FROM products WHERE id = $id";
    $check_result = $mysqli->query($check_sql);
    
    if ($check_result->num_rows === 0) {
        die("Товар не найден");
    }

    $product = $check_result->fetch_assoc();

    // Получение имени файла изображения
    $image_sql = "SELECT file_name FROM images WHERE id = " . $product['image_id'];
    $image_result = $mysqli->query($image_sql);

    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc();
        $image_path = '../images/' . $image['file_name'];

        // Удаление файла изображения
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Удаление записи из базы данных
    $delete_sql = "DELETE FROM products WHERE id = $id";
    
    if ($mysqli->query($delete_sql)) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Ошибка удаления: " . $mysqli->error;
    }
    
    $mysqli->close();
} else {
    header("Location: ../index.php");
}
