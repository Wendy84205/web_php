
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

// Kiểm tra người dùng có tồn tại
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Không tìm thấy người dùng.";
    exit;
}

// Thực hiện mở khóa
$stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
$stmt->execute([$id]);

// Quay lại danh sách với thông báo
header("Location: users.php?msg=unblock_success");
exit;
