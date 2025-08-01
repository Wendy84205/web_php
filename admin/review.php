<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

// Xử lý thao tác xóa
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['message'] = 'Đã xóa đánh giá thành công!';
    header('Location: review.php');
    exit;
}

// Lấy danh sách đánh giá
$stmt = $pdo->query("
    SELECT r.*, u.username, p.name as product_name 
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN products p ON r.product_id = p.id
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đánh giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .rating-stars {
            color: #ffc107;
        }
        .table-responsive {
            min-height: 400px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quản lý Đánh giá</h1>
                </div>

                <!-- Thông báo -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Sản phẩm</th>
                                <th>Người đánh giá</th>
                                <th>Đánh giá</th>
                                <th>Nội dung</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($reviews) > 0): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($review['id']) ?></td>
                                        <td><?= htmlspecialchars($review['product_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($review['username'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="rating-stars">
                                                <?= str_repeat('<i class="bi bi-star-fill"></i>', $review['rating']) ?>
                                                <?= str_repeat('<i class="bi bi-star"></i>', 5 - $review['rating']) ?>
                                            </span>
                                            (<?= $review['rating'] ?>)
                                        </td>
                                        <td><?= htmlspecialchars(substr($review['comment'], 0, 50)) ?><?= strlen($review['comment']) > 50 ? '...' : '' ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></td>
                                        <td>
                                            <a href="review.php?action=delete&id=<?= $review['id'] ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                                <i class="bi bi-trash"></i> Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">Không có đánh giá nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>