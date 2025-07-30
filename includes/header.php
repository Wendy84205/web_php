<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php'; // ✅ Đảm bảo đúng path đến db.php

$cartCount = 0;
$isLoggedIn = isset($_SESSION['user_id']);

// Đếm số lượng sản phẩm trong giỏ nếu đã đăng nhập
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartCount = $stmt->fetchColumn() ?? 0;
}

// Lấy danh sách danh mục
$categories = [];
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wendy Food | Đặt món ngon mỗi ngày</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">

    <header class="top-header">
        <div class="top-header-container">
            <a href="index.php" class="logo"><img src="assets/images/logo.png" alt="Wendy"></a>

            <div class="dropdown category-menu">
                <?php
                $currentPage = basename($_SERVER['PHP_SELF']);
                $hideCategoryMenu = in_array($currentPage, ['category.php', 'product-detail.php']);
                ?>

                <?php if (!$hideCategoryMenu): ?>
                    <div class="dropdown category-menu">
                        <button class="menu-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-bars"></i> <span>Danh mục</span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a class="dropdown-item" href="category.php?id=<?= $category['id'] ?>">
                                        <i class="fas fa-utensils me-1"></i> <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>

            <div class="location-select">
                <i class="fas fa-map-marker-alt"></i><span>Ho Chi Minh</span>
                <i class="fas fa-chevron-down"></i>
            </div>

            <div class="search-wrapper">
                <input type="text" class="search-input" placeholder="Bạn muốn tìm món gì?">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>

            <!-- Cart icon -->
            <a href="<?= $isLoggedIn ? 'cart.php' : '#' ?>" class="action-btn" id="cart-button"
                data-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Giỏ hàng</span>
                <div class="cart-count"><?= $cartCount ?></div>
            </a>

            <!-- User Info -->
            <?php if (isset($_SESSION['username'])): ?>
                <a href="includes/order_history.php" class="action-btn">
                    <i class="fas fa-receipt"></i>
                    <span>Lịch sử</span>
                </a>
                <div class="action-btn user-info">
                    <i class="fas fa-user"></i>
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="includes/logout.php" class="logout-link">(Đăng xuất)</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="action-btn" id="login-button">
                    <i class="fas fa-user"></i>
                    <span>Đăng nhập</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- 🔽 LOGIN MODAL -->
    <div id="loginModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <div id="login-form-container">
                <div class="smember-header">
                    <h2>Smember</h2>
                    <img src="assets/images/chibi2.webp" alt="Smember Mascot">
                </div>
                <p class="login-message">Vui lòng đăng nhập để xem ưu đãi và thanh toán dễ dàng hơn.</p>
                <div class="login-buttons">
                    <button class="btn-register">Đăng ký</button>
                    <button class="btn-login">Đăng nhập</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔽 JAVASCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginModal = document.getElementById("loginModal");
            const loginButton = document.getElementById("login-button");
            const cartButton = document.getElementById("cart-button");
            const closeButton = loginModal?.querySelector(".modal-close");
            const registerBtn = loginModal?.querySelector(".btn-register");
            const loginBtn = loginModal?.querySelector(".btn-login");

            const isLoggedIn = document.body.dataset.loggedIn === "true";

            // Mở modal khi nhấn login
            loginButton?.addEventListener("click", function (e) {
                e.preventDefault();
                loginModal.style.display = "block";
                document.body.style.overflow = "hidden";
            });

            // Mở modal khi nhấn cart (nếu chưa login)
            cartButton?.addEventListener("click", function (e) {
                if (!isLoggedIn) {
                    e.preventDefault();
                    loginModal.style.display = "block";
                    document.body.style.overflow = "hidden";
                }
            });

            // Đóng modal bằng X
            closeButton?.addEventListener("click", () => {
                loginModal.style.display = "none";
                document.body.style.overflow = "auto";
            });

            // Đăng nhập
            loginBtn?.addEventListener("click", () => {
                window.location.href = "login.php";
            });

            // Đăng ký
            registerBtn?.addEventListener("click", () => {
                window.location.href = "register.php";
            });

            // Click ra ngoài modal
            window.addEventListener("click", function (e) {
                if (e.target === loginModal) {
                    loginModal.style.display = "none";
                    document.body.style.overflow = "auto";
                }
            });

            // Đóng bằng ESC
            document.addEventListener("keydown", function (e) {
                if (e.key === "Escape" && loginModal.style.display === "block") {
                    loginModal.style.display = "none";
                    document.body.style.overflow = "auto";
                }
            });
        });
    </script>