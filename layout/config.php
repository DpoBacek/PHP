<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "project");

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$mysqli->query("SET NAMES 'utf8'");

$base_url = '/';

// Создание базы данных, если она не существует
$mysqli->query("CREATE DATABASE IF NOT EXISTS project");
$mysqli->select_db("project");

// Создание таблицы categories
$mysqli->query("CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
)");

// Создание таблицы images
$mysqli->query("CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL
)");

// Создание таблицы products
$mysqli->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    category_id INT NOT NULL,
    image_id INT NOT NULL,
    status ENUM('on confirmation', 'approved', 'rejected') NOT NULL DEFAULT 'on confirmation',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
)");

// Создание таблицы users с обновлённой структурой
$mysqli->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    patronimic VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
)");

// Создание таблицы orders
$mysqli->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address TEXT NOT NULL,
    order_date DATETIME NOT NULL,
    status ENUM('В обработке', 'Выполнено', 'Отменено') NOT NULL DEFAULT 'В обработке',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Создание таблицы order_details
$mysqli->query("CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)");

// Заполнение таблицы categories
$mysqli->query("INSERT IGNORE INTO categories (id, name) VALUES 
    (1, 'Электроника'),
    (2, 'Одежда'),
    (3, 'Книги'),
    (4, 'Игрушки')");

// Заполнение таблицы users
$mysqli->query("INSERT IGNORE INTO users (id, name, surname, patronimic, login, email, password, role) VALUES 
    (1, 'Админ', 'Админов', 'Админович', 'admin', 'admin@example.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin'),
    (2, 'Пользователь', 'Пользователов', 'Пользователович', 'user', 'user@example.com', '" . password_hash('user123', PASSWORD_DEFAULT) . "', 'user')");

// Заполнение таблицы images
$mysqli->query("INSERT IGNORE INTO images (id, file_name) VALUES 
    (1, 'DefaultPicture.png')");
    
// Заполнение таблицы products
$mysqli->query("INSERT IGNORE INTO products (title, description, price, quantity, category_id, image_id, status) VALUES 
    ('Смартфон', 'Современный смартфон с большим экраном и мощной камерой.', 19999.99, 10, 1, 1),
    ('Ноутбук', 'Мощный ноутбук для работы и игр.', 59999.99, 5, 1, 1),
    ('Футболка', 'Удобная футболка из натурального хлопка.', 999.99, 20, 2, 1),
    ('Книга', 'Интересная книга для чтения в свободное время.', 499.99, 15, 3, 1),
    ('Игрушечная машинка', 'Яркая игрушечная машинка для детей.', 299.99, 30, 4, 1),
    ('Планшет', 'Компактный планшет для работы и развлечений.', 24999.99, 8, 1, 1),
    ('Куртка', 'Тёплая зимняя куртка.', 4999.99, 12, 2, 7),
    ('Роман', 'Захватывающий роман от известного автора.', 799.99, 10, 3, 1),
    ('Конструктор', 'Развивающий конструктор для детей.', 1499.99, 25, 4, 1),
    ('Наушники', 'Беспроводные наушники с шумоподавлением.', 7999.99, 7, 11, 1)
)");