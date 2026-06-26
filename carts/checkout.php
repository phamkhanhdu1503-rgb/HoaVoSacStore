<?php
require '../config/auth.php';
require '../config/database.php';
require '../config/flash.php';

$user_id = $_SESSION['user_id'];

// ============================
// LẤY GIỎ HÀNG
// ============================
$sql = "
SELECT
    c.product_id,
    c.quantity,
    p.price,
    p.stock
FROM carts c
JOIN products p
ON c.product_id = p.id
WHERE c.user_id = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    setFlash(
        "warning",
        "Giỏ hàng của bạn đang trống!"
    );

    header("Location: ../cart.php");
    exit;
}

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {

    // Kiểm tra tồn kho trước
    if ($row['quantity'] > $row['stock']) {

        setFlash(
            "warning",
            "Một hoặc nhiều sản phẩm trong giỏ hàng không đủ số lượng."
        );

        header("Location: ../cart.php");
        exit;
    }

    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

// ============================
// BẮT ĐẦU TRANSACTION
// ============================
$db->begin_transaction();

try {

    // ============================
    // TẠO ORDER
    // ============================
    $insertOrder = $db->prepare("
        INSERT INTO orders (user_id, total, status)
        VALUES (?, ?, 'Chờ xác nhận')
    ");

    $insertOrder->bind_param(
        "id",
        $user_id,
        $total
    );

    $insertOrder->execute();

    $order_id = $db->insert_id;

    // ============================
    // ORDER DETAILS
    // ============================
    foreach ($items as $item) {

        // Thêm chi tiết đơn hàng
        $insertDetail = $db->prepare("
            INSERT INTO order_details
            (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        $insertDetail->bind_param(
            "iiid",
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        );

        $insertDetail->execute();

        // Cập nhật số lượng bán
        $updateSold = $db->prepare("
            UPDATE products
            SET sold = sold + ?
            WHERE id = ?
        ");

        $updateSold->bind_param(
            "ii",
            $item['quantity'],
            $item['product_id']
        );

        $updateSold->execute();

        // Trừ tồn kho
        $updateStock = $db->prepare("
            UPDATE products
            SET stock = stock - ?
            WHERE id = ?
        ");

        $updateStock->bind_param(
            "ii",
            $item['quantity'],
            $item['product_id']
        );

        $updateStock->execute();
    }

    // ============================
    // XÓA GIỎ HÀNG
    // ============================
    $deleteCart = $db->prepare("
        DELETE FROM carts
        WHERE user_id = ?
    ");

    $deleteCart->bind_param(
        "i",
        $user_id
    );

    $deleteCart->execute();

    // ============================
    // LƯU DATABASE
    // ============================
    $db->commit();

    header("Location: ../success.php?order_id=" . $order_id);
    exit;

} catch (Exception $e) {

    $db->rollback();

    setFlash(
        "danger",
        "Đặt hàng thất bại! Vui lòng thử lại."
    );

    header("Location: ../cart.php");
    exit;
}