<?php
require_once '../layout/config.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

function getCategoryName($category_id) {
    global $mysqli;
    $result = $mysqli->query("SELECT name FROM categories WHERE id = $category_id");
    return $result->num_rows ? $result->fetch_assoc()['name'] : 'Без категории';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $mysqli->prepare("UPDATE products SET status = 'approved' WHERE id = ?");
    } elseif ($action === 'reject') {
        $stmt = $mysqli->prepare("UPDATE products SET status = 'rejected' WHERE id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: moderation.php');
    exit();
}

$result = $mysqli->query("SELECT * FROM products WHERE status = 'on confirmation'");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация товаров</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <?php include '../layout/header.php'; ?>

    <main class="container">
        <h1>Модерация товаров</h1>

        <?php if ($result->num_rows > 0): ?>
            <section class="products-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <article class="product-card">
                        <?php
                        $image_result = $mysqli->query("SELECT file_name FROM images WHERE id = '{$row['image_id']}'");
                        $image_file = $image_result && $image_result->num_rows > 0 ? $image_result->fetch_assoc()['file_name'] : 'default.jpg';
                        ?>
                        <img src="../images/<?= htmlspecialchars($image_file) ?>" 
                             alt="<?= htmlspecialchars($row['title']) ?>"
                             loading="lazy"
                             width="100%"
                             height="300px">
                        <h2><?= htmlspecialchars($row['title']) ?></h2>
                        <div class="price">Цена: <?= number_format($row['price'], 2) ?> ₽</div>
                        <div class="quantity">Количество: <?= htmlspecialchars($row['quantity']) ?></div>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <div class="category">
                            Категория: <?= htmlspecialchars(getCategoryName($row['category_id'])) ?>
                        </div>
                        <form method="POST" style="display: flex; gap: 10px; margin-top: 10px;">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn primary">Одобрить</button>
                            <button type="submit" name="action" value="reject" class="btn danger">Отклонить</button>
                        </form>
                    </article>
                <?php endwhile; ?>
            </section>
        <?php else: ?>
            <p>Нет товаров, ожидающих модерации.</p>
        <?php endif; ?>
    </main>

    <?php include '../layout/footer.php'; ?>
</body>
</html>