// Chức năng chọn tỉnh thành cho header
// Khi nhấn vào .location-select sẽ hiện popup chọn tỉnh thành
// Khi chọn tỉnh, tên tỉnh sẽ đổi ở header và popup tự đóng

document.addEventListener('DOMContentLoaded', function() {
    const locationBtn = document.querySelector('.location-select'); // Lấy nút chọn tỉnh thành
    const locationModal = document.getElementById('locationModal'); // Lấy popup tỉnh thành
    const closeBtn = document.querySelector('.location-close'); // Lấy nút đóng popup
    const locationItems = document.querySelectorAll('.location-item'); // Lấy tất cả các tỉnh
    const locationText = document.querySelector('.location-select span'); // Lấy phần hiển thị tên tỉnh ở header

    // Khi nhấn vào nút chọn tỉnh thành thì hiện popup
    locationBtn.addEventListener('click', function() {
        locationModal.classList.add('show');
    });
    // Khi nhấn nút đóng thì ẩn popup
    closeBtn.addEventListener('click', function() {
        locationModal.classList.remove('show');
    });
    // Khi click ra ngoài popup thì ẩn popup
    locationModal.addEventListener('click', function(e) {
        if (e.target === locationModal) locationModal.classList.remove('show');
    });
    // Khi chọn một tỉnh thành
    locationItems.forEach(function(item) {
        item.addEventListener('click', function() {
            // Bỏ chọn tất cả
            locationItems.forEach(i => i.classList.remove('selected'));
            // Chọn tỉnh mới
            this.classList.add('selected');
            // Đổi tên tỉnh ở header
            locationText.textContent = this.textContent;
            // Đóng popup
            locationModal.classList.remove('show');
        });
    });
}); 