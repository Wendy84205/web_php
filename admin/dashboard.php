<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

// Tổng sản phẩm
$stmt = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
$totalProducts = $stmt->fetch()['total_products'];

// Tổng đơn hàng
$stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
$totalOrders = $stmt->fetch()['total_orders'];

// Tổng người dùng
$stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmt->fetch()['total_users'];

// Tổng doanh thu (đơn hàng đã giao hàng)
$stmt = $pdo->query("SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status = 'completed'");
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;

// Doanh thu theo tháng (chỉ tính đơn hàng đã hoàn thành)
$stmt = $pdo->query("
    SELECT MONTH(created_at) AS month, SUM(total_amount) AS total 
    FROM orders 
    WHERE status = 'completed' 
    GROUP BY MONTH(created_at) 
    ORDER BY MONTH(created_at)
");

$monthlyRevenue = array_fill(0, 12, 0); // Mặc định doanh thu 12 tháng = 0

while ($row = $stmt->fetch()) {
    $monthlyRevenue[$row['month'] - 1] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            height: auto;
            background-color: #343a40;
            padding: 20px;
        }

        .sidebar a {
            color: #adb5bd;
            display: block;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }

        .topbar {
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white">Wendy</h4>
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products/products.php"><i class="fas fa-box"></i> Products</a>
                <a href="orders/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users/users.php"><i class="fas fa-users"></i> Users</a>
                <a href="review.php"><i class="fas fa-star"></i> Review</a>
                <a href="categories/categories.php"><i class="fas fa-tags"></i> Categories</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>

            <!-- Main -->
            <div class="col-md-10">
                <div class="topbar d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Dashboard</h5>
                    <div>
                        <span class="me-2">Chào Admin</span>
                        <img src="https://i.pravatar.cc/40" class="rounded-circle" alt="avatar">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Sản phẩm</h6>
                                <h3 class="card-text"><?= $totalProducts ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Đơn hàng</h6>
                                <h3 class="card-text"><?= $totalOrders ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Người dùng</h6>
                                <h3 class="card-text"><?= $totalUsers ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Doanh thu</h6>
                                <h3 class="card-text"><?= number_format($totalRevenue) ?> ₫</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="card">
                    <div class="card-header">Doanh thu theo tháng</div>
                    <div class="card-body">
                        <canvas id="chartRevenue"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const revenueData = <?= json_encode($monthlyRevenue) ?>;
        const ctx = document.getElementById('chartRevenue').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
                datasets: [{
                    label: 'Doanh thu theo tháng',
                    data: revenueData,
                    backgroundColor: '#4e73df',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => value.toLocaleString() + ' ₫'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>