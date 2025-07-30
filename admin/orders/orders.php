<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

// Lọc theo trạng thái nếu có
$status = $_GET['status'] ?? '';
$sql = "SELECT * FROM orders";
$params = [];

if ($status) {
    $sql .= " WHERE status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
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

        .sidebar a:hover,
        .sidebar .active {
            background-color: #495057;
            color: #fff;
        }

        .content {
            margin-left: 230px;
            padding: 30px;
        }

        /* Định dạng cho bảng và thẻ td/th */
        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
        }

        /* Mã theo dõi format đẹp hơn */
        .table td.order-code {
            font-weight: bold;
            color: #0d6efd;
        }

        /* Nhóm hành động */
        .action-links a {
            margin: 0 4px;
            text-decoration: none;
            font-weight: 500;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .action-links a.text-danger {
            color: red;
        }

        .action-links a.text-danger:hover {
            font-weight: bold;
        }

        /* Nút badge trạng thái */
        .badge.pending {
            background-color: orange;
        }

        .badge.shipping {
            background-color: dodgerblue;
        }

        .badge.done {
            background-color: seagreen;
        }

        .badge.cancel {
            background-color: crimson;
        }
    </style>
</head>

<body class="bg-light">
    <div class="sidebar">
        <h4 class="p-3"><strong>Wendy</strong></h4>
        <a href="../dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
        <a href="../products/products.php"><i class="fas fa-boxes me-2"></i> Products</a>
        <a href="../orders/orders.php"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
        <a href="../users/users.php"><i class="fas fa-users me-2"></i> Users</a>
        <a href="../review.php"><i class="fas fa-star me-2"></i> Review</a>
        <a href="categories_list.php" class="active"><i class="fas fa-tags me-2"></i> Categories</a>
        <a href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <div class="container py-5">
        <h2 class="mb-4">📦 Quản lý đơn hàng</h2>

        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="keyword" placeholder="Tên người dùng..." class="form-control"
                style="width: 200px;">
            <input type="date" name="date" class="form-control" style="width: 180px;">
            <select name="status" class="form-select" style="width: 160px;">
                <option value="">-- Trạng thái --</option>
                <option value="pending">Chờ xử lý</option>
                <option value="shipping">Đang giao</option>
                <option value="done">Đã giao</option>
                <option value="cancel">Đã hủy</option>
            </select>
            <button class="btn btn-primary">Lọc</button>
        </form>

        <?php
        $sql = "SELECT o.* FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($_GET['keyword'])) {
            $sql .= " AND u.fullname LIKE ?";
            $params[] = '%' . $_GET['keyword'] . '%';
        }

        if (!empty($_GET['date'])) {
            $sql .= " AND DATE(o.created_at) = ?";
            $params[] = $_GET['date'];
        }

        if (!empty($_GET['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $_GET['status'];
        }
        ?>

        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                    <th>Mã theo dõi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có đơn hàng nào.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($orders as $index => $order): ?>
                    <?php
                    $statusClass = match ($order['status']) {
                        'pending' => 'pending',
                        'shipping' => 'shipping',
                        'done' => 'done',
                        'cancel' => 'cancel',
                        default => 'secondary'
                    };
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <span class="badge <?= $statusClass ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </td>
                        <td class="order-code"><?= 'ORD-' . str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td class="action-links">
                            <a href="order_detail.php?id=<?= $order['id'] ?>">Chi tiết</a>
                            <a href="order_update.php?id=<?= $order['id'] ?>">Cập nhật</a>
                            <a href="print_order.php?id=<?= $order['id'] ?>" target="_blank">In</a>
                            <a href="delete_order.php?id=<?= $order['id'] ?>" onclick="return confirm('Xóa đơn hàng này?')"
                                class="text-danger">Xóa</a>
                        </td>
                    </tr>

                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <?php
    // Khai báo limit và trang
    $limit = 10;
    $page = max(1, intval($_GET['page'] ?? 1));
    $offset = ($page - 1) * $limit;

    // Xây dựng lại câu query với LIMIT + OFFSET
    $sql .= " ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    // Đếm tổng số đơn để phân trang
    $countSql = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
    $countParams = [];

    if (!empty($_GET['keyword'])) {
        $countSql .= " AND u.fullname LIKE ?";
        $countParams[] = '%' . $_GET['keyword'] . '%';
    }
    if (!empty($_GET['date'])) {
        $countSql .= " AND DATE(o.created_at) = ?";
        $countParams[] = $_GET['date'];
    }
    if (!empty($_GET['status'])) {
        $countSql .= " AND o.status = ?";
        $countParams[] = $_GET['status'];
    }

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalOrders = $countStmt->fetch()['total'] ?? 0;
    $totalPages = ceil($totalOrders / $limit);
    ?>

    <!--<nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav> -->

</body>

</html>