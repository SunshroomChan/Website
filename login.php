<?php
// Kết nối MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userdb";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_or_email = $_POST['username'];
    $pass = $_POST['password'];

    // Chuẩn bị truy vấn SQL để kiểm tra username hoặc email
    $sql = "SELECT * FROM khachhang WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_or_email, $user_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Kiểm tra nếu người dùng đăng nhập bằng username hoặc email?
    if(filter_var($input, FILTER_VALIDATE_EMAIL)){
        // Cho xử lý dưới dạng email
        $query = "SELECT * FROM khachhang WHERE email = ?";
    } else {
        // Cho xử lý dưới dạng username
        $query = "SELECT * FROM khachhang WHERE username = ?";
    }

    // Kiểm tra nếu người dùng tồn tại
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Kiểm tra mật khẩu
        if (password_verify($pass, $row['password'])) {
            echo "Đăng nhập thành công!";
        } else {
            echo "Sai tên người dùng hoặc mật khẩu.";
        }
    } else {
        echo "Sai tên người dùng hoặc mật khẩu.";
    }

    $stmt->close();
}
$conn->close();
?>