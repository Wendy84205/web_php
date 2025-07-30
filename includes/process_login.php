<?php
session_start();
require 'db.php';

// Lấy thông tin từ form
$username = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

try {
    // Truy vấn thông tin người dùng
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Kiểm tra mật khẩu
    if ($user && password_verify($password, $user['password'])) {
        // Gán thông tin vào session sau khi xác thực thành công
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['fullname'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'] ?? 'user';

        // Nếu có URL trước đó (như cart.php), quay lại đó
        if (!empty($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        } else {
            header("Location: ../index.php");
        }
        exit;
    } else {
        echo "<script>alert('Sai số điện thoại hoặc mật khẩu!'); window.location.href = '../login.php';</script>";
        exit;
    }

} catch (PDOException $e) {
    echo "Lỗi đăng nhập: " . $e->getMessage();
}
?>
<?php 
session_start();
require_once 'includes/db.php';

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Truy vấn kiểm tra thông tin đăng nhập
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Kiểm tra xem người dùng có tồn tại không và mật khẩu có đúng không
    if ($user && password_verify($password, $user['password'])) {
        // Lưu thông tin vào session
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        
        // Điều hướng đến trang Dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Nếu đăng nhập sai
        $_SESSION['error'] = "Sai tên đăng nhập hoặc mật khẩu!";
        header("Location: login.php");
        exit();
    }
}
?>
