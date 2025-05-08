<?php
session_start();
require_once '../layout/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $login = trim($_POST['login']);
        $password = $_POST['password'];

        if (empty($login) || empty($password)) {
            throw new Exception("Все поля обязательны для заполнения");
        }

        $stmt = $mysqli->prepare("SELECT id, login, password, role FROM users WHERE login = ?");
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $mysqli->error);
        }

        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Пользователь с таким логином не найден");
        }

        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Неверный пароль");
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'login' => $user['login'],
            'role' => $user['role']
        ];

        header("Location: ../index.php");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" type="text/css" href="../style/style.php">
</head>
<body>
    <?php include '../layout/header.php' ?>

    <main class="container">
        <h1 class="form-title">Вход в систему</h1>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" 
                       name="login" 
                       required
                       autocomplete="username">
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" 
                       name="password" 
                       required
                       autocomplete="current-password">
            </div>

            <button type="submit" class="btn primary">Войти</button>

            <div class="form-links">
                Нет аккаунта? <a href="registration.php">Создать аккаунт</a>
                <br>
                <a href="index.php" class="btn link">На главную</a>
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php' ?>
</body>
</html>
