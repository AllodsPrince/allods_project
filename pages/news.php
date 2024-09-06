<?php
include '../includes/header.php';
include '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Подготовка запроса
    $stmt = $conn->prepare("SELECT title, content, created_at FROM news WHERE id = :id");
    
    // Привязка параметра
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // Выполнение запроса
    $stmt->execute();
    
    // Получение результата
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($news) {
        echo "<div class='container'>";
        echo "<main class='main-content'>";
        echo "<h1>" . htmlspecialchars($news['title']) . "</h1>";
        echo "<p class='news-date'>Опубликовано: " . date('d.m.Y H:i', strtotime($news['created_at'])) . "</p>";
        echo "<div class='news-content'>" . nl2br(htmlspecialchars($news['content'])) . "</div>";
        echo "</main>";
        echo "</div>";
    } else {
        echo "<p>Новость не найдена.</p>";
    }
} else {
    echo "<p>Идентификатор новости не указан.</p>";
}

include '../includes/footer.php';
?>