<?php include '../includes/header.php'; ?>
<?php include '../db.php'; ?>

<div class="content">
    <h1>Форум</h1>
    <?php
    $sql = "SELECT * FROM forum_categories";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='forum-category'>";
            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "<a href='forum_topics.php?category_id=" . $row['id'] . "'>Просмотреть темы</a>";
            echo "</div>";
        }
    } else {
        echo "<p>Категорий форума пока нет.</p>";
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>