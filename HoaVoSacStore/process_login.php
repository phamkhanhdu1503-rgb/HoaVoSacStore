<?php
session_start();

// Kết nối database
$conn = new mysqli("localhost", "root", "", "HoaVoSacStore");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Kiểm tra dữ liệu rỗng
if (empty($username) || empty($password)) {
    die("Vui lòng nhập đầy đủ thông tin.");
}

// Tìm tài khoản
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    // So sánh mật khẩu đã mã hóa
    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['username'] = $user['username'];

        header("Location: index.php");
        exit();

    } else {

        header("Location: login.php?error=Sai mật khẩu");
        exit();

    }

} else {

    header("Location: login.php?error=Không tồn tại tài khoản");
    exit();

}

$stmt->close();
$conn->close();
?>