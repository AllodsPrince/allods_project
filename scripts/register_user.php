<?php
include '../config.php';
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        die("Пожалуйста, заполните все поля.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: /pages/register.php?success=1");
        exit();
    } else {
        header("Location: /pages/register.php?error=1");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>