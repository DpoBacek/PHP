<?php
// Подключение конфигурации сайта
require_once 'layout/config.php';

// Обработка POST-запроса при отправке формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация CSRF-токена
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Недействительный CSRF-токен");
        }
        // Очистка и валидация входных данных
        $title = $mysqli->real_escape_string($_POST['title']);
        $description = $mysqli->real_escape_string($_POST['description']);
        $price = (float)$_POST['price']; // Приведение к числовому типу
        $quantity = (int)$_POST['quantity'];
        $category_id = (int)$_POST['category_id'];
        
        // Проверка существования категории
        $stmt = $mysqli->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        
        if (!$stmt->get_result()->num_rows) {
            throw new Exception("Неверная категория");
        }
        $stmt->close();
        
        // Обработка загрузки изображения
            $target_dir = "images/";
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension; // Генерация уникального имени
            $target_file = $target_dir . $file_name;
        
        // Проверка типа файла
        if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception("Допустимы только изображения JPG, JPEG, PNG и GIF");
        }
        
        // Проверка размера файла
        if ($_FILES["image"]["size"] > 2000000) {
            throw new Exception("Файл слишком большой (макс. 2MB)");
        }
        
        // Перемещение загруженного файла
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            throw new Exception("Ошибка загрузки изображения");
        }
        
        // Сохранение информации об изображении в БД
        $stmt = $mysqli->prepare("INSERT INTO images (file_name) VALUES (?)");
        $stmt->bind_param("s", $file_name);
        $stmt->execute();
        $image_id = $stmt->insert_id; // Получение ID вставленной записи
        $stmt->close();
        
        // Вставка данных о товаре с использованием подготовленного выражения
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
    
    // Перенаправление после успешного добавления
        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        // Обработка и отображение ошибок
        $error_message = $e->getMessage();
    }
}

// Получение списка категорий для select
$categories = $mysqli->query("SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="ru">
<?php include 'layout/header.php' ?> <!-- Подключение шапки сайта -->

<body>
    <main class="container">
        <h1>Добавить новый товар</h1>
        
        <!-- Блок отображения ошибок -->
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Форма добавления товара -->
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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
                        <!-- Экранирование вывода для предотвращения XSS -->
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

    <?php include 'layout/footer.php' ?> <!-- Подключение подвала сайта -->
</body>
</html>
