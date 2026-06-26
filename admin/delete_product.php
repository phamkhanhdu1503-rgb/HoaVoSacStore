<?php
require '../config/admin_auth.php';
// Kết nối cơ sở dữ liệu
require '../config/database.php';

// Kiểm tra xem ID sản phẩm có được truyền qua URL hay không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID sản phẩm!");
}

// Lấy ID sản phẩm từ URL và chuyển đổi sang kiểu số nguyên
$id = (int) $_GET['id'];

// Lấy thông tin sản phẩm từ cơ sở dữ liệu để xác định tên file hình ảnh (nếu có)
$stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra xem sản phẩm có tồn tại hay không
if ($result->num_rows === 0) {
    die("Sản phẩm không tồn tại!");
}

// Lấy tên file hình ảnh của sản phẩm 
$product = $result->fetch_assoc();
$imageName = $product['image'];

// Chuẩn bị câu lệnh SQL để xóa sản phẩm dựa trên ID
$stmt = $db->prepare("DELETE FROM products WHERE id = ?");

// Gắn tham số và thực thi câu lệnh SQL
$stmt->bind_param("i", $id);
$stmt->execute();

// Nếu sản phẩm có hình ảnh, xóa file hình ảnh khỏi thư mục uploads
if (!empty($imageName)) {
    $filePath = "../uploads/" . $imageName;

    // Kiểm tra nếu file tồn tại trước khi xóa
    if (file_exists($filePath)) {
        unlink($filePath); // Xóa file ảnh khỏi ổ đĩa
    }
}

// Chuyển hướng người dùng về trang danh sách sản phẩm sau khi xóa thành công
header("Location: products.php");
exit;
?>