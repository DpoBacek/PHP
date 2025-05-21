<style>
<?php header("Content-type: text/css"); ?>
/* Base styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}

/* Header */
header {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

header h1 {
    color: #333;
    font-size: 2.5em;
}
h1 {
    display: block;
    text-align: center;
}
/* Стили для таблиц */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 1rem;
    text-align: left;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.styled-table thead tr {
    background-color: #007bff;
    color: #ffffff;
    text-align: left;
    font-weight: bold;
}

.styled-table th, .styled-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.styled-table tbody tr {
    background-color: #f9f9f9;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:hover {
    background-color: #f1f1f1;
}

.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #007bff;
}

/* Navigation */
nav {
    background: #ffffff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

nav a {
    display: inline-block;
    padding: 8px 16px;
    margin: 5px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s ease;
}

nav a:hover {
    background: #0056b3;
}

/* Main content */
main {
    flex: 1;
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

section {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    width: 100%;
}

article {
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

article:hover {
    transform: translateY(-5px);
}

article img {
    display: block;
    margin: 0 auto 15px;
    border-radius: 4px;
}

article h3 {
    color: #333;
    margin-bottom: 10px;
}

.price {
    color: #28a745;
    font-size: 1.2em;
    font-weight: bold;
    margin-bottom: 10px;
}

time {
    display: block;
    font-size: 0.9em;
    color: #6c757d;
    margin-top: 10px;
}

article div a {
    display: inline-block;
    padding: 5px 10px;
    margin-top: 10px;
    margin-right: 5px;
    border-radius: 4px;
    font-size: 0.9em;
    text-decoration: none;
}

article div a:first-child {
    background: #4CAF50;
    color: white;
}

article div a:last-child {
    background: #dc3545;
    color: white;
}
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
/* Footer */
footer {
    margin-top: 20px;
    padding: 15px;
    background: #ffffff;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #6c757d;
}

/* Forms */
input, textarea {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
}

textarea {
    max-width: 100%;
    resize: vertical; /* Ограничиваем изменение размера только по вертикали */
    overflow: auto; /* Добавляем прокрутку, если текст превышает размер */
}

button {
    background: #28a745;
    color: white;
    cursor: pointer;
    transition: transform 0.2s ease, background-color 0.2s ease;
}

button:hover {
    transform: scale(1.05);
    background-color: #0056b3;
}

button:active {
    transform: scale(0.95);
}


/* Responsive design */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    
    nav a {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
    
    section {
        grid-template-columns: 1fr;
    }
}

.error {
    background: #ffe3e3;
    color: #dc3545;
    padding: 1rem;
    border: 1px solid #dc3545;
    border-radius: 5px;
    margin: 1rem 0;
}
.success {
    background:rgb(232, 255, 227);
    color:rgb(53, 220, 67);
    padding: 1rem;
    border: 1px solid rgb(53, 220, 67);
    border-radius: 5px;
    margin: 1rem 0;
}

.notice {
    background: #e3f2fd;
    color: #1976d2;
    padding: 1rem;
    border-radius: 5px;
    text-align: center;
}

/* Добавьте в существующие стили */
form {
    max-width: 600px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.product-form{
    max-width: 600px;
    margin: 20px auto;
    padding: 5px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.product-forms{
    margin: 20px auto;
    padding: 5px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

form label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}

form input[type="text"],
form input[type="number"],
form textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

form button[type="submit"] {
    background: #28a745;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}
select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.warning {
    background: #fff3cd;
    color: #856404;
    padding: 15px;
    border-radius: 4px;
    margin: 20px 0;
    border: 1px solid #ffeeba;
}
/* Header styles */
.main-header {
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.logo {
    color: #2c3e50;
    font-size: 2.2em;
    margin: 0;
}

.user-panel {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-info {
    text-align: right;
}

.username {
    font-weight: 600;
    color: #2c3e50;
}

.user-badge {
    display: block;
    font-size: 0.8em;
    background: #27ae60;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
}

.main-nav {
    padding: 10px 0;
}

.nav-list {
    list-style: none;
    display: flex;
    justify-content: center;
    gap: 25px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.nav-list a {
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.nav-list a:hover {
    background: #34495e;
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    border-radius: 5px;
    min-width: 200px;
    padding: 10px 0;
    z-index: 1000;
}

.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-menu li {
    padding: 0;
}

.dropdown-menu a {
    color: #2c3e50;
    display: block;
    padding: 10px 20px;
    white-space: nowrap;
}

.dropdown-menu a:hover {
    background: #f8f9fa;
}
/* Стили для формы регистрации */
.registration-form {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.form-title {
    text-align: center;
    margin-bottom: 30px;
    color: #2c3e50;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #34495e;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #bdc3c7;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: #3498db;
    outline: none;
}

.form-agreement {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input {
    width: 18px;
    height: 18px;
}

.form-links {
    text-align: center;
    margin-top: 20px;
    color: #7f8c8d;
}

.form-links a {
    color: #3498db;
    text-decoration: none;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .registration-form {
        padding: 20px;
    }
}
.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group input {
    width: 100%;
    padding: 12px;
    box-sizing: border-box; /* Добавлено */
}

/* Выравнивание высоты */
input[type="text"],
input[type="password"],
input[type="email"] {
    height: 45px; /* Фиксированная высота */
}

/* Стили для формы входа */
.auth-form {
    max-width: 400px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.form-title {
    text-align: center;
    margin-bottom: 30px;
    color: #2c3e50;
}

.form-links {
    margin-top: 20px;
    text-align: center;
    color: #7f8c8d;
}

.form-links a {
    color: #3498db;
    text-decoration: none;
}

/* Product card styles */
.product-card {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}
.product-card img {
    width: 100%;
    height: auto;
    object-fit: cover; /* Обрезка изображения для заполнения области */
    border-radius: 8px;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-card .status-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: 0;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    z-index: 1;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.product-div{
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}


.product-card .status-overlay p {
    font-size: 1em;
    margin: 0;
    color: #fff;
}
.slider-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    overflow: hidden;
}

.slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slide {
    flex: 0 0 100%;
    text-align: center;
    padding: 10px;
    box-sizing: border-box;
}

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    width: 40px;
    height: 100%; 
    padding: 10px;
    cursor: pointer;
    z-index: 10;
}

.slider-btn:hover {
    background-color: rgba(0, 0, 0, 0.7);
    transform: translateY(-50%) scale(1.1);
}

.prev-btn {
    left: 10px;
}

.next-btn {
    right: 10px;
}
</style>