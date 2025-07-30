<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;

// Kiểm tra dữ liệu
if (!$product_id) {
    die("Thiếu product_id.");
}

// Kiểm tra sản phẩm có tồn tại
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
if (!$stmt->fetch()) {
    die("Sản phẩm không tồn tại hoặc đã bị xoá.");
}

// Kiểm tra sản phẩm đã có trong giỏ
$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);

if ($stmt->rowCount() > 0) {
    // Nếu đã có, tăng số lượng
    $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?")
        ->execute([$user_id, $product_id]);
} else {
    // Nếu chưa có, thêm mới
    $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)")
        ->execute([$user_id, $product_id]);
}

header("Location: ../cart.php");
exit;
