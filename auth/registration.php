<?php
session_start();
require_once '../layout/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация данных
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $patronimic = trim($_POST['patronimic'] ?? '');
        $login = trim($_POST['login']);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $r_password = $_POST['r_password'];

        // Проверка обязательных полей
        if (empty($name) || empty($surname) || empty($login) || empty($email)) {
            throw new Exception("Все обязательные поля должны быть заполнены");
        }

        // Проверка паролей
        if ($password !== $r_password) {
            throw new Exception("Пароли не совпадают!");
        }

        // Проверка уникальности логина
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Логин уже занят!");
        }

        // Хэширование пароля
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Вставка в БД
        $stmt = $mysqli->prepare("INSERT INTO users 
            (name, surname, patronimic, login, email, password)
            VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssss", 
            $name, 
            $surname, 
            $patronimic, 
            $login, 
            $email, 
            $hashed_password
        );

        if (!$stmt->execute()) {
            throw new Exception("Ошибка при создании пользователя: " . $stmt->error);
        }

        // Получаем ID нового пользователя
        $new_user_id = $stmt->insert_id;

        // Автоматическая авторизация
        $_SESSION['user'] = [
            'id' => $new_user_id,
            'login' => $login
        ];

        // Перенаправление на защищенную страницу
        header("Location: index.php");
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
    <title>Регистрация</title>
    <link rel="stylesheet" type="text/css" href="../style/style.php">
</head>
<body>
    <?php include '../layout/header.php' ?>

    <main class="container">
        <h1 class="form-title">Регистрация нового пользователя</h1>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="registration-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" 
                           name="name" 
                           pattern="^[А-Яа-яёЁ\s\-]+$"
                           title="Допустимы только русские буквы, пробелы и дефисы"
                           required>
                </div>

                <div class="form-group">
                    <label>Фамилия *</label>
                    <input type="text" 
                           name="surname" 
                           pattern="^[А-Яа-яёЁ\s\-]+$"
                           title="Допустимы только русские буквы, пробелы и дефисы"
                           required>
                </div>

                <div class="form-group">
                    <label>Отчество</label>
                    <input type="text" 
                           name="patronimic" 
                           pattern="^[А-Яа-яёЁ\s\-]+$"
                           title="Допустимы только русские буквы, пробелы и дефисы">
                </div>

                <div class="form-group">
                    <label>Логин *</label>
                    <input type="text" 
                           name="login" 
                           pattern="^[A-Za-z0-9\-]+$"
                           title="Допустимы латинские буквы, цифры и дефисы"
                           required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Пароль *</label>
                    <input type="password" 
                           name="password" 
                           minlength="6"
                           title="Минимальная длина 6 символов"
                           required>
                </div>

                <div class="form-group">
                    <label>Повторите пароль *</label>
                    <input type="password" 
                           name="r_password" 
                           minlength="6"
                           title="Минимальная длина 6 символов"
                           required>
                </div>
            </div>

            <div class="form-agreement">
                <label class="checkbox-label">
                    <input type="checkbox" 
                           id="agreementCheckbox" 
                           name="agreement"
                           required>
                    <span>Согласен на обработку персональных данных</span>
                </label>
            </div>

            <button type="submit" class="btn primary" id="submitBtn" disabled>
                Зарегистрироваться
            </button>

            <div class="form-links">
                Уже есть аккаунт? <a href="login.php">Войти</a>
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php' ?>

    <script>
        const checkbox = document.getElementById('agreementCheckbox');
        const submitBtn = document.getElementById('submitBtn');

        checkbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });
    </script>
</body>
</html>
