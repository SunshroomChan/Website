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

// Chọn cơ sở dữ liệu
$conn->select_db($dbname);

// Tạo bảng khachhang nếu chưa tồn tại
$sql2 = "CREATE TABLE IF NOT EXISTS khachhang (
    id_kh INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sdt VARCHAR(20) NULL,
    password VARCHAR(255) NOT NULL,
    ngaysinh DATE NOT NULL,
    gioitinh VARCHAR(255) NOT NULL,
    anhavt LONGBLOB NOT NULL,
    CREATE_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UPDATE_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql2) !== TRUE) {
    die("Lỗi khi tạo bảng khachhang: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $user = $_POST['username'];
        $email = $_POST['email'];
        $pass = $_POST['password'];

        // Kiểm tra xem username hoặc email đã tồn tại chưa
        $sql_check = "SELECT * FROM khachhang WHERE username = ? OR email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $user, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if (empty($user) || empty($email) || empty($pass)) {
            echo "Vui lòng điền đầy đủ thông tin.";
        } else if ($result_check->num_rows > 0) {
            echo "Tên đăng nhập hoặc email đã tồn tại, vui lòng chọn tên khác.";
        } else {
            // Mã hóa mật khẩu
            $password_hashed = password_hash($pass, PASSWORD_DEFAULT);

            // Chuẩn bị truy vấn SQL để thêm người dùng mới
            $sql = "INSERT INTO khachhang (username, email, password) VALUES (?, ?, ?)";
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
    } else {
        echo "Vui lòng điền đầy đủ thông tin.";
    }
}
$conn->close();
?>