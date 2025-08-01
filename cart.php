<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

$show_categories = !isset($_GET['cart_view']);

if (!isset($_SESSION['user_id'])) {
    echo "<p class='cart-message'>Vui lòng <a href='login.php'>đăng nhập</a> để xem giỏ hàng.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

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
<style>
    /* Thêm CSS mới */
    .cart-center-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    .cart-total-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
    }
    
    @media (max-width: 768px) {
        .cart-center-container {
            width: 100%;
            padding: 0 10px;
        }
        
        .cart-total-container {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <?php if ($show_categories): ?>
        <div class="col-md-3">
            <?php include 'includes/categories_sidebar.php'; ?>
        </div>
        <?php endif; ?>

        <div class="<?= $show_categories ? 'col-md-9' : 'col-md-12' ?>">
            <div class="cart-center-container"> <!-- Thêm container căn giữa -->
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
                        <h2 class="cart-title text-center">🛒 Giỏ hàng của bạn</h2> <!-- Thêm text-center -->

                        <div class="cart-list">
                            <?php foreach ($cartItems as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <div class="cart-item">
                                    <img src="<?= $item['image'] ?: 'assets/images/default-food.png' ?>" alt="<?= $item['name'] ?>">
                                    <div class="cart-info">
                                        <h3 class="text-center"><?= htmlspecialchars($item['name']) ?></h3> <!-- Thêm text-center -->
                                        <p class="text-muted text-center">Giá: <strong><?= number_format($item['price'], 0, ',', '.') ?>₫</strong></p>
                                        
                                        <div class="d-flex justify-content-center align-items-center gap-3 mt-2"> <!-- Thêm justify-content-center -->
                                            <form action="includes/update_cart.php" method="POST" class="d-flex gap-2 align-items-center">
                                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="quantity-input">
                                                <button type="submit" class="btn btn-sm btn-update">
                                                    <i class="fas fa-sync-alt"></i> Cập nhật
                                                </button>
                                            </form>
                                            
                                            <a href="includes/remove_from_cart.php?id=<?= $item['id'] ?>" 
                                               class="remove-btn"
                                               onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                                <i class="fas fa-trash-alt"></i> Xóa
                                            </a>
                                        </div>
                                        
                                        <p class="mt-2 text-center">Thành tiền: <strong class="text-primary"><?= number_format($subtotal, 0, ',', '.') ?>₫</strong></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-total-container"> <!-- Thay đổi class -->
                            <h3>Tổng cộng: <?= number_format($total, 0, ',', '.') ?>₫</h3>
                            <a href="includes/checkout.php" class="btn btn-success">Thanh toán</a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>