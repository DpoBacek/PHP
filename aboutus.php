<?php
session_start();
require_once 'layout/config.php';
$page_title = "О нас";
require_once 'layout/header.php';

// Получаем 5 последних товаров
$query = "
    SELECT p.*, i.file_name AS image 
    FROM products p 
    LEFT JOIN images i ON p.image_id = i.id 
    WHERE p.status = 'approved' 
    ORDER BY p.created_at DESC 
    LIMIT 5
";
$result = $mysqli->query($query);
?>

<main class="container">
    <h1>О нас</h1>
    <p>Добро пожаловать в наш магазин! Мы стремимся предоставить вам лучшие товары по доступным ценам.</p>
    <blockquote class="notice">
        "Качество, которому вы можете доверять!"
    </blockquote>

    <h2>Наши последние товары</h2>
    <?php if ($result->num_rows > 0): ?>
        <div class="slider-container">
            <button class="slider-btn prev-btn">❮</button>
            <div class="slider">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="slide">
                        <img src="images/<?= htmlspecialchars($row['image']) ?>" 
                             alt="<?= htmlspecialchars($row['title']) ?>" 
                             loading="lazy" 
                             style="width: 100%; height: 360px;"
                             onerror="this.onerror=null; this.src='cart/DefaultPicture.png';">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <div class="price"><?= number_format($row['price'], 2) ?> ₽</div>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="slider-btn next-btn">❯</button>
        </div>
    <?php else: ?>
        <p class="notice">Товары отсутствуют.</p>
    <?php endif; ?>
</main>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        let currentIndex = 0;

        function updateSlider() {
            const offset = -currentIndex * 100;
            slider.style.transform = `translateX(${offset}%)`;
        }

        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
            updateSlider();
        });

        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
            updateSlider();
        });

        updateSlider();
    });
</script>
<?php require_once 'layout/footer.php'; ?>