<?php
$page_title = "Главная страница";
require_once 'layout/header.php';
?>

<main class="container" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 100vh;">

<?php
try {
    $limit = isset($_SESSION['user']) ? "" : "LIMIT 1";
    $query = "SELECT p.*, i.file_name AS image FROM products p 
              LEFT JOIN images i ON p.image_id = i.id 
              ORDER BY p.created_at DESC $limit";

    if (!$result = $mysqli->query($query)) {
        throw new Exception("Ошибка запроса: " . $mysqli->error);
    }

    if ($result->num_rows === 0) {
        echo "<p class='notice'>Товары отсутствуют</p>";
    }
    else {
        ?>
        <section class="products-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
        <article class="product-card" >
            <?php if ($row['status'] === 'on confirmation' || $row['status'] === 'rejected'): ?>
                <div class="status-overlay">
                    <p><?php if ($row['status'] === 'on confirmation') { echo 'На подтверждении <br> администрацией'; } elseif ($row['status'] === 'rejected') {
                         if ($_SESSION['user']['role'] === 'admin'): ?>
                            <div class="admin-actions">
                                <a href="admin/delete.php?id=<?= $row['id'] ?>" 
                                class="delete-btn"
                                onclick="return confirm('Удалить товар?')">Удалить с базы данных</a>
                            </div>
                        <?php else: echo 'Товар удалён'; ?>                           
                        <?php endif; 
                    } ?></p>
                </div>
            <?php endif; ?>
            <div class="product-div" style="<?php if ($row['status'] === 'on confirmation') { echo 'filter: blur(5px); '; } elseif ($row['status'] === 'rejected') { echo 'filter: grayscale(100%);'; } ?> position: relative; z-index: 0;">
                <img src="images/<?= htmlspecialchars($row['image']) ?>" 
                     alt="<?= htmlspecialchars($row['title']) ?>"
                     loading="lazy"
                     width="100%"
                     height="300px"
                     onerror="this.onerror=null; this.src='cart/DefaultPicture.png';">
                <h2><?= htmlspecialchars($row['title']) ?></h2>
                <div class="price"><?= number_format($row['price'], 2) ?> ₽</div>
                <div class="quantity">Количество: <?= htmlspecialchars($row['quantity']) ?></div>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <div class="category">
                    Категория: <?= htmlspecialchars(getCategoryName($row['category_id'])) ?>
                </div>
                <time datetime="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
                    Добавлено: <?= date('d.m.Y H:i', strtotime($row['created_at'])) ?>
                </time>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <div class="admin-actions">
                        <a href="admin/edit.php?id=<?= $row['id'] ?>" class="edit-btn">✏️</a>
                        <a href="admin/delete.php?id=<?= $row['id'] ?>" 
                           class="delete-btn"
                           onclick="return confirm('Удалить товар?')">🗑️</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="cart/cart.php"  style="box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" style="width: 100%; margin: 0; padding: 10px; font-size: 16px;">В корзину</button>
                </form>
            </div>
        </article>
        <?php endwhile; ?>
        </section>
        <?php
    }
}
catch(Exception $e) {
    echo "<div class='error'>Ошибка: " . $e->getMessage() . "</div>";
}

require_once 'layout/footer.php';

if (isset($mysqli) && $mysqli instanceof mysqli) {
    $mysqli->close();
}

function getCategoryName($category_id) {
    global $mysqli;
    $result = $mysqli->query("SELECT name FROM categories WHERE id = $category_id");
    return $result->num_rows ? $result->fetch_assoc()['name'] : 'Без категории';
}
?>
</main>
