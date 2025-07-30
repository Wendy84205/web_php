
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require '../../includes/db.php';

// Lấy danh mục
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Không tìm thấy sản phẩm!";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $image = $product['image'];

    if ($name === '') $errors[] = "Tên sản phẩm không được để trống";
    if ($price <= 0) $errors[] = "Giá phải lớn hơn 0";

    // Xử lý ảnh nếu có thay đổi
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../uploads/";
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $filename;
            } else {
                $errors[] = "Lỗi upload ảnh.";
            }
        } else {
            $errors[] = "Chỉ chấp nhận ảnh jpg, png, gif.";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $price, $category_id, $image, $id]);
        header("Location: products.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">✏️ Sửa sản phẩm</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Giá (₫)</label>
                    <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Ảnh hiện tại</label><br>
                <?php if (!empty($product['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="Ảnh sản phẩm" width="120">
                <?php else: ?>
                    <p>Không có ảnh</p>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Thay ảnh mới (nếu có)</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button class="btn btn-primary">Cập nhật</button>
            <a href="products.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>

</html>

