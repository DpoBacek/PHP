<?php
require_once 'layout/config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <?php include 'layout/header.php'; ?>

    <main class="container">
        <h1>Где нас найти</h1>

        <p>
            Мы находимся по адресу: <strong>г. Москва, ул. Примерная, д. 123</strong>.
        </p>
        <p>
            Вы можете связаться с нами по телефону: <strong>+7 (123) 456-78-90</strong> или написать на email: <strong>info@techshop.ru</strong>.
        </p>

        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153167!3d-37.81627974202144!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d8b3c1b0b1a!2sTechShop!5e0!3m2!1sen!2sru!4v1614311234567!5m2!1sen!2sru"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy">
        </iframe>
    </main> 

    <?php include 'layout/footer.php'; ?>
</body>
</html>