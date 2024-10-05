<?php
// Kết nối MySQL mà không chỉ định cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userdb";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem cơ sở dữ liệu có tồn tại hay không
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Cơ sở dữ liệu đã được tạo thành công hoặc đã tồn tại.";
} else {
    die("Lỗi khi tạo cơ sở dữ liệu: " . $conn->error);
}

// Kết nối lại với cơ sở dữ liệu vừa tạo
$conn->select_db($dbname);

// Tạo bảng nếu chưa tồn tại
$sql = "CREATE TABLE IF NOT EXISTS khachhang (
    id_kh INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sdt VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,
    ngaysinh DATE NULL,
    gioitinh VARCHAR(255) NULL,
    anhavt LONGBLOB NULL,
    id_role INT NOT NULL,
    FOREIGN KEY (id_role) REFERENCES phanquyen(id_role),
    CREATE_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UPDATE_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

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

        if ($result_check->num_rows > 0) {
            echo "Tên đăng nhập hoặc email đã tồn tại, vui lòng chọn tên khác.";
        } else {
            // Mã hóa mật khẩu
            $password_hashed = password_hash($pass, PASSWORD_DEFAULT);

            // Phân quyền cho người dùng
            // Note: 1 - Admin, 2 - NhanVien, 3 - KhachHang
            $role = 3;

            // Chuẩn bị truy vấn SQL để thêm người dùng mới
            $sql = "INSERT INTO khachhang (username, email, password, id_role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $user, $email, $password_hashed, $role);

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