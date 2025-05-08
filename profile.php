<link rel="stylesheet" type="text/css" href="style/style.php" />
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
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
    <style>
        .profile-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .profile-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .profile-container table th,
        .profile-container table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .profile-container table th {
            background-color: #f8f9fa;
            color: #34495e;
            font-weight: bold;
        }

        .profile-container .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .profile-container .btn:hover {
            background: #0056b3;
        }

        .profile-container .btn-d {
            background: #dc3545;
        }

        .profile-container .btn-d:hover {
            background: #a71d2a;
        }
    </style>
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
            <a class="btn" href='index.php'>Вернуться на главную</a>
            <a class="btn btn-d" href="logout.php">Выйти</a>
        </p>
    </div>
</body>

</html>