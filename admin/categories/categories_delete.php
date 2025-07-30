
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

$id = $_GET['id'] ?? 0;

// Kiểm tra có sản phẩm nào đang dùng danh mục này không
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    echo "<script>alert('Không thể xóa. Danh mục đang được sử dụng cho $count sản phẩm!'); window.location.href='category_list.php';</script>";
    exit;
}

// Xóa danh mục
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);

header("Location: categories.php");
exit;
