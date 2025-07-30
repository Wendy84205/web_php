<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<p class='cart-message'>Vui lòng <a href='login.php'>đăng nhập</a> để xem giỏ hàng.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy giỏ hàng từ CSDL
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.image, p.price 
    FROM cart_items c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

$total = 0;
?>

<link rel="stylesheet" href="assets/css/cart.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="cart-container">
    <?php if (empty($cartItems)): ?>
        <div class="empty-cart-wrapper">
            <div class="top-nav">
                <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <span class="page-title">Giỏ hàng của bạn</span>
            </div>

            <div class="empty-cart-content">
                <img src="assets/images/cart-empty.png" alt="Giỏ hàng trống">
                <p>Giỏ hàng của bạn đang trống.<br>Hãy chọn thêm sản phẩm để mua sắm nhé</p>
            </div>

            <div class="bottom-btn">
                <a href="index.php" class="btn-return">Quay lại trang chủ</a>
            </div>
        </div>
    <?php else: ?>
        <h2 class="cart-title">🛒 Giỏ hàng của bạn</h2>

        <div class="cart-list">
            <?php foreach ($cartItems as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div class="cart-item">
                    <img src="<?= $item['image'] ?: 'assets/images/default-food.png' ?>" alt="<?= $item['name'] ?>">
                    <div class="cart-info">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p>Giá: <strong><?= number_format($item['price'], 0, ',', '.') ?>₫</strong></p>
                        <form action="includes/update_cart.php" method="POST" class="quantity-form d-flex gap-2 align-items-center">
                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control-sm" style="width: 60px;">
                            <button class="btn btn-sm btn-outline-secondary">Cập nhật</button>
                        </form>
                        <p>Thành tiền: <strong><?= number_format($subtotal, 0, ',', '.') ?>₫</strong></p>
                        <a href="includes/remove_from_cart.php?id=<?= $item['id'] ?>" class="remove-btn text-danger">Xoá</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-total text-end mt-4">
            <h3>Tổng cộng: <?= number_format($total, 0, ',', '.') ?>₫</h3>
            <a href="includes/checkout.php" class="btn btn-success">Thanh toán</a>
        </div>
    <?php endif; ?>
</section>