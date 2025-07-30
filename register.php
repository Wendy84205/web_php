<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/register.css">
    <title>Wendy.com - Đăng ký</title>
</head>
<body>
    <div class="register-container">
        <img src="assets/images/chibi2.webp" alt="Smember" class="smember-logo">
        <h1>Đăng ký với</h1>
        
        <div class="social-login">
            <button type="button" class="social-btn">
                <img src="assets/images/google.png" alt="Google">
                Google
            </button>
            <button type="button" class="social-btn">
                <img src="assets/images/zalo.png" alt="Zalo">
                Zalo
            </button>
        </div>

        <div class="divider">hoặc</div>

        <form id="registerForm" action="includes/process_register.php" method="POST">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Nhập họ và tên" required>
            </div>
            <div class="form-group">
                <input type="tel" name="phone" placeholder="Nhập số điện thoại" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Nhập email (không bắt buộc)">
                <div class="hint-text">Hóa đơn VAT khi mua hàng sẽ được gửi qua email này</div>
            </div>
            <div class="form-group">
                <input type="date" name="dob" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                <div class="hint-text">(*) Mật khẩu tối thiểu 6 ký tự, có ít nhất 1 chữ và 1 số. (VD: 123abc)</div>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="newsletter" name="newsletter">
                <label for="newsletter">Đăng ký nhận bản tin khuyến mãi từ Wendy</label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="terms" required>
                <label for="terms">Tôi đồng ý với các <a href="#">điều khoản sử dụng</a> và <a href="#">chính sách bảo mật</a></label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="student" name="student">
                <label for="student">Tôi là Học sinh - sinh viên / Giáo viên - giảng viên <span class="hint-text">(nhận thêm ưu đãi tới 500K/ sản phẩm)</span></label>
            </div>

            <button type="submit" class="register-btn">Đăng ký</button>
        </form>

        <div class="login-link">
            Bạn đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </div>
    </div>
</body>
</html>
