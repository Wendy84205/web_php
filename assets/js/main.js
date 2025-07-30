// DOM Elements
const megaMenu = document.querySelector('.mega-menu');
const categoriesToggle = document.querySelector('.categories-toggle');
const modals = document.querySelectorAll('.modal');
const modalCloseButtons = document.querySelectorAll('.modal-close');

// Global state
let isMenuOpen = false;
let currentSlide = 0;
let slideInterval;

// Utility functions
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
};

const showLoading = () => {
    const loader = document.createElement('div');
    loader.className = 'loader';
    document.body.appendChild(loader);
};

const hideLoading = () => {
    const loader = document.querySelector('.loader');
    if (loader) {
        loader.remove();
    }
};

// API calls
const api = {
    baseUrl: '/api',
    
    async get(endpoint) {
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`);
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    },

    async post(endpoint, data) {
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
};

// Categories
const loadCategories = async () => {
    try {
        const categories = await api.get('/categories');
        renderMegaMenu(categories);
    } catch (error) {
        console.error('Error loading categories:', error);
    }
};

const renderMegaMenu = (categories) => {
    const menuHtml = categories.map(category => `
        <div class="mega-menu-column">
            <h3>${category.name}</h3>
            ${category.subcategories ? `
                <ul>
                    ${category.subcategories.map(sub => `
                        <li><a href="/category/${sub.slug}">${sub.name}</a></li>
                    `).join('')}
                </ul>
            ` : ''}
        </div>
    `).join('');
    
    megaMenu.innerHTML = menuHtml;
};

// Hero Slider
const initSlider = async () => {
    try {
        const slides = await api.get('/slides');
        renderSlider(slides);
        startSlideShow();
    } catch (error) {
        console.error('Error initializing slider:', error);
    }
};

const renderSlider = (slides) => {
    const sliderHtml = slides.map((slide, index) => `
        <div class="slide ${index === 0 ? 'active' : ''}" style="background-image: url(${slide.image})">
            <div class="slide-content">
                <h2>${slide.title}</h2>
                <p>${slide.description}</p>
                <a href="${slide.link}" class="btn btn-primary">${slide.buttonText}</a>
            </div>
        </div>
    `).join('');
    
    document.querySelector('.hero-slider').innerHTML = sliderHtml;
};

const startSlideShow = () => {
    slideInterval = setInterval(nextSlide, 5000);
};

const nextSlide = () => {
    const slides = document.querySelectorAll('.slide');
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
};

// Featured Products
const loadFeaturedProducts = async () => {
    try {
        const products = await api.get('/products?featured=true');
        renderProducts(products, '.featured-products .product-grid');
    } catch (error) {
        console.error('Error loading featured products:', error);
    }
};

const renderProducts = (products, container) => {
    const productsHtml = products.map(product => `
        <div class="product-card">
            <img src="${product.images[0]}" alt="${product.name}">
            <div class="product-info">
                <h3 class="product-title">${product.name}</h3>
                <div class="product-price">
                    ${product.discount > 0 ? `
                        <span class="original-price">${formatPrice(product.price)}</span>
                        <span class="discounted-price">${formatPrice(product.price * (1 - product.discount/100))}</span>
                    ` : `
                        <span>${formatPrice(product.price)}</span>
                    `}
                </div>
                <div class="product-actions">
                    <button class="btn btn-primary add-to-cart" data-id="${product._id}">
                        Add to Cart
                    </button>
                    <button class="btn btn-outline add-to-wishlist" data-id="${product._id}">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    document.querySelector(container).innerHTML = productsHtml;
};

// Event Listeners
categoriesToggle.addEventListener('mouseover', () => {
    isMenuOpen = true;
    megaMenu.style.display = 'flex';
});

categoriesToggle.addEventListener('mouseout', () => {
    isMenuOpen = false;
    setTimeout(() => {
        if (!isMenuOpen) {
            megaMenu.style.display = 'none';
        }
    }, 200);
});

megaMenu.addEventListener('mouseover', () => {
    isMenuOpen = true;
});

megaMenu.addEventListener('mouseout', () => {
    isMenuOpen = false;
    setTimeout(() => {
        if (!isMenuOpen) {
            megaMenu.style.display = 'none';
        }
    }, 200);
});

document.addEventListener('click', (e) => {
    if (e.target.classList.contains('add-to-cart')) {
        const productId = e.target.dataset.id;
        addToCart(productId);
    } else if (e.target.classList.contains('add-to-wishlist')) {
        const productId = e.target.dataset.id;
        addToWishlist(productId);
    }
});

modalCloseButtons.forEach(button => {
    button.addEventListener('click', () => {
        const modal = button.closest('.modal');
        modal.style.display = 'none';
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    initSlider();
    loadFeaturedProducts();

    // Get DOM elements
    const loginButton = document.querySelector('a[href="/dang-nhap"]');
    const loginModal = document.getElementById('loginModal');
    const closeButton = loginModal.querySelector('.modal-close');

    // Show modal when login button is clicked
    loginButton.addEventListener('click', (e) => {
        e.preventDefault();
        loginModal.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    });

    // Close modal when X button is clicked
    closeButton.addEventListener('click', () => {
        loginModal.classList.remove('show');
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    });
});

// Banner Slider
document.addEventListener('DOMContentLoaded', function() {
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.slider-nav.prev');
    const nextBtn = document.querySelector('.slider-nav.next');
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    let isTransitioning = false;
    let autoSlideInterval;
    
    // Initialize slider
    function initSlider() {
        updateSliderPosition();
        startAutoSlide();
    }
    
    // Update slider position
    function updateSliderPosition(transition = true) {
        if (transition) {
            sliderWrapper.style.transition = 'transform 0.5s ease-in-out';
        } else {
            sliderWrapper.style.transition = 'none';
        }
        
        sliderWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
        updateDots();
    }
    
    // Update dots
    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
    
    // Next slide
    function nextSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSliderPosition();
        
        setTimeout(() => {
            isTransitioning = false;
        }, 500);
    }
    
    // Previous slide
    function prevSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSliderPosition();
        
        setTimeout(() => {
            isTransitioning = false;
        }, 500);
    }
    
    // Start auto slide
    function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(nextSlide, 5000);
    }
    
    // Stop auto slide
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }
    
    // Event Listeners
    prevBtn.addEventListener('click', () => {
        prevSlide();
        startAutoSlide(); // Reset timer
    });
    
    nextBtn.addEventListener('click', () => {
        nextSlide();
        startAutoSlide(); // Reset timer
    });
    
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            if (isTransitioning || currentSlide === index) return;
            
            isTransitioning = true;
            currentSlide = index;
            updateSliderPosition();
            startAutoSlide(); // Reset timer
            
            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        });
    });
    
    // Touch events for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    sliderWrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        stopAutoSlide();
    });
    
    sliderWrapper.addEventListener('touchmove', (e) => {
        if (isTransitioning) return;
        
        const currentX = e.touches[0].clientX;
        const diff = touchStartX - currentX;
        const offset = -currentSlide * 100 - (diff / sliderWrapper.offsetWidth) * 100;
        
        sliderWrapper.style.transition = 'none';
        sliderWrapper.style.transform = `translateX(${offset}%)`;
    });
    
    sliderWrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        
        const diff = touchStartX - touchEndX;
        const threshold = sliderWrapper.offsetWidth * 0.2;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSliderPosition();
        }
        
        startAutoSlide();
    });
    
    // Pause auto slide on hover
    sliderWrapper.addEventListener('mouseenter', stopAutoSlide);
    sliderWrapper.addEventListener('mouseleave', startAutoSlide);
    
    // Initialize
    initSlider();
});

