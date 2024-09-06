<?php
session_start();
include '../includes/header.php';
include '../includes/db_connect.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Обработка формы редактирования профиля
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_email = trim($_POST['email']);
    
    try {
        $stmt = $conn->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->bindParam(':email', $new_email);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        $success_message = "Профиль успешно обновлен.";
    } catch(PDOException $e) {
        $error_message = "Ошибка при обновлении профиля: " . $e->getMessage();
    }
}

// Обработка формы изменения пароля
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error_message = "Новые пароли не совпадают.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':id', $_SESSION['user_id']);
                $stmt->execute();
                $success_message = "Пароль успешно изменен.";
            } else {
                $error_message = "Текущий пароль неверен.";
            }
        } catch(PDOException $e) {
            $error_message = "Ошибка при изменении пароля: " . $e->getMessage();
        }
    }
}

// Получение данных пользователя
try {
    $stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Ошибка при получении данных пользователя: " . $e->getMessage());
}
?>

<div class="container">
    <main class="main-content">
        <h1>Личный кабинет</h1>
        <div class="profile-info">
            <h2>Добро пожаловать, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Дата регистрации:</strong> <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
        </div>

        <h2>Магазин</h2>
        <div class="shop-container">
            <div class="shop-item">
                <h3>Сундук Первопроходца</h3>
                <p class="price">799 руб.</p>
                <button class="buy-button" onclick="confirmPurchase()">Купить</button>
            </div>
        </div>

        <a href="logout.php" class="allods-button">Выйти</a>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
