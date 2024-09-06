<?php
session_start();
include '../includes/db_connect.php';

// Проверка, является ли пользователь администратором
function is_admin($conn, $user_id) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['role'] === 'admin';
}

// Если пользователь не авторизован или не админ, перенаправляем на страницу входа
if (!isset($_SESSION['user_id']) || !is_admin($conn, $_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Аллоды Онлайн</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Админ-панель Аллоды Онлайн</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="news_manager.php">Управление новостями</a></li>
                    <li><a href="../pages/logout.php">Выйти</a></li>
                </ul>
            </nav>
        </header>
        <main class="admin-content">
            <h2>Добро пожаловать в админ-панель</h2>
            <p>Здесь вы можете управлять контентом сайта.</p>
        </main>
    </div>
</body>
</html>
