<?php
// Kết nối cơ sở dữ liệu
require '../config/database.php';

// Kiểm tra xem ID danh mục có được truyền qua URL hay không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID danh mục!");
}

// Lấy ID danh mục từ URL và chuyển đổi sang kiểu số nguyên
$id = (int) $_GET['id'];

// Chuẩn bị câu lệnh SQL để xóa danh mục dựa trên ID
$stmt = $db->prepare("
    DELETE FROM categories
    WHERE id = ?
");

// Gắn tham số và thực thi câu lệnh SQL
$stmt->bind_param("i", $id);
$stmt->execute();

// Chuyển hướng người dùng về trang danh sách danh mục sau khi xóa thành công
header("Location: category.php");
exit;