
<?php
// Hash mật khẩu trước khi insert vào database
$password = password_hash('admin', PASSWORD_BCRYPT);

// Kết nối cơ sở dữ liệu
require_once '../includes/db.php';

// Kiểm tra xem username đã tồn tại chưa
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => 'admin']);
$user = $stmt->fetch();

if ($user) {
    echo "Tài khoản admin đã tồn tại!";
} else {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, email, phone, dob, role, is_student, subscribed_newsletter)
    VALUES (:username, :password, :fullname, :email, :phone, :dob, :role, :is_student, :subscribed_newsletter)");

    $stmt->execute([
        ':username' => 'admin',
        ':password' => $password,
        ':fullname' => 'Admin User',
        ':email' => 'admin@example.com',
        ':phone' => '0123456789',
        ':dob' => '1990-01-01',
        ':role' => 'admin',
        ':is_student' => 0,
        ':subscribed_newsletter' => 0
    ]);

    echo "Tài khoản admin đã được tạo thành công!";
}
?>
