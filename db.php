<?php
include 'config.php';

$conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);

if ($conn === false) {
    die("Ошибка подключения: " . $conn->errorInfo()[2]);
}
?>