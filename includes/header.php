<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аллоды Онлайн - Фан-сервер</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <img src="/assets/images/allods-logo.png" alt="Аллоды Онлайн" class="logo">
            <nav>
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="characters.php">Персонажи</a></li>
                    <li><a href="guilds.php">Гильдии</a></li>
                    <li><a href="ratings.php">Рейтинги</a></li>
                    <li><a href="forum.php">Форум</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php">Личный кабинет</a></li>
                        <li><a href="logout.php">Выйти</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Вход</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>