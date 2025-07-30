<?php
if (!isset($product)) return;

$image = !empty($product['image']) ? 'uploads/' . $product['image'] : 'assets/images/default-product.jpg';
$name = $product['name'] ?? '';
$price = number_format($product['price'], 0, ',', '.') . '₫';
$original_price = isset($product['original_price']) ? number_format($product['original_price'], 0, ',', '.') . '₫' : '';
$discount = isset($product['discount_percentage']) ? $product['discount_percentage'] . '% Off' : '';
$installment = '0% Installment';
?>

<!-- Thẻ card sản phẩm -->
<div class="product-card">
    <!-- Link đến trang chi tiết sản phẩm -->
    <a href="product_detail.php?id=<?= $product['id'] ?>">Chi tiết</a>
        <div class="product-badge">
            <?php if (!empty($discount)): ?>
                <span class="discount"><?= $discount ?></span>
            <?php endif; ?>
            <span class="installment"><?= $installment ?></span>
        </div>
        <div class="product-image">
            <img src="<?= $image ?>" alt="<?= htmlspecialchars($name) ?>" class="img-fluid">
        </div>
        <div class="product-info">
            <h3 class="product-name"><?= htmlspecialchars($name) ?></h3>
            <div class="product-price">
                <span class="current-price"><?= $price ?></span>
                <?php if (!empty($original_price)): ?>
                    <span class="original-price"><?= $original_price ?></span>
                <?php endif; ?>
            </div>
            <?php if (!empty($product['promo1'])): ?>
                <div class="product-promo">
                    <p><?= htmlspecialchars($product['promo1']) ?></p>
                    <p><?= htmlspecialchars($product['promo2'] ?? '') ?></p>
                </div>
            <?php endif; ?>
            <div class="product-rating">
                <div class="stars">★★★★★</div>
                <button class="wishlist-btn" type="button">
                    <span>Favorite</span>
                    <i class="far fa-heart"></i>
                </button>
            </div>
        </div>
    </a>

    <!-- Nút thêm vào giỏ phải ở ngoài thẻ <a> -->
    <form method="POST" action="includes/add_to_cart.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="quantity" value="1">
        <button class="btn btn-primary w-100 mt-2" type="submit">🛒 Thêm vào giỏ</button>
    </form>
</div>

