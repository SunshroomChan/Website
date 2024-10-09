<?php
session_start();
$is_logged_in = false;

if (isset($_SESSION['user_id'])) {
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

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id_kh FROM khachhang WHERE id_kh = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $is_logged_in = true;
    }

    $stmt->close();
    $conn->close();
}
?>