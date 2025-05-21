<?php
// Подключение конфигурационных файлов и проверка прав доступа
require_once '../layout/config.php';
requireAdmin();

// Обработка POST-запросов на добавление/удаление категорий
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        // Валидация CSRF-токена
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Недействительный CSRF-токен");
        }
        // Блок добавления новой категории
        if ($_POST['action'] === 'add') {
            // Санитизация и валидация входных данных
            $name = $mysqli->real_escape_string($_POST['name']);

            // Проверка на пустое название категории
            if (empty($name)) {
                throw new Exception("Название категории не может быть пустым");
            }

            // Подготовленный запрос для безопасной вставки
            $stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);

            if (!$stmt->execute()) {
                throw new Exception("Ошибка: " . $stmt->error);
            }
        } 


        // Блок удаления существующей категории
        elseif ($_POST['action'] === 'delete') {
            // Приведение ID категории к целому числу
            $category_id = intval($_POST['category_id']);

            // Подготовленный запрос для безопасного удаления
            $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $category_id);

            if (!$stmt->execute()) {
                throw new Exception("Ошибка: " . $stmt->error);
            }
        }

        // Перенаправление для предотвращения повторной отправки формы
        header("Location: addCategory.php");
        exit();

    } catch (Exception $e) {
        // Логирование ошибок и отображение пользователю
        $error_message = $e->getMessage();
    }
}

// Получение списка всех категорий для отображения
$categories = $mysqli->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="ru">
<?php include '../layout/header.php' ?> <!-- Подключение общей шапки -->

<body>
    <main class="container">
        <h1>Управление категориями</h1>

        <!-- Блок отображения ошибок -->
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Двухколоночный макет -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Левая колонка - форма добавления -->
            <div>
                <h2>Добавить категорию</h2>
                <form method="POST" class="category-form">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Название категории:</label>
                        <input type="text" name="name" required>
                    </div>

                    <button type="submit" class="btn primary">Создать категорию</button>
                </form>
            </div>

            <!-- Правая колонка - список для удаления -->
            <div>
                <h2>Удалить категорию</h2>
                <?php if ($categories->num_rows > 0): ?>
                    <ul class="category-list form">
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <li style="display: flex; justify-content: space-between; align-items: center;">
                                <!-- Экранирование вывода -->
                                <span><?= htmlspecialchars($category['name']) ?></span>
                                <!-- Форма удаления с hidden-полями -->
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                    <button type="submit" class="btn danger" 
                                        style="background-color: #ff4d4d; color: white; border: none; padding: 2px 8px; border-radius: 3px; font-size: 0.9em; cursor: pointer;">
                                        Удалить
                                    </button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Категорий пока нет.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Навигационная ссылка -->
        <div class="form-links">
            <a href="addProduct.php" class="btn link">Назад к добавлению товаров</a>
        </div>
    </main>

    <?php include '../layout/footer.php' ?> <!-- Подключение общего подвала -->
</body>
</html>
