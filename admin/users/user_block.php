
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "ID không hợp lệ.";
    exit;
}

// Kiểm tra xem người dùng có tồn tại không
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Không tìm thấy người dùng.";
    exit;
}

// Khóa tài khoản
$stmt = $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id = ?");
$stmt->execute([$id]);

// Chuyển hướng về trang danh sách
header("Location: users.php?msg=blocked_success");
exit;
