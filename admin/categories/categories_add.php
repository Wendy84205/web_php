<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

$error = '';
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name === '') {
        $error = '⚠️ Tên danh mục không được để trống';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);

        $_SESSION['success'] = '✅ Thêm danh mục thành công!';
        header('Location: categories_list.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm danh mục</title>
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
    <h4 class="p-3">🧠 <strong>Wendy</strong></h4>
    <a href="../dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
    <a href="../product/products.php"><i class="fas fa-boxes me-2"></i> Products</a>
    <a href="../orders/orders.php"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
    <a href="../users/users.php"><i class="fas fa-users me-2"></i> Users</a>
    <a href="../review.php"><i class="fas fa-star me-2"></i> Review</a>
    <a href="categories_list.php" class="active"><i class="fas fa-tags me-2"></i> Categories</a>
    <a href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="content">
    <h2>➕ Thêm danh mục</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên danh mục</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control" required>
        </div>
        <button class="btn btn-success">💾 Lưu</button>
        <a href="categories.php" class="btn btn-secondary">↩️ Quay lại</a>
    </form>
</div>
</body>
</html>
