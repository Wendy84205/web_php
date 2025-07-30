<?php
session_start();
require '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    echo "Thiếu mã đơn hàng!";
    exit;
}

// Xóa chi tiết đơn hàng trước (tránh lỗi ràng buộc khóa ngoại)
$pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);

// Sau đó xóa đơn hàng chính
$pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);

$_SESSION['success'] = "✅ Đã xóa đơn hàng thành công!";
header("Location: orders.php");
exit;
?>
