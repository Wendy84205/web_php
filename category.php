<?php
require 'includes/db.php';
require 'includes/header.php';

$category_id = $_GET['id'] ?? null;

if (!$category_id) {
    echo "Danh mục không tồn tại.";
    exit;
}

// Lấy thông tin danh mục
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    echo "Không tìm thấy danh mục.";
    exit;
}

// Lấy sản phẩm theo danh mục
$stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
$stmt->execute([$category_id]);
$products = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>🍽 Danh mục: <?= htmlspecialchars($category['name']) ?></h2>
    <div class="row mt-3">
        <?php if (empty($products)): ?>
            <p>Không có sản phẩm nào trong danh mục này.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <?php include 'components/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
