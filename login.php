
<!-- login.php -->
<link rel="stylesheet" href="assets/css/login.css">

<div class="login-container">
    <img src="assets/images/chibi2.webp" alt="Smember" class="smember-logo">
    <h1>Đăng nhập với</h1>

    <div class="social-login">
        <button type="button" class="social-btn">
            <img src="assets/images/google.png" alt="Google"> Google
        </button>
        <button type="button" class="social-btn">
            <img src="assets/images/zalo.png" alt="Zalo"> Zalo
        </button>
    </div>

    <div class="divider">hoặc</div>

   <form id="loginForm" method="POST" action="includes/process_login.php">
        <div class="form-group">
            <input type="text" name="phone" placeholder="Nhập số điện thoại" 
                   value="<?php echo $_COOKIE['remember_phone'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" id="remember" name="remember" 
                <?php echo isset($_COOKIE['remember_phone']) ? 'checked' : ''; ?>>
            <label for="remember">Ghi nhớ đăng nhập</label>
        </div>

        <!-- reCAPTCHA -->
        <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>

        <div class="forgot-password">
            <a href="#">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="login-btn">Đăng nhập</button>
    </form>

    <div class="register-link">
        Bạn chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
    </div>

    <div class="policy-link">
        <a href="#">Xem chính sách ưu đãi Smember</a>
    </div>
</div>

<!-- reCAPTCHA script -->
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