// Search Categories Dropdown
document.addEventListener('DOMContentLoaded', function() {
    const searchCategories = document.querySelector('.search-categories');
    const categoryDropdown = document.querySelector('.category-dropdown');
    const categoryItems = document.querySelectorAll('.category-item');
    
    // Show submenu on hover
    categoryItems.forEach(item => {
        let submenu = null;
        
        item.addEventListener('mouseenter', () => {
            // Remove any existing submenus
            const existingSubmenu = document.querySelector('.category-submenu');
            if (existingSubmenu) {
                existingSubmenu.remove();
            }
            
            // Create submenu
            submenu = document.createElement('div');
            submenu.className = 'category-submenu';
            
            // Get category type from the span text
            const categoryType = item.querySelector('span').textContent;
            
            // Add submenu content based on category
            switch(categoryType) {
                case 'Điện thoại, Tablet':
                    submenu.innerHTML = `
                        <div class="submenu-section">
                            <h4>Hãng điện thoại</h4>
                            <ul>
                                <li><a href="#">iPhone</a></li>
                                <li><a href="#">Samsung</a></li>
                                <li><a href="#">Xiaomi</a></li>
                                <li><a href="#">OPPO</a></li>
                                <li><a href="#">realme</a></li>
                                <li><a href="#">TECNO</a></li>
                            </ul>
                        </div>
                        <div class="submenu-section">
                            <h4>Mức giá điện thoại</h4>
                            <ul>
                                <li><a href="#">Dưới 2 triệu</a></li>
                                <li><a href="#">Từ 2 - 4 triệu</a></li>
                                <li><a href="#">Từ 4 - 7 triệu</a></li>
                                <li><a href="#">Từ 7 - 13 triệu</a></li>
                                <li><a href="#">Từ 13 - 20 triệu</a></li>
                                <li><a href="#">Trên 20 triệu</a></li>
                            </ul>
                        </div>
                        <div class="submenu-section">
                            <h4>Điện thoại HOT</h4>
                            <ul>
                                <li><a href="#">iPhone 16 Series</a></li>
                                <li><a href="#">iPhone 15 Pro Max</a></li>
                                <li><a href="#">Galaxy S25 Ultra</a></li>
                                <li><a href="#">OPPO Find N5</a></li>
                            </ul>
                        </div>
                    `;
                    break;
                    
                case 'Laptop':
                    submenu.innerHTML = `
                        <div class="submenu-section">
                            <h4>Hãng laptop</h4>
                            <ul>
                                <li><a href="#">MacBook</a></li>
                                <li><a href="#">ASUS</a></li>
                                <li><a href="#">HP</a></li>
                                <li><a href="#">Dell</a></li>
                                <li><a href="#">Lenovo</a></li>
                                <li><a href="#">Acer</a></li>
                            </ul>
                        </div>
                        <div class="submenu-section">
                            <h4>Phân loại</h4>
                            <ul>
                                <li><a href="#">Laptop Gaming</a></li>
                                <li><a href="#">Học tập, văn phòng</a></li>
                                <li><a href="#">Đồ họa, kỹ thuật</a></li>
                                <li><a href="#">Mỏng nhẹ</a></li>
                                <li><a href="#">Cao cấp, sang trọng</a></li>
                            </ul>
                        </div>
                    `;
                    break;
                    
                // Add more cases for other categories...
            }
            
            if (submenu.innerHTML !== '') {
                // Position the submenu
                submenu.style.position = 'absolute';
                submenu.style.left = '100%';
                submenu.style.top = '0';
                
                // Add submenu to item
                item.appendChild(submenu);
            }
        });
    });
    
    // Handle search input
    const searchInput = document.querySelector('.search-input-wrapper input');
    const searchButton = document.querySelector('.search-input-wrapper button');
    
    searchInput.addEventListener('focus', () => {
        searchInput.parentElement.parentElement.classList.add('focused');
    });
    
    searchInput.addEventListener('blur', () => {
        searchInput.parentElement.parentElement.classList.remove('focused');
    });
    
    // Add search suggestions
    searchInput.addEventListener('input', debounce(function(e) {
        const query = e.target.value.trim();
        if (query.length > 0) {
            showSearchSuggestions(query);
        } else {
            hideSearchSuggestions();
        }
    }, 300));
});

