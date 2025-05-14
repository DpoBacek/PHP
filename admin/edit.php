<link rel="stylesheet" type="text/css" href="../style/style.php" />
<?php
$mysqli = new mysqli("localhost", "root", "", "project");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $title = $mysqli->real_escape_string($_POST['title']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];

    $image_update = '';
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $stmt = $mysqli->prepare("INSERT INTO images (file_name) VALUES (?)");
        $stmt->bind_param("s", $file_name);
        $stmt->execute();
        $new_image_id = $stmt->insert_id;
        $stmt->close();

        $image_update = ", image_id = $new_image_id";
    }   

    $sql = "UPDATE products 
        SET 
            title = '$title', 
            description = '$description', 
            price = $price 
            $image_update 
        WHERE id = $id";

    if ($mysqli->query($sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Ошибка обновления: " . $mysqli->error;
    }
} else {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $mysqli->query($sql);
    
    if ($result->num_rows === 0) {
        die("Товар не найден");
    }
    
    $product = $result->fetch_assoc();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать товар</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <h2>Редактировать товар</h2>
        <div>
            <label>Название:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
            
            <label>Описание:</label>
            <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
            
            <label>Цена:</label>
            <input type="number" step="1" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            
            <label>Изображение:</label>
            <input type="file" name="image" accept="image/*">
            <p>Текущее изображение: <?= basename($product['image']) ?></p>
        </div>
        <button type="submit">Сохранить</button>

        <div class="form-links">
            <a href="../index.php">Назад</a>
        </div>
    </form>
</body>
</html>
