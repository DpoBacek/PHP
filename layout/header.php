<?php
require_once 'config.php';
$base_url = $base_url ?? '/';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Магазин' ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <h1 class="logo">TechShop</h1>
            
            <div class="user-panel">
                <?php if (isset($_SESSION['user']['login'])): ?>
                    <a href="<?= $base_url ?>cart/cart.php" style="text-decoration: none; color: inherit;"><div class="user-info">
                        <span class="username">
                        Вы вошли как: <strong><?php echo htmlspecialchars($_SESSION['user']['login']) ?></strong>
                        </span>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <span class="user-badge" style="display: block; text-align: center;">Администратор</span>
                        <?php endif; ?>
                    </div></a>
                <?php endif; ?>
            </div>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="<?= $base_url ?>index.php">Главная</a></li>
                <li><a href="<?= $base_url ?>addProduct.php">Добавить товар</a></li>
                <li><a href="<?= $base_url ?>aboutUs.php">О нас</a></li>
                <li><a href="<?= $base_url ?>address.php">Контакты</a></li>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <li class="dropdown">
                            <a href="#">Управление ▾</a>
                            <ul class="dropdown-menu" style="list-style: none; padding: 0; margin: 0;">
                                <li><a href="<?= $base_url ?>admin/addCategory.php">Управление категориями</a></li>
                                <li><a href="<?= $base_url ?>admin/usersList.php">Список пользователей</a></li>
                                <li><a href="<?= $base_url ?>admin/moderation.php">Модерация товаров</a></li>
                                <li><a href="<?= $base_url ?>admin/orders.php">Управление заказами</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php endif; ?>
                        <li class="dropdown">
                            <a href="#">Профиль ▾</a>
                            <ul class="dropdown-menu" style="list-style: none; padding: 0; margin: 0;">
                                <li><a href="<?= $base_url ?>profile/profile.php">Мой профиль</a></li>
                                <li><a href="<?= $base_url ?>profile/orderHistory.php">История заказов</a></li>
                            </ul>
                        </li>

                    <li><a href="<?= $base_url ?>auth/logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="<?= $base_url ?>auth/login.php">Вход</a></li>
                    <li><a href="<?= $base_url ?>auth/registration.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
