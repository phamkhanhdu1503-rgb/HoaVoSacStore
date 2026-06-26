<?php
require '../config/auth.php';
require '../config/database.php';
session_start();

$user_id = 1; // ⚠ tạm thời (sau này lấy từ login)
$product_id = (int)($_GET['id'] ?? 0);

if ($product_id <= 0) {
    // Nếu gọi bằng AJAX thì trả về lỗi JSON, ngược lại thì die như cũ
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        exit;
    } else {
        die("ID không hợp lệ");
    }
}

// kiểm tra đã tồn tại chưa
$check = $db->prepare("SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {
    // đã có → tăng số lượng
    $newQty = $row['quantity'] + 1;

    $update = $db->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $newQty, $row['id']);
    $update->execute();

} else {
    // chưa có → insert mới
    $insert = $db->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
}

// ===================================================
// XỬ LÝ PHẢN HỒI THÔNG MINH (AJAX HOẶC TRUY CẬP THẲNG)
// ===================================================
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Nếu nút "Thêm nhanh" gọi ngầm bằng Javascript, trả về JSON thành công
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
} else {
    // Nếu bấm nút "Mua ngay" hoặc link thường, nhảy về trang giỏ hàng như cũ
    header("Location: cart.php");
    exit;
}