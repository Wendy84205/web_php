<?php include 'sidebar.php'; ?>

<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM reviews");
$reviews = $stmt->fetchAll();
?>

<table>
    <tr>
        <th>Sản phẩm</th>
        <th>Người đánh giá</th>
        <th>Đánh giá</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($reviews as $review): ?>
        <tr>
            <td><?= htmlspecialchars($review['product_id']) ?></td>
            <td><?= $review['user_id'] ?></td>
            <td><?= $review['rating'] ?></td>
            <td>
                <a href="review_delete.php?id=<?= $review['id'] ?>">Xoá</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>    