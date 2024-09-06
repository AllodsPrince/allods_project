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

$message = '';

// Обработка добавления новой новости
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $publish_date = $_POST['publish_date'];
    $image_path = null;

    if (!empty($title) && !empty($content)) {
        // Обработка загрузки изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/news/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                $image_path = '/uploads/news/' . $file_name;
            } else {
                $message = "Ошибка при загрузке изображения.";
            }
        }

        // SQL запрос для добавления новости
        $stmt = $conn->prepare("INSERT INTO news (title, content, image, publish_date) VALUES (:title, :content, :image, :publish_date)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image_path);
        $stmt->bindParam(':publish_date', $publish_date);

        if ($stmt->execute()) {
            $message = "Новость успешно добавлена.";
        } else {
            $message = "Ошибка при сохранении новости.";
        }
    } else {
        $message = "Пожалуйста, заполните все обязательные поля.";
    }
}

// Обработка редактирования новости
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_news'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $publish_date = $_POST['publish_date'];
    $image_path = null;

    if (!empty($title) && !empty($content)) {
        // Обработка загрузки изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/news/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                $image_path = '/uploads/news/' . $file_name;
            } else {
                $message = "Ошибка при загрузке изображения.";
            }
        }

        // SQL запрос для обновления новости
        $stmt = $conn->prepare("UPDATE news SET title = :title, content = :content, image = :image, publish_date = :publish_date WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image_path);
        $stmt->bindParam(':publish_date', $publish_date);

        if ($stmt->execute()) {
            $message = "Новость успешно обновлена.";
        } else {
            $message = "Ошибка при сохранении новости.";
        }
    } else {
        $message = "Пожалуйста, заполните все обязательные поля.";
    }
}

// Обработка удаления новости
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = :id");
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $message = "Новость успешно удалена.";
    } else {
        $message = "Ошибка при удалении новости.";
    }
}

// Получение новости для редактирования
$edit_news = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT id, title, content, image, publish_date FROM news WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $edit_news = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Получение списка новостей с пагинацией
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$stmt = $conn->prepare("SELECT id, title, created_at, publish_date FROM news WHERE publish_date <= NOW() ORDER BY publish_date DESC LIMIT :offset, :perPage");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalNews = $conn->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPages = ceil($totalNews / $perPage);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление новостями - Аллоды Онлайн</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <!-- Ваш скрипт TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/6ff7fr7e407f558fd3nelfrotpuyxz7ms0ybc2ezbgl3medm/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
      tinymce.init({
        selector: 'textarea',
        plugins: [
          'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
          'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown',
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
          { value: 'First.Name', title: 'First Name' },
          { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
      });
    </script>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Управление новостями</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="news_manager.php">Управление новостями</a></li>
                    <li><a href="../pages/logout.php">Выйти</a></li>
                </ul>
            </nav>
        </header>
        <main class="admin-content">
            <?php if (!empty($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <h2><?php echo $edit_news ? 'Редактировать новость' : 'Добавить новую новость'; ?></h2>
            <form method="post" action="" class="news-form" enctype="multipart/form-data">
                <?php if ($edit_news): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_news['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="title">Заголовок:</label>
                    <input type="text" id="title" name="title" required value="<?php echo $edit_news ? htmlspecialchars($edit_news['title']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="content">Содержание:</label>
                    <textarea id="content" name="content" required><?php echo $edit_news ? $edit_news['content'] : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Изображение:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <?php if ($edit_news && $edit_news['image']): ?>
                        <img src="<?php echo htmlspecialchars($edit_news['image']); ?>" alt="Current image" style="max-width: 200px; margin-top: 10px;">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="publish_date">Дата публикации:</label>
                    <input type="datetime-local" id="publish_date" name="publish_date" value="<?php echo $edit_news ? date('Y-m-d\TH:i', strtotime($edit_news['publish_date'])) : date('Y-m-d\TH:i'); ?>">
                </div>
                <button type="submit" name="<?php echo $edit_news ? 'edit_news' : 'add_news'; ?>" class="allods-button">
                    <?php echo $edit_news ? 'Обновить новость' : 'Добавить новость'; ?>
                </button>
            </form>

            <h2>Список новостей</h2>
            <table class="news-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Заголовок</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($item['publish_date'])); ?></td>
                            <td>
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="edit-link">Редактировать</a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="delete-link" onclick="return confirm('Вы уверены, что хотите удалить эту новость?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

<?php
//$conn->close();
?>
