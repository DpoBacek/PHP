<?php
require_once '../layout/config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список пользователей</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>style/style.php">
</head>
<body>
    <?php include '../layout/header.php'; ?>

    <main class="container">
        <h1 class="page-title">Список пользователей</h1>

        <?php
        $mysqli = new mysqli("localhost", "root", "", "project");
        $mysqli->query("SET NAMES 'utf8'");

        $result = $mysqli->query("SELECT * FROM users");

        if ($result->num_rows > 0): ?>
            <div class="user-list" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <?php foreach ($result as $user): ?>
                    <div class="user-card">
                        <h2>Пользователь №<?= $user['id'] ?></h2>
                        <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']) ?></p>
                        <p><strong>Фамилия:</strong> <?= htmlspecialchars($user['surname']) ?></p>
                        <p><strong>Отчество:</strong> <?= htmlspecialchars($user['patronimic']) ?></p>
                        <p><strong>Логин:</strong> <?= htmlspecialchars($user['login']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Пользователей не найдено.</p>
        <?php endif; ?>
        <div class="form-links">
            <a class="btn" href="../index.php">Вернуться на главную</a>
        </div>
        <?php $mysqli->close(); ?>
    </main>

    <?php include '../layout/footer.php'; ?>
</body>
</html>