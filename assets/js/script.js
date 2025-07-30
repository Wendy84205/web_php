// Chức năng thêm vào giỏ hàng
// Lặp qua tất cả các nút có class 'btn'
document.querySelectorAll('.btn').forEach(button => {
    // Gán sự kiện click cho từng nút
    button.addEventListener('click', function() {
        // Nếu nút có nội dung là 'Add to Cart' thì hiển thị thông báo
        if (this.textContent === 'Add to Cart') {
            alert('Product added to cart!'); // Thông báo đã thêm sản phẩm vào giỏ hàng
        }
    });
});

// Chức năng tìm kiếm sản phẩm
const searchBar = document.querySelector('.search-bar input'); // Lấy ô input tìm kiếm
const searchButton = document.querySelector('.search-bar button'); // Lấy nút tìm kiếm

// Gán sự kiện click cho nút tìm kiếm
searchButton.addEventListener('click', function() {
    const searchTerm = searchBar.value.trim(); // Lấy giá trị nhập vào và loại bỏ khoảng trắng
    if (searchTerm) {
        alert(`Searching for: ${searchTerm}`); // Thông báo từ khóa tìm kiếm
        // Thông thường ở đây sẽ gọi API để tìm kiếm sản phẩm
    }
});

// Chức năng menu di động (responsive)
function createMobileMenu() {
    const nav = document.querySelector('.nav-links'); // Lấy phần menu
    const mobileMenuBtn = document.createElement('button'); // Tạo nút menu di động
    mobileMenuBtn.className = 'mobile-menu-btn';
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>'; // Thêm icon menu
    
    document.querySelector('.main-nav .container').prepend(mobileMenuBtn); // Thêm nút vào đầu thanh điều hướng

    // Gán sự kiện click để hiện/ẩn menu
    mobileMenuBtn.addEventListener('click', () => {
        nav.classList.toggle('active'); // Thêm hoặc xóa class 'active' để hiện/ẩn menu
    });
}

// Khởi tạo menu di động nếu màn hình nhỏ hơn hoặc bằng 768px
if (window.innerWidth <= 768) {
    createMobileMenu();
}

// Hiệu ứng hover cho sản phẩm
// Lặp qua tất cả các thẻ sản phẩm
document.querySelectorAll('.product-card').forEach(card => {
    // Khi rê chuột vào sản phẩm
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)'; // Đẩy sản phẩm lên trên 5px
    });

    // Khi rời chuột khỏi sản phẩm
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)'; // Đưa sản phẩm về vị trí cũ
    });
});

// Cuộn mượt cho các liên kết điều hướng nội bộ
// Lặp qua tất cả các thẻ a có href bắt đầu bằng '#'
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault(); // Ngăn chuyển trang mặc định
        const target = document.querySelector(this.getAttribute('href')); // Lấy phần tử mục tiêu
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth' // Cuộn mượt đến phần tử mục tiêu
            });
        }
    });
});

// Chức năng hiển thị modal đăng nhập
// Đợi trang tải xong mới thực thi
document.addEventListener('DOMContentLoaded', function() {
    const loginLink = document.querySelector('.login-link'); // Lấy liên kết đăng nhập
    const loginModal = document.getElementById('loginModal'); // Lấy modal đăng nhập
    const closeButton = loginModal.querySelector('.modal-close'); // Lấy nút đóng modal

    // Khi click vào liên kết đăng nhập thì hiện modal
    loginLink.addEventListener('click', function(e) {
        e.preventDefault(); // Ngăn chuyển trang mặc định
        loginModal.style.display = 'block'; // Hiện modal
    });

    // Khi click vào nút đóng thì ẩn modal
    closeButton.addEventListener('click', function() {
        loginModal.style.display = 'none'; // Ẩn modal
    });

    // Khi click ra ngoài modal thì cũng ẩn modal
    window.addEventListener('click', function(e) {
        if (e.target === loginModal) {
            loginModal.style.display = 'none'; // Ẩn modal
        }
    });
});
// Chức năng slider cho trang chủ
    document.addEventListener('DOMContentLoaded', function() {
        const sliderWrapper = document.querySelector('.hero-slider-wrapper');
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.dot');
        const titles = document.querySelectorAll('.slider-title');
        const prevBtn = document.querySelector('.slider-nav.prev');
        const nextBtn = document.querySelector('.slider-nav.next');
        
        let currentSlide = 0;
        const slideCount = slides.length;
        let autoplayInterval;

        // Function to update the slider position and styles
        function updateSlider() {
            slides.forEach((slide, index) => {
                slide.style.transform = `translateX(${(index - currentSlide) * 100}%)`;
                slide.style.transition = 'transform 0.5s ease-in-out';
            });
            
            // Update dots and titles
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
            titles.forEach((title, index) => {
                title.classList.toggle('active', index === currentSlide);
            });
        }

        // Function to go to next slide
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slideCount;
            updateSlider();
        }

        // Function to go to previous slide
        function prevSlide() {
            currentSlide = (currentSlide - 1 + slideCount) % slideCount;
            updateSlider();
        }

        // Event listeners for navigation buttons
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });

        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });

        // Event listeners for dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateSlider();
                resetAutoplay();
            });
        });

        // Event listeners for titles
        titles.forEach((title, index) => {
            title.addEventListener('click', () => {
                currentSlide = index;
                updateSlider();
                resetAutoplay();
            });
        });

        // Autoplay functionality
        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        function resetAutoplay() {
            clearInterval(autoplayInterval);
            startAutoplay();
        }

        // Initialize slider
        updateSlider();
        startAutoplay();

        // Pause autoplay on hover
        sliderWrapper.addEventListener('mouseenter', () => clearInterval(autoplayInterval));
        sliderWrapper.addEventListener('mouseleave', startAutoplay);
});

// Chức năng tìm kiếm sản phẩm
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    const productCards = document.querySelectorAll('.product-card');

    function filterProducts() {
        const keyword = searchInput.value.trim().toLowerCase();
        productCards.forEach(card => {
            const name = card.querySelector('.product-name').textContent.toLowerCase();
            if (name.includes(keyword) || keyword === "") {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }   
        });
    }

    // Khi nhấn nút tìm kiếm
    searchBtn.addEventListener('click', filterProducts);

    // Khi nhấn Enter trong ô input
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') filterProducts();
    });
});
