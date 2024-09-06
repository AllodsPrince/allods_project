<?php
include '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("SELECT title, content, created_at FROM news WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($news) {
            echo json_encode([
                'success' => true,
                'title' => htmlspecialchars($news['title']),
                'content' => nl2br(htmlspecialchars($news['content'])),
                'date' => date('d.m.Y', strtotime($news['created_at']))
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Новость не найдена']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID новости не указан']);
}