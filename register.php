<?php
// Kết nối MySQL
$servername = "localhost";
$username = "hoang";
$password = "Hoangviphb1234.";
$dbname = "testdb";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Chọn cơ sở dữ liệu
$conn->select_db($dbname);

// Tạo bảng phanquyen nếu chưa tồn tại
$sql1 = "CREATE TABLE IF NOT EXISTS phanquyen (
    id_role INT(11) AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(255) NOT NULL
)";
if ($conn->query($sql1) !== TRUE) {
    die("Lỗi khi tạo bảng phanquyen: " . $conn->error);
}

// Thêm các vai trò nếu chưa tồn tại
$roles = ["Admin", "NhanVien", "KhachHang"];
foreach ($roles as $role) {
    $sql_role = "INSERT INTO phanquyen (role_name) SELECT * FROM (SELECT '$role') AS tmp WHERE NOT EXISTS (SELECT role_name FROM phanquyen WHERE role_name = '$role') LIMIT 1";
    if ($conn->query($sql_role) !== TRUE) {
        die("Lỗi khi thêm vai trò: " . $conn->error);
    }
}

// Tạo bảng khachhang nếu chưa tồn tại
$sql2 = "CREATE TABLE IF NOT EXISTS khachhang (
    id_kh INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sdt VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,
    ngaysinh DATE NOT NULL,
    gioitinh VARCHAR(255) NOT NULL,
    anhavt LONGBLOB NOT NULL,
    id_role INT NOT NULL,
    FOREIGN KEY (id_role) REFERENCES phanquyen(id_role),
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

            // Phân quyền cho người dùng
            // Note: 1 - Admin, 2 - NhanVien, 3 - KhachHang
            $role = 3;

            // Chuẩn bị truy vấn SQL để thêm người dùng mới
            $sql = "INSERT INTO khachhang (username, email, password, id_role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $user, $email, $password_hashed, $role);

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