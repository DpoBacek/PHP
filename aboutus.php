<link rel="stylesheet" type="text/css" href="style/style.php">
<?php
session_start();
?>
<!DOCTYPE html>
<link rel="stylesheet" type="text/css" href="style/style.php">
<html>

<head>
    <title>О нас</title>
    <meta charset="utf-8">
</head>

<body>
    <h1>О нас</h1>

    <nav>
        <?php if (isset($_SESSION['user'])): ?>
            Вы вошли как: <strong><?php echo $_SESSION['user'] ?></strong><br>
            <a class="btn" href="profile.php">Профиль</a>
            <a class="btn" href="users_list.php">Список пользователей</a>
            <a class="btn" href="add_product.php">Добавить товар</a>
            <a class="btn" href="logout.php">Выйти</a>
        <?php else: ?>
            <a class="btn" href="registration.php">Регистрация</a>
            <a class="btn" href="login.php">Вход</a>
        <?php endif; ?>
    </nav>
    
    <p>
        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestiae, eum!
    </p>
</body>