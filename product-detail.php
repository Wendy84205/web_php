<?php
session_start();
require 'includes/db.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    echo "<p style='color:red'>Không tìm thấy sản phẩm phù hợp.</p>";
    exit;
}

// Lấy dữ liệu sản phẩm và thương hiệu
$stmt = $pdo->prepare("
    SELECT p.*, b.name AS brand_name, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<p style='color:red'>Sản phẩm không tồn tại!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> | Chi tiết sản phẩm</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item">
                <a href="category.php?slug=<?= urlencode($product['category_slug']) ?>">
                    <?= htmlspecialchars($product['category_name']) ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <!-- Hiển thị chi tiết sản phẩm -->
    <?php include 'components/product-detail-card.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
