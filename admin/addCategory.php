<?php
require_once '../layout/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'add') {
            $name = $mysqli->real_escape_string($_POST['name']);

            if (empty($name)) {
                throw new Exception("Название категории не может быть пустым");
            }

            $stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);

            if (!$stmt->execute()) {
                throw new Exception("Ошибка: " . $stmt->error);
            }
        } elseif ($_POST['action'] === 'delete') {
            $category_id = intval($_POST['category_id']);

            $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $category_id);

            if (!$stmt->execute()) {
                throw new Exception("Ошибка: " . $stmt->error);
            }
        }

        header("Location: addCategory.php");
        exit();

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$categories = $mysqli->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="ru">
<?php include '../layout/header.php' ?>

<body>
    <main class="container">
        <h1>Управление категориями</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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

            <div>
                <h2>Удалить категорию</h2>
                <?php if ($categories->num_rows > 0): ?>
                    <ul class="category-list form">
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <li style="display: flex; justify-content: space-between; align-items: center;">
                                <span><?= htmlspecialchars($category['name']) ?></span>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                    <button type="submit" class="btn danger" style="background-color: #ff4d4d; color: white; border: none; padding: 2px 8px; border-radius: 3px; font-size: 0.9em; cursor: pointer;">Удалить</button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Категорий пока нет.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-links">
            <a href="addProduct.php" class="btn link">Назад к добавлению товаров</a>
        </div>
    </main>

    <?php include '../layout/footer.php' ?>
</body>
</html>
