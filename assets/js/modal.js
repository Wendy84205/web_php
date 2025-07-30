document.addEventListener("DOMContentLoaded", function () {
  const loginModal = document.getElementById("loginModal");
  const cartButton = document.getElementById("cart-button");
  const loginButton = document.getElementById("login-button");
  const closeButton = loginModal?.querySelector(".modal-close");
  const registerBtn = loginModal?.querySelector(".btn-register");
  const loginBtn = loginModal?.querySelector(".btn-login");

  // ✅ Hàm mở modal
  const openModal = () => {
    loginModal.style.display = "block";
    document.body.style.overflow = "hidden";
  };

  // 👉 Click vào giỏ hàng sẽ mở modal nếu chưa đăng nhập
   cartButton.addEventListener("click", function (e) {
    if (!isLoggedIn) {
      e.preventDefault();
      openModal();
    } else {
      window.location.href = 'cart.php';
    }
  });

  // 👉 Click vào login cũng mở modal
  loginButton?.addEventListener("click", function (e) {
    e.preventDefault();
    openModal();
  });

  // 👉 Đóng modal
  closeButton?.addEventListener("click", () => {
    loginModal.style.display = "none";
    document.body.style.overflow = "auto";
  });

  // 👉 Đóng khi click ngoài vùng modal
  window.addEventListener("click", function (e) {
    if (e.target === loginModal) {
      loginModal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });

  // 👉 Đóng khi nhấn ESC
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && loginModal.style.display === "block") {
      loginModal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });

  // 👉 Chuyển đến login / register
  loginBtn?.addEventListener("click", () => window.location.href = "login.php");
  registerBtn?.addEventListener("click", () => window.location.href = "register.php");
});
document.addEventListener("DOMContentLoaded", function () {
  const cartButton = document.getElementById("cart-button");
  const isLoggedIn = cartButton.getAttribute("data-logged-in") === "true";

  // 👉 Click vào giỏ hàng sẽ mở modal nếu chưa đăng nhập
  cartButton.addEventListener("click", function (e) {
    if (!isLoggedIn) {
      e.preventDefault();
      openModal();
    } else {
      window.location.href = 'cart.php';
    }
  });
});