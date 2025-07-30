<?php
session_start();
require '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$order_id = $_GET['id'] ?? null;
$error = '';

if (!$order_id) {
    echo "Thiếu mã đơn hàng!";
    exit;
}

// Lấy dữ liệu đơn hàng
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Không tìm thấy đơn hàng!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';

    if (!in_array($status, ['Chờ xử lý', 'Đã xử lý', 'Đã giao hàng', 'Đã hủy'])) {
        $error = '⚠️ Trạng thái không hợp lệ';
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $_SESSION['success'] = "✅ Cập nhật trạng thái đơn hàng thành công!";
        header("Location: orders.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3>✏️ Cập nhật đơn hàng <strong>#<?= htmlspecialchars($order['id']) ?></strong></h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm mt-4">
        <div class="mb-3">
            <label class="form-label">Trạng thái đơn hàng</label>
            <select name="status" class="form-select">
                <?php
                $statuses = ['Chờ xử lý', 'Đã xử lý', 'Đã giao hàng', 'Đã hủy'];
                foreach ($statuses as $s):
                ?>
                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <button class="btn btn-primary">💾 Cập nhật</button>
        <a href="orders.php" class="btn btn-secondary">↩️ Quay lại</a>
    </form>
</div>
</body>
</html>
