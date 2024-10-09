<?php
// Kết nối MySQL
$servername = "localhost";
$username = "hoang";
$password = "Hoangviphb1234.";
$dbname = "testdb";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $user_or_email = $_POST['username'];
        $pass = $_POST['password'];

        // Chuẩn bị truy vấn SQL để kiểm tra username hoặc email
        $sql = "SELECT * FROM khachhang WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_or_email, $user_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu người dùng tồn tại
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Kiểm tra mật khẩu
            if (password_verify($pass, $row['password'])) {
                // Đăng nhập thành công, thiết lập session
                session_start();
                $_SESSION['user_id'] = $row['id_kh'];
                echo "Đăng nhập thành công!";
            } else {
                echo "Sai tên người dùng hoặc mật khẩu.";
            }
        } else {
            echo "Sai tên người dùng hoặc mật khẩu.";
        }

        $stmt->close();
    } else {
        echo "Vui lòng điền đầy đủ thông tin.";
    }
}
$conn->close();
?>