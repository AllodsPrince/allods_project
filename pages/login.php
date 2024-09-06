<?php
session_start();
include '../includes/header.php';
include '../includes/db_connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Пожалуйста, заполните все поля.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: profile.php");
                    exit();
                } else {
                    $error = "Неверный пароль.";
                }
            } else {
                $error = "Пользователь не найден.";
            }
        } catch(PDOException $e) {
            $error = "Ошибка при входе: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <main class="main-content">
        <h1>Вход в систему</h1>
        <?php
        if (!empty($error)) {
            echo "<p class='error-message'>$error</p>";
        }
        ?>
        <form method="post" action="" class="login-form">
            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="allods-button">Войти</button>
        </form>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
