<?php
require '../config/database.php';
session_start();

// ============================
// CHECK LOGIN
// ============================
if (!isset($_SESSION['user_id'])) {
    die("Vui lòng đăng nhập để đặt hàng!");
}

$user_id = $_SESSION['user_id'];

// ============================
// 1. LẤY GIỎ HÀNG (CÓ STOCK)
// ============================
$sql = "
SELECT c.product_id, c.quantity, p.price, p.stock
FROM carts c
JOIN products p ON c.product_id = p.id
WHERE c.user_id = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Giỏ hàng trống!");
}

// ============================
// 2. TÍNH TOTAL + LƯU ITEMS
// ============================
$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {

    $subtotal = $row['price'] * $row['quantity'];

    $total += $subtotal;

    $items[] = $row;
}

// ============================
// 3. TẠO ORDER
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
// 4. ORDER DETAILS + SOLD + STOCK
// ============================
foreach ($items as $item) {

    // CHECK STOCK
    if ($item['quantity'] > $item['stock']) {

        die(
            "Sản phẩm ID "
            . $item['product_id']
            . " không đủ hàng!"
        );
    }

    // INSERT ORDER DETAIL
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

    // UPDATE SOLD
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

    // UPDATE STOCK
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
// 5. XOÁ CART
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
// 6. REDIRECT
// ============================
header(
    "Location: ../success.php?order_id=$order_id"
);

exit;