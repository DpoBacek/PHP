<?php
// Подключение конфигурации и проверка прав доступа
require_once '../layout/config.php';
requireAdmin();

// Инициализация переменных
$error_message = '';
$success_message = '';

try {
    // Обработка POST-запроса
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Валидация CSRF-токена
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Недействительный CSRF-токен");
        }

        // Базовые проверки
        if (empty($_POST['id'])) {
            throw new Exception("Не указан ID товара");
        }

        // Валидация данных
        $id = (int)$_POST['id'];
        $title = cleanInput($_POST['title']);
        $description = cleanInput($_POST['description']);
        $price = (float)$_POST['price'];
        
        if (empty($title) || strlen($title) > 255) {
            throw new Exception("Название должно содержать от 1 до 255 символов");
        }

        // Получаем информацию о текущем изображении
        $stmt = $mysqli->prepare("
            SELECT i.id AS image_id, i.file_name 
            FROM products p
            LEFT JOIN images i ON p.image_id = i.id 
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $old_image = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Обновление основной информации
        $stmt = $mysqli->prepare("
            UPDATE products 
            SET title = ?, description = ?, price = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssdi", $title, $description, $price, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Ошибка обновления товара: " . $stmt->error);
        }
        $stmt->close();

        // Обработка нового изображения
        if (!empty($_FILES["image"]["name"])) {
            // Валидация изображения
            $max_size = 4 * 1024 * 1024; // 4MB
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($_FILES['image']['tmp_name']);
            
            if (!in_array($mime_type, $allowed_types)) {
                throw new Exception("Допустимы только изображения JPG, PNG и GIF");
            }
            
            if ($_FILES['image']['size'] > $max_size) {
                throw new Exception("Максимальный размер файла - 4MB");
            }

            // Генерация уникального имени
            $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $file_name = uniqid('img_', true) . '.' . $file_ext;
            $target_dir = "../images/";
            $target_file = $target_dir . $file_name;

            // Перемещение файла
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                throw new Exception("Ошибка загрузки изображения");
            }

            // Вставка нового изображения
            $stmt = $mysqli->prepare("INSERT INTO images (file_name) VALUES (?)");
            $stmt->bind_param("s", $file_name);
            if (!$stmt->execute()) {
                @unlink($target_file);
                throw new Exception("Ошибка сохранения изображения: " . $stmt->error);
            }
            $new_image_id = $stmt->insert_id;
            $stmt->close();

            // Обновление ссылки на изображение
            $stmt = $mysqli->prepare("UPDATE products SET image_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_image_id, $id);
            if (!$stmt->execute()) {
                throw new Exception("Ошибка обновления изображения: " . $stmt->error);
            }
            $stmt->close();

            // Удаление старого изображения
            if ($old_image && $old_image['image_id']) {
                try {
                    // Удаление файла
                    $old_file_path = "../images/{$old_image['file_name']}";
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }

                    // Удаление записи из БД
                    $stmt = $mysqli->prepare("DELETE FROM images WHERE id = ?");
                    $stmt->bind_param("i", $old_image['image_id']);
                    $stmt->execute();
                    $stmt->close();
                } catch (Exception $e) {
                    error_log("Ошибка удаления старого изображения: " . $e->getMessage());
                }
            }
        }

        $success_message = "Товар успешно обновлен";
        header("Refresh: 2; URL=../index.php");
    }

    // Получение данных товара для отображения
    if (empty($_GET['id'])) {
        throw new Exception("Не указан ID товара");
    }
    
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare("
        SELECT p.*, i.file_name 
        FROM products p
        LEFT JOIN images i ON p.image_id = i.id
        WHERE p.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Товар не найден");
    }
    
    $product = $result->fetch_assoc();
    $stmt->close();

} catch (Exception $e) {
    $error_message = $e->getMessage();
} finally {
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
}
?>
    <?php include '../layout/header.php' ?>

    <main class="container">
        <h1>Редактировать товар</h1>

        <?php if ($success_message): ?>
            <div class="alert success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

            <div class="form-group">
                <label>Название:</label>
                <input type="text" name="title" 
                       value="<?= htmlspecialchars($product['title']) ?>" 
                       required
                       minlength="1"
                       maxlength="255">
            </div>

            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="4" required><?= 
                    htmlspecialchars($product['description']) 
                ?></textarea>
            </div>

            <div class="form-group">
                <label>Цена (₽):</label>
                <input type="number" step="0.01" name="price" 
                       value="<?= htmlspecialchars($product['price']) ?>" 
                       required
                       min="0">
            </div>

            <div class="form-group">
                <label>Изображение:</label>
                <input type="file" name="image" accept="image/*">
                <?php if (!empty($product['file_name'])): ?>
                    <div class="current-image">
                        <p>Текущее изображение:</p>
                        <img src="../images/<?= htmlspecialchars($product['file_name']) ?>" 
                             alt="<?= htmlspecialchars($product['title']) ?>"
                             style="max-width: 200px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn primary">Сохранить изменения</button>
            </div>
            <div class="form-links">

                <a href="../index.php" class="btn link">На главную</a>
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php' ?>
</body>
</html>
