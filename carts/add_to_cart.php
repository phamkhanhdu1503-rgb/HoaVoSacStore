<?php
session_start();
require '../config/database.php';

// CHẶN nếu chưa login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)($_GET['id'] ?? 0);

if ($product_id <= 0) {
    die("ID không hợp lệ");
}

// kiểm tra tồn tại
$check = $db->prepare("SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {

    // tăng số lượng
    $update = $db->prepare("UPDATE carts SET quantity = quantity + 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();

} else {

    // thêm mới
    $insert = $db->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
}

// trả về
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

header("Location: cart.php");
exit;