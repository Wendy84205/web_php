
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

// Lấy danh mục sản phẩm
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $original_price = isset($_POST['original_price']) ? floatval($_POST['original_price']) : null;
    $discount_percentage = isset($_POST['discount_percentage']) ? intval($_POST['discount_percentage']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $category_id = intval($_POST['category_id']);
    $image = '';

    // Validate cơ bản
    if ($name === '')
        $errors[] = "Tên sản phẩm không được để trống";
    if ($price <= 0)
        $errors[] = "Giá bán phải lớn hơn 0";
    if ($original_price <= 0)
        $errors[] = "Giá gốc phải lớn hơn 0";
    if ($discount_percentage < 0 || $discount_percentage > 100)
        $errors[] = "Phần trăm giảm giá phải từ 0 đến 100";

    // Xử lý upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../uploads/";
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $filename;
            } else {
                $errors[] = "Lỗi khi upload ảnh.";
            }
        } else {
            $errors[] = "Chỉ cho phép định dạng ảnh jpg, png, gif.";
        }
    }

    // Nếu không có lỗi thì thêm vào DB
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products 
        (name, price, original_price, discount_percentage, description, category_id, image, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $name,
            $price,
            $original_price,
            $discount_percentage,
            $description,
            $category_id,
            $image
        ]);

        header("Location: products.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">🆕 Thêm sản phẩm</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Giá bán (₫)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Giá gốc (₫)</label>
                    <input type="number" name="original_price" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">% Giảm giá</label>
                    <input type="number" name="discount_percentage" class="form-control" step="0.1" min="0" max="100">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ảnh sản phẩm</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button class="btn btn-success">Lưu sản phẩm</button>
            <a href="products.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>

</html>