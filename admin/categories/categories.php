<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

// Lấy danh sách danh mục
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

<div class="content">
    <h2 class="mb-4">📂 Danh mục sản phẩm</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <a href="categories_add.php" class="btn btn-success mb-3">
        <i class="fas fa-plus"></i> Thêm danh mục
    </a>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Tên danh mục</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($categories)): ?>
            <tr>
                <td colspan="3" class="text-center">Chưa có danh mục nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categories as $index => $cat): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td>
                        <a href="categories_edit.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">
                            ✏️ Sửa
                        </a>
                        <a href="categories_delete.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Bạn có chắc muốn xoá danh mục này?')">
                            🗑️ Xoá
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
