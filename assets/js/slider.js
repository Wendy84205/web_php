// Chức năng slider/banner tự động chuyển và điều khiển bằng nút, dot, tiêu đề

document.addEventListener('DOMContentLoaded', function() {
    const sliderWrapper = document.querySelector('.hero-slider-wrapper'); // Lấy khung slider
    if (!sliderWrapper) return; // Nếu không có slider thì thoát

    const slides = document.querySelectorAll('.hero-slide'); // Lấy các slide
    const dots = document.querySelectorAll('.dot'); // Lấy các chấm tròn
    const titles = document.querySelectorAll('.slider-title'); // Lấy các tiêu đề
    const prevBtn = document.querySelector('.slider-nav.prev'); // Nút prev
    const nextBtn = document.querySelector('.slider-nav.next'); // Nút next
    
    let currentSlide = 0;
    const slideCount = slides.length;
    let autoplayInterval;

    // Cập nhật vị trí slider và trạng thái dot, title
    function updateSlider() {
        slides.forEach((slide, index) => {
            slide.style.transform = `translateX(${(index - currentSlide) * 100}%)`;
            slide.style.transition = 'transform 0.5s ease-in-out';
        });
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
        titles.forEach((title, index) => {
            title.classList.toggle('active', index === currentSlide);
        });
    }
    // Chuyển sang slide tiếp theo
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slideCount;
        updateSlider();
    }
    // Chuyển về slide trước
    function prevSlide() {
        currentSlide = (currentSlide - 1 + slideCount) % slideCount;
        updateSlider();
    }
    // Tự động chuyển slide
    function startAutoplay() {
        autoplayInterval = setInterval(nextSlide, 5000);
    }
    function resetAutoplay() {
        clearInterval(autoplayInterval);
        startAutoplay();
    }
    // Sự kiện cho nút prev/next
    if (prevBtn) prevBtn.addEventListener('click', () => {
        prevSlide();
        resetAutoplay();
    });
    if (nextBtn) nextBtn.addEventListener('click', () => {
        nextSlide();
        resetAutoplay();
    });
    // Sự kiện cho dot
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            updateSlider();
            resetAutoplay();
        });
    });
    // Sự kiện cho tiêu đề
    titles.forEach((title, index) => {
        title.addEventListener('click', () => {
            currentSlide = index;
            updateSlider();
            resetAutoplay();
        });
    });
    // Khởi tạo slider
    updateSlider();
    startAutoplay();
    // Dừng tự động khi hover
    sliderWrapper.addEventListener('mouseenter', () => clearInterval(autoplayInterval));
    sliderWrapper.addEventListener('mouseleave', startAutoplay);
}); 