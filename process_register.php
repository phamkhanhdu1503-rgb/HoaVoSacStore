<?php
session_start();

/* Kết nối MySQL */
$conn = new mysqli("localhost", "root", "", "HoaVoSacStore");

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

/* Lấy dữ liệu từ form */
$fullname = trim($_POST['fullname']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

/* Kiểm tra mật khẩu */
if ($password !== $confirm_password) {
    echo "<script>
            alert('Mật khẩu xác nhận không khớp!');
            window.history.back();
          </script>";
    exit();
}

/* Kiểm tra username hoặc email đã tồn tại chưa */
$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>
            alert('Tên đăng nhập hoặc Email đã tồn tại!');
            window.history.back();
          </script>";
    exit();
}

/* Mã hóa mật khẩu */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* Thêm tài khoản */
$sql = "INSERT INTO users(fullname, username, email, phone, password)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssss",
    $fullname,
    $username,
    $email,
    $phone,
    $hashed_password
);

if ($stmt->execute()) {

    echo "<script>
            alert('Đăng ký thành công!');
            window.location='login.php';
          </script>";

} else {

    echo "<script>
            alert('Đăng ký thất bại!');
            window.history.back();
          </script>";

}

$stmt->close();
$conn->close();
?>