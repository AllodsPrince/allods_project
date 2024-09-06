<?php 
session_start(); // Запускаем сессию

// Проверяем, вошел ли пользователь
$isLoggedIn = isset($_SESSION['user_id']); // Предполагаем, что user_id сохраняется в сессии

include '../includes/header.php';
include '../includes/db_connect.php';

try {
    // Получение списка новостей
    $stmt = $conn->prepare("SELECT id, title, content, image, publish_date FROM news WHERE publish_date <= NOW() ORDER BY publish_date DESC LIMIT 5");
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получение полной новости, если запрошена
    $full_news = null;
    if (isset($_GET['news_id'])) {
        $stmt = $conn->prepare("SELECT id, title, content, image, publish_date FROM news WHERE id = :id AND publish_date <= NOW()");
        $stmt->bindParam(':id', $_GET['news_id']);
        $stmt->execute();
        $full_news = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    die("Ошибка запроса: " . $e->getMessage());
}
?>

<div class="container">
    <main class="main-content">
        <h1>Добро пожаловать в мир Аллодов!</h1>
        <p class="intro-text">Отправляйтесь в эпическое путешествие по парящим островам, сражайтесь с могущественными боссами и станьте легендой в нашем уникальном мире!</p>

        <div class="content-section">
            <h2>Последние новости</h2>
            <div class="news-container">
                <?php if ($full_news): ?>
                    <div class="full-news">
                        <h3><?php echo htmlspecialchars($full_news['title']); ?></h3>
                        <?php if ($full_news['image']): ?>
                            <img src="<?php echo htmlspecialchars($full_news['image']); ?>" alt="<?php echo htmlspecialchars($full_news['title']); ?>" class="news-image">
                        <?php endif; ?>
                        <p class="news-date"><?php echo date('d.m.Y', strtotime($full_news['publish_date'])); ?></p>
                        <div class="news-content"><?php echo $full_news['content']; ?></div>
                        <a href="index.php" class="allods-button">Назад к списку новостей</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($news as $item): ?>
                        <div class="news-item">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="news-thumbnail">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="news-excerpt"><?php echo mb_substr(strip_tags($item['content']), 0, 100) . '...'; ?></p>
                            <a href="index.php?news_id=<?php echo $item['id']; ?>" class="read-more">Читать далее</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Код для статистики сервера остается без изменений
        ?>

        <a href="#" class="allods-button">Начать приключение</a>
    </main>
</div>

<div class="header-content">
    <h1>Добро пожаловать на сайт!</h1>
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="logout.php">Выйти</a></li>
            <?php else: ?>
                <li><a href="login.php">Вход</a></li>
                <li><a href="register.php">Регистрация</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include '../includes/footer.php'; ?>