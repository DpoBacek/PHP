<link rel="stylesheet" type="text/css" href="style/style.php" />
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "project");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}
$mysqli->query("SET NAMES 'utf8'");
$login = $_SESSION['user'] ['login'];
$sql = "SELECT * FROM users WHERE login = '$login'";
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();

$mysqli->close();
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style/style.php" />

<head>
    <title>Профиль</title>
    <meta charset="utf-8">
</head>

<body>
    <div class="profile-container">
        <h2>Ваш профиль</h2>

        <table>
            <tr>
                <th>Имя</th>
                <td><?= $user['name'] ?></td>
            </tr>
            <tr>
                <th>Фамилия</th>
                <td><?= $user['surname'] ?></td>
            </tr>
            <tr>
                <th>Отчество</th>
                <td><?= $user['patronimic'] ?></td>
            </tr>
            <tr>
                <th>Логин</th>
                <td><?= $user['login'] ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= $user['email'] ?></td>
            </tr>
        </table>

        <p>
            <a class="btn" href='../index.php'>Вернуться на главную</a>
            <a class="btn btn-d" href="../auth/logout.php">Выйти</a>
        </p>
    </div>
</body>

</html>