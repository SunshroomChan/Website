

// Mã này tạo ra một slider ảnh đơn giản với hai nút để điều khiển chuyển động qua lại giữa các ảnh. Chỉ số của ảnh hiện tại được theo dõi và cập nhật dựa trên các nút nhấn, đồng thời sử dụng CSS transform để tạo hiệu ứng di chuyển mượt mà.
const leftImages = document.querySelectorAll('.slider_container-item-left img');
const rightImages = document.querySelectorAll('.slider_container-item-right-slider');
const prevButton = document.querySelector('.fa-chevron-left');
const nextButton = document.querySelector('.fa-chevron-right');

let currentIndex = 0;

// Hàm hiển thị slide dựa trên currentIndex
function showSlide(index) {
    leftImages.forEach((img, i) => {
        img.style.transform = `translateX(${(i - index) * 100}%)`;
    });

    rightImages.forEach((img, i) => {
        img.style.transform = `translateX(${(i - index) * 100}%)`;
    });
}

// Xử lý sự kiện khi nhấn nút Next
nextButton.addEventListener('click', function() {
    currentIndex = (currentIndex + 1) % leftImages.length;
    showSlide(currentIndex);
});

// Xử lý sự kiện khi nhấn nút Prev
prevButton.addEventListener('click', function() {
    currentIndex = (currentIndex - 1 + leftImages.length) % leftImages.length;
    showSlide(currentIndex);
});

// Khởi tạo slider với slide đầu tiên
showSlide(currentIndex);

// history search------------------------------------------------------------------------------
// Lưu từ khóa tìm kiếm vào localStorage và hiển thị lịch sử
function saveSearch() {
    const searchInput = document.querySelector('.header_search-input').value.trim();
    
    if (searchInput) {
        // Lấy danh sách lịch sử tìm kiếm từ localStorage
        let searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || [];

        // Kiểm tra nếu từ khóa đã tồn tại trong lịch sử thì không thêm lại
        if (!searchHistory.includes(searchInput)) {
            searchHistory.unshift(searchInput); // Thêm vào đầu danh sách
        }

        // Lưu lại lịch sử tìm kiếm vào localStorage
        localStorage.setItem('searchHistory', JSON.stringify(searchHistory));

        // Cập nhật danh sách lịch sử tìm kiếm
        displaySearchHistory();
    }
}

// Hiển thị lịch sử tìm kiếm
function displaySearchHistory() {
    const searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || [];
    const historyList = document.querySelector('.search-history-list');
    historyList.innerHTML = '';

    // Thêm các từ khóa lịch sử vào danh sách
    searchHistory.forEach(item => {
        const listItem = document.createElement('li');
        listItem.classList.add('search-history-list-item');
        listItem.innerHTML = `<a href="#">${item}</a>`;
        historyList.appendChild(listItem);
    });
}

function validatePassword() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var errorMessage = document.getElementById("error_message");

    if (password !== confirmPassword) {
        errorMessage.textContent = "Mật khẩu nhập lại không khớp.";
        return false; // Prevent form submission
    } else {
        errorMessage.textContent = "";
        return true; // Allow form submission
    }
}

// Thêm sản phẩm vào giỏ hàng
function addToCart(productId) {
    fetch('/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ productId: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sản phẩm đã được thêm vào giỏ hàng.');
        } else {
            alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Xóa sản phẩm khỏi giỏ hàng
function removeFromCart(productId) {
    fetch('/remove-from-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ productId: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sản phẩm đã được xóa khỏi giỏ hàng.');
        } else {
            alert('Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Hiển thị giỏ hàng
function displayCart() {
    fetch('/display-cart.php')
    .then(response => response.json())
    .then(data => {
        const cartList = document.querySelector('.cart-list');
        cartList.innerHTML = '';

        data.cartItems.forEach(item => {
            const listItem = document.createElement('li');
            listItem.classList.add('cart-list-item');
            listItem.innerHTML = `
                <span>${item.productName}</span>
                <span>${item.quantity}</span>
                <button onclick="removeFromCart(${item.productId})">Xóa</button>
            `;
            cartList.appendChild(listItem);
        });
    })
    .catch(error => console.error('Error:', error));
}

// Gọi hàm displayCart() khi tải trang
document.addEventListener('DOMContentLoaded', displayCart);

// Xóa toàn bộ lịch sử tìm kiếm
function clearSearchHistory() {
    localStorage.removeItem('searchHistory');
    displaySearchHistory();
}

// Gọi hàm displaySearchHistory() khi tải trang
document.addEventListener('DOMContentLoaded', displaySearchHistory);

// Thêm sự kiện click cho nút tìm kiếm
document.querySelector('.header_search-btn').addEventListener('click', saveSearch);
