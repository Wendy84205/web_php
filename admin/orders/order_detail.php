<?php
session_start();
require '../../includes/db.php';

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    echo "Thiếu mã đơn hàng!";
    exit;
}

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("
    SELECT o.*, u.fullname 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Không tìm thấy đơn hàng!";
    exit;
}

// Lấy danh sách sản phẩm trong đơn hàng
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3>🧾 Chi tiết đơn hàng #<?= htmlspecialchars($order['id']) ?></h3>
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    <p><strong>Trạng thái:</strong> <?= ucfirst($order['status']) ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($items as $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><img src="../../uploads/<?= htmlspecialchars($item['image']) ?>" width="60"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($subtotal, 0, ',', '.') ?>₫</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h5 class="text-end">Tổng cộng: <?= number_format($total, 0, ',', '.') ?>₫</h5>
    <a href="orders.php" class="btn btn-secondary mt-3">Quay lại</a>
</div>
</body>
</html>
