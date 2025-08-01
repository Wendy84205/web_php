<?php
session_start();
require_once 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu giỏ hàng
$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    echo "Giỏ hàng trống!";
    exit;
}

// Tính tổng tiền
$total = 0;
foreach ($items as $item) {
    $productStmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $productStmt->execute([$item['product_id']]);
    $product = $productStmt->fetch();

    if ($product) {
        $total += $product['price'] * $item['quantity'];
    }
}

// Tạo đơn hàng
$pdo->beginTransaction();
try {
    $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)")
        ->execute([$user_id, $total]);
    $order_id = $pdo->lastInsertId();

    $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $productStmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $productStmt->execute([$item['product_id']]);
        $product = $productStmt->fetch();

        if ($product) {
            $insertItem->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $product['price']
            ]);
        }
    }

    // Xoá giỏ hàng
    $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?")->execute([$user_id]);

    $pdo->commit();
    header("Location: track_order.php?id=" . $order_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Lỗi khi tạo đơn hàng: " . $e->getMessage();
}
?>
