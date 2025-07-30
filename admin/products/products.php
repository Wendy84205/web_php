<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

// --- CẤU HÌNH PHÂN TRANG ---
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// --- TÌM KIẾM VÀ LỌC ---
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// --- LẤY DANH MỤC ---
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// --- ĐẾM TỔNG SP PHÙ HỢP ---
$countSQL = "SELECT COUNT(*) FROM products WHERE 1";
$params = [];

if ($search) {
    $countSQL .= " AND name LIKE :search";
    $params['search'] = "%$search%";
}
if ($category) {
    $countSQL .= " AND category_id = :category";
    $params['category'] = $category;
}
$stmt = $pdo->prepare($countSQL);
$stmt->execute($params);
$totalProducts = $stmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// --- TRUY VẤN DỮ LIỆU SẢN PHẨM ---
$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1";
if ($search) $sql .= " AND p.name LIKE :search";
if ($category) $sql .= " AND p.category_id = :category";
$sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            color: white;
        }
        .sidebar a {
            color: #adb5bd;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar .active {
            background-color: #495057;
            color: #fff;
        }
        .content {
            margin-left: 230px;
            padding: 30px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4 class="p-3"><strong>Wendy</strong></h4>
    <a href="../dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
    <a href="../products/products.php"><i class="fas fa-boxes me-2"></i> Products</a>
    <a href="../orders/orders.php"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
    <a href="../users/users.php"><i class="fas fa-users me-2"></i> Users</a>
    <a href="../review.php"><i class="fas fa-star me-2"></i> Review</a>
    <a href="../categories/categories.php" class="active"><i class="fas fa-tags me-2"></i> Categories</a>
    <a href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="container py-5">
    <h2 class="mb-4">📦 Quản lý sản phẩm</h2>

    <!-- FORM TÌM KIẾM VÀ LỌC -->
    <form class="row search-bar mb-4" method="GET">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="🔍 Tìm sản phẩm..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">📁 Tất cả danh mục</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Lọc</button>
        </div>
        <div class="col-md-3 text-end">
            <a href="add_product.php" class="btn btn-success w-100">+ Thêm sản phẩm</a>
        </div>
    </form>

    <!-- TABLE HIỂN THỊ SẢN PHẨM -->
    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Danh mục</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) === 0): ?>
                <tr><td colspan="7" class="text-center text-muted">Không tìm thấy sản phẩm nào.</td></tr>
            <?php endif; ?>

            <?php foreach ($products as $i => $product): ?>
                <tr>
                    <td><?= $offset + $i + 1 ?></td>
                    <td>
                        <?php if (!empty($product['image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="ảnh">
                        <?php else: ?>
                            <span class="text-muted">Không ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 0, ',', '.') ?> ₫</td>
                    <td><?= htmlspecialchars($product['category_name'] ?? '-') ?></td>
                    <td><?= date('d/m/Y', strtotime($product['created_at'])) ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xoá?')">Xoá</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- PHÂN TRANG -->
    <nav>
