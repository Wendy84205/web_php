
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location:../login.php');
    exit;
}

require '../../includes/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Không tìm thấy người dùng!";
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'] ?? 'user';
    $status = $_POST['status'] ?? 'active';
    $newPassword = $_POST['new_password'] ?? '';

    if ($fullname === '') $errors[] = 'Họ tên không được để trống';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';

    if (empty($errors)) {
        $params = [$fullname, $email, $phone, $role, $status];

        if ($newPassword !== '') {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET fullname=?, email=?, phone=?, role=?, status=?, password=? WHERE id=?";
            $params[] = $hashed;
        } else {
            $sql = "UPDATE users SET fullname=?, email=?, phone=?, role=?, status=? WHERE id=?";
        }

        $params[] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2>✏️ Sửa thông tin người dùng</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role" class="form-select">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Người dùng</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="blocked" <?= $user['status'] === 'blocked' ? 'selected' : '' ?>>Đã khóa</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu mới (nếu muốn đổi)</label>
            <input type="password" name="new_password" class="form-control" placeholder="Để trống nếu không đổi">
        </div>

        <button class="btn btn-success">💾 Lưu thay đổi</button>
        <a href="users.php" class="btn btn-secondary">↩️ Quay lại</a>
    </form>
</div>
</body>
</html>