// Debounce function to limit API calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Show search suggestions
function showSearchSuggestions(query) {
    const suggestionsBox = document.querySelector('.search-suggestions') || document.createElement('div');
    suggestionsBox.className = 'search-suggestions';
    
    // Example suggestions (replace with actual API call)
    const suggestions = [
        `iPhone 16 Pro Max liên quan đến "${query}"`,
        `Samsung Galaxy S25 Ultra liên quan đến "${query}"`,
        `Xiaomi 14T Pro liên quan đến "${query}"`,
        `OPPO Find N5 liên quan đến "${query}"`
    ];
    
    suggestionsBox.innerHTML = suggestions.map(suggestion => `
        <div class="suggestion-item">
            <i class="fas fa-search"></i>
            <span>${suggestion}</span>
        </div>
    `).join('');
    
    const searchInput = document.querySelector('.search-input-wrapper');
    if (!document.querySelector('.search-suggestions')) {
        searchInput.appendChild(suggestionsBox);
    }
}

// Hide search suggestions
function hideSearchSuggestions() {
    const suggestionsBox = document.querySelector('.search-suggestions');
    if (suggestionsBox) {
        suggestionsBox.remove();
    }
}

// Close suggestions when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-input-wrapper')) {
        hideSearchSuggestions();
    }
}); 