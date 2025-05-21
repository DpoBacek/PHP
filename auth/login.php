<?php
// Начало сессии для доступа к сессионным переменным
session_start();
// Подключение файла конфигурации базы данных и других настроек
require_once '../layout/config.php';

// Переменная для хранения сообщений об ошибках
$error = '';

// Обработка данных формы при POST-запросе
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация CSRF-токена
        if (!validateCsrfToken($_POST['csrf_token'])) {
            throw new Exception("Недействительный CSRF-токен");
        }
        // Санитизация и обрезка логина
        $login = trim($_POST['login']);
        $password = $_POST['password'];

        // Проверка на пустые поля
        if (empty($login) || empty($password)) {
            throw new Exception("Все поля обязательны для заполнения");
        }

        // Подготовка SQL-запроса для поиска пользователя по логину
        $stmt = $mysqli->prepare("SELECT id, login, password, role FROM users WHERE login = ?");
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $mysqli->error);
        }

        // Привязка параметра и выполнение запроса
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        // Проверка существования пользователя
        if ($result->num_rows === 0) {
            throw new Exception("Пользователь с таким логином не найден");
        }

        // Получение данных пользователя
        $user = $result->fetch_assoc();
        // Верификация пароля
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Неверный пароль");
        }

        // Сохранение данных пользователя в сессию
        $_SESSION['user'] = [
            'id' => $user['id'],
            'login' => $user['login'],
            'role' => $user['role'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];

        // Перенаправление на главную страницу после успешного входа
        header("Location: ../index.php");
        exit();

    } catch (Exception $e) {
        // Ловля и обработка исключений
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
    <!-- Подключение CSS стилей -->
    <link rel="stylesheet" type="text/css" href="../style/style.php">
</head>
<body>
    <!-- Включение шапки сайта -->
    <?php include '../layout/header.php' ?>

    <main class="container">
        <h1 class="form-title">Вход в систему</h1>

        <!-- Блок вывода ошибок -->
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Форма авторизации -->
        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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

            <!-- Кнопка отправки формы -->
            <button type="submit" class="btn primary">Войти</button>

            <!-- Дополнительные ссылки -->
            <div class="form-links">
                Нет аккаунта? <a href="registration.php">Создать аккаунт</a>
                <br>
                <a href="index.php" class="btn link">На главную</a>
            </div>
        </form>
    </main>

    <!-- Включение подвала сайта -->
    <?php include '../layout/footer.php' ?>
</body>
</html>
