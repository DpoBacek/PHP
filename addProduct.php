<?php
require_once 'layout/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация данных
        $title = $mysqli->real_escape_string($_POST['title']);
        $description = $mysqli->real_escape_string($_POST['description']);
        $price = (float)$_POST['price'];
        $quantity = (int)$_POST['quantity'];
        $category_id = (int)$_POST['category_id'];

        // Проверка категории
        $stmt = $mysqli->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        
        if (!$stmt->get_result()->num_rows) {
            throw new Exception("Неверная категория");
        }

        // Обработка изображения
        $target_dir = "images/";
        // Генерация уникального имени файла
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception("Допустимы только изображения JPG, JPEG, PNG и GIF");
        }

        if ($_FILES["image"]["size"] > 2000000) {
            throw new Exception("Файл слишком большой (макс. 2MB)");
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            throw new Exception("Ошибка загрузки изображения");
        }

        // Сохранение изображения в базе данных
        $stmt = $mysqli->prepare("INSERT INTO images (file_name) VALUES (?)");
        $stmt->bind_param("s", $file_name);
        $stmt->execute();
        $image_id = $stmt->insert_id;
        $stmt->close();

        // Использование image_id в отдельном запросе для добавления товара
        $stmt = $mysqli->prepare("INSERT INTO products 
            (title, description, price, quantity, category_id, image_id, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'on confirmation')");
        $stmt->bind_param("ssdiis", 
            $title, 
            $description, 
            $price, 
            $quantity, 
            $category_id, 
            $image_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Ошибка базы данных: " . $stmt->error);
        }

        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Получение списка категорий
$categories = $mysqli->query("SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="ru">
<?php include 'layout/header.php' ?>

<body>
    <main class="container">
        <h1>Добавить новый товар</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label>Название товара:</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="4" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Цена (₽):</label>
                    <input type="number" step="0.01" name="price" required>
                </div>

                <div class="form-group">
                    <label>Количество:</label>
                    <input type="number" name="quantity" min="0" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Изображение товара:</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label>Категория:</label>
                <select name="category_id" required>
                    <option value="">Выберите категорию</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>


            <button type="submit" class="btn primary">Добавить товар</button>
        </form>
            <div class="form-links">
                <a href="index.php" class="btn link">На главную</a>
            </div>
    </main>

    <?php include 'layout/footer.php' ?>
</body>
</html>
