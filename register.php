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
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Kiểm tra xem username hoặc email đã tồn tại chưa
    $sql_check = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $user, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "Tên đăng nhập hoặc email đã tồn tại, vui lòng chọn tên khác.";
    } else {
        // Mã hóa mật khẩu
        $password_hashed = password_hash($pass, PASSWORD_DEFAULT);

        // Chuẩn bị truy vấn SQL để thêm người dùng mới
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $user, $email, $password_hashed);

        // Thực thi truy vấn và kiểm tra kết quả
        if ($stmt->execute()) {
            echo "Đăng ký thành công!";
        } else {
            echo "Đăng ký thất bại: " . $stmt->error;
        }

        $stmt->close();
    }

    $stmt_check->close();
}
$conn->close();
?>
