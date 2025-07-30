<!-- components/product-detail-card.php -->
<div class="product-detail d-flex p-4 gap-4 bg-white shadow rounded">
    <!-- Hình ảnh sản phẩm -->
    <div class="product-gallery col-md-5">
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
             class="img-fluid rounded border">
        <!-- Có thể thêm ảnh phụ ở đây nếu cần -->
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="product-detail-info col-md-7">
        <h2 class="product-title mb-3"><?= htmlspecialchars($product['name']) ?></h2>

        <p class="text-muted mb-2">
            <strong>Thương hiệu:</strong>
            <?= htmlspecialchars($product['brand_name'] ?? 'Không rõ') ?>
        </p>

        <div class="product-pricing mb-3">
            <span class="current-price fw-bold fs-4 text-danger">
                <?= number_format($product['price'], 0, ',', '.') ?>₫
            </span>
            <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                <span class="original-price text-decoration-line-through text-muted ms-2">
                    <?= number_format($product['original_price'], 0, ',', '.') ?>₫
                </span>
                <span class="badge bg-success ms-2">
                    -<?= $product['discount_percentage'] ?? round(100 - $product['price'] / $product['original_price'] * 100) ?>%
                </span>
            <?php endif; ?>
        </div>

        <!-- Mô tả sản phẩm -->
        <div class="product-description mb-3">
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>

        <!-- Hành động mua hàng -->
        <form action="add_to_cart.php" method="POST" class="d-flex gap-2">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-bolt"></i> Mua ngay
            </button>
            <button type="submit" name="add_to_cart" value="1" class="btn btn-outline-secondary">
                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
            </button>
        </form>

        <!-- Đánh giá -->
        <div class="product-rating mt-3">
            <p>⭐⭐⭐⭐⭐ <strong>4.8</strong> | 120 đánh giá</p>
        </div>
    </div>
</div>
