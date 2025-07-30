<?php
require 'db.php';

$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$email = $_POST['email'] ?? null;
$dob = $_POST['dob'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

$is_student = isset($_POST['student']) ? 1 : 0;
$subscribe = isset($_POST['newsletter']) ? 1 : 0;

if ($password !== $confirm) {
    die("Mật khẩu không khớp!");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

try {
    // Kiểm tra username trùng
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $check->execute([$phone]);
    $exists = $check->fetchColumn();

    if ($exists > 0) {
        die("Số điện thoại này đã được đăng ký. Vui lòng dùng số khác.");
    }

    //Insert nếu không trùng
    $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, email, phone, dob, role, is_student, subscribe) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $phone,
        $hashed,
        $fullname,
        $email,
        $phone,
        $dob,
        'user',
        $is_student,
        $subscribe
    ]);

    echo "Đăng ký thành công!";
    header("Location: ../login.php"); exit;

} catch (PDOException $e) {
    echo "Lỗi đăng ký: " . $e->getMessage();
}
?>
