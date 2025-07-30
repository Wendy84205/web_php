<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<?php
require_once 'includes/db.php'; // 
?>
<?php
require 'includes/db.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$products = $stmt->fetchAll();
?>

<!-- index.php -->
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/main.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<main>
    <div class="main-hero-section">
        <!-- Categories Navigation -->
        <!-- Categories Navigation -->
        <div class="nav-categories">
            <div class="nav-categories-card">
                <div class="nav-category-list">
                    <?php
                    $catStmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
                    $categories = $catStmt->fetchAll();
                    foreach ($categories as $cat):
                        ?>
                        <a href="category.php?id=<?= $cat['id'] ?>" class="nav-category-item">
                            <i class="fas fa-utensils"></i>
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- Hero Slider Section -->
        <div class="hero-slider-wrapper">
            <div class="hero-slider">
                <div class="hero-slider-container">
                    <div class="hero-slider-wrapper">
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                        <div class="hero-slide">
                            <img src="" alt="Khuyến mãi">
                        </div>
                    </div>
                    <!-- Add Navigation Buttons -->
                    <button class="slider-nav prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slider-nav next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <!-- Add Navigation Dots -->
                    <div class="slider-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>
            <!-- Add Slider Titles -->
            <div class="slider-titles">
                <div class="slider-title active" data-slide="0">
                    <span class="title-main"></span>
                    <span class="title-sub"></span>
                </div>
                <div class="slider-title" data-slide="1">
                    <span class="title-main"></span>
                    <span class="title-sub"></span>
                </div>
                <div class="slider-title" data-slide="2">
                    <span class="title-main"></span>
                    <span class="title-sub"></span>
                </div>
                <div class="slider-title" data-slide="3">
                    <span class="title-main"></span>
                    <span class="title-sub"></span>
                </div>
                <div class="slider-title" data-slide="4">
                    <span class="title-main"></span>
                    <span class="title-sub"></span>
                </div>
            </div>
        </div>

        <!-- Hero Banners -->
        <div class="hero-banners">
            <div class="hero-banner">
                <img src="" alt="Khuyến mãi đặc biệt 1">
            </div>
            <div class="hero-banner">
                <img src="" alt="Special Promotion 2">
            </div>
            <div class="hero-banner">
                <img src="" alt="Khuyến mãi đặc biệt 3">
            </div>
        </div>
    </div>

    <section class="product-listing">
        <div class="container">
            <h2 class="section-title"></h2>
            <div class="products-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
                foreach ($stmt as $product) {
                    include 'components/product-card.php';
                }
                ?>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>