<?php
session_start();

require 'config/database.php';

// Chỉ cho phép gửi bằng POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Lấy dữ liệu
$fullname = trim($_POST['txtHoTen'] ?? '');
$phone = trim($_POST['txtDienThoai'] ?? '');

// Kiểm tra dữ liệu
if ($fullname == '' || $phone == '') {
    die("Vui lòng nhập đầy đủ thông tin.");
}

if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    die("Số điện thoại không hợp lệ.");
}

// Lưu database
$stmt = $db->prepare("
    INSERT INTO consult_requests(fullname, phone)
    VALUES (?, ?)
");

$stmt->bind_param("ss", $fullname, $phone);

if ($stmt->execute()) {

    $stmt->close();
    $db->close();

    header("Location: consult_success.php");
    exit;

} else {

    echo "Có lỗi xảy ra khi lưu dữ liệu!";
}
?>