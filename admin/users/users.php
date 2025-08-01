<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

// Tìm kiếm
$keyword = $_GET['keyword'] ?? '';
$sql = "SELECT * FROM users WHERE 1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
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
        <a href="categories_list.php" class="active"><i class="fas fa-tags me-2"></i> Categories</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <div class="container py-5">
        <h2>👥 Danh sách người dùng</h2>

        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="keyword" placeholder="Tìm theo tên, email, số điện thoại" class="form-control"
                style="width: 300px;" value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-primary">🔍 Tìm kiếm</button>
            <a href="export_users_pdf.php" class="btn btn-danger">📄 Xuất PDF</a>
            <a href="export_users_excel.php" class="btn btn-success">📊 Xuất Excel</a>
        </form>

        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Quyền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có người dùng nào.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($user['fullname']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td>
                            <span class="badge <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                <?= $user['role'] === 'admin' ? 'Admin' : 'Người dùng' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $user['status'] === 'active' ? 'badge-active' : 'badge-blocked' ?>">
                                <?= $user['status'] === 'active' ? 'Đang hoạt động' : 'Đã khóa' ?>
                            </span>
                        </td>
                        <td class="action-btns">
                            <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                            <?php if ($user['status'] === 'active'): ?>
                                <a href="user_block.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">🔒 Khóa</a>
                            <?php else: ?>
                                <a href="user_unblock.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-success"
                                    onclick="return confirm('Mở khóa tài khoản này?')">🔓 Mở khóa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</body>

</html>