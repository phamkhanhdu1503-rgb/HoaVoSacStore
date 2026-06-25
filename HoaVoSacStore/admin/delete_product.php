<?php
require '../config/database.php';

// ==========================
// 1. CHECK ID SẢN PHẨM
// ==========================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID sản phẩm!");
}

$id = (int) $_GET['id'];

// ==========================
// 2. LẤY THÔNG TIN ẢNH ĐỂ XÓA FILE VẬT LÝ
// ==========================
$stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Sản phẩm không tồn tại!");
}

$product = $result->fetch_assoc();
$imageName = $product['image'];

// ==========================
// 3. TIẾN HÀNH XÓA SẢN PHẨM TRONG DATABASE
// ==========================
$stmt = $db->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// ==========================
// 4. XÓA FILE ẢNH TRONG THƯ MỤC UPLOADS (NẾU CÓ)
// ==========================
if (!empty($imageName)) {
    $filePath = "../uploads/" . $imageName;
    if (file_exists($filePath)) {
        unlink($filePath); // Xóa file ảnh khỏi ổ đĩa
    }
}

// ==========================
// 5. QUAY TRỞ LẠI TRANG DANH SÁCH
// ==========================
header("Location: products.php");
exit;
?>