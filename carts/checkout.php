<?php
require '../config/auth.php';
require '../config/database.php';
require '../config/flash.php';

$user_id = $_SESSION['user_id'];

/* =========================
   LẤY GIỎ HÀNG
========================= */
$sql = "
SELECT
    c.product_id,
    c.quantity,
    p.price,
    p.stock
FROM carts c
JOIN products p ON c.product_id = p.id
WHERE c.user_id = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlash("warning", "Giỏ hàng trống!");
    header("Location: ../cart.php");
    exit;
}

$total = 0;
$items = [];

/* =========================
   CHECK STOCK + TÍNH TIỀN
========================= */
while ($row = $result->fetch_assoc()) {

    if ($row['quantity'] > $row['stock']) {
        setFlash("warning", "Sản phẩm không đủ tồn kho!");
        header("Location: ../cart.php");
        exit;
    }

    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

/* =========================
   XỬ LÝ ORDER
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_method = $_POST['payment_method'] ?? 'COD';

    $db->begin_transaction();

    try {

        /* =====================
           CREATE ORDER
        ===================== */
        $order = $db->prepare("
            INSERT INTO orders (user_id, total, status, payment_method)
            VALUES (?, ?, 'Chờ xác nhận', ?)
        ");

        $order->bind_param("ids", $user_id, $total, $payment_method);
        $order->execute();

        $order_id = $db->insert_id;

        /* =====================
           ORDER DETAILS
        ===================== */
        foreach ($items as $item) {

            $detail = $db->prepare("
                INSERT INTO order_details
                (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            $detail->bind_param(
                "iiid",
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            );

            $detail->execute();

            /* SOLD */
            $sold = $db->prepare("
                UPDATE products
                SET sold = sold + ?
                WHERE id = ?
            ");

            $sold->bind_param("ii", $item['quantity'], $item['product_id']);
            $sold->execute();

            /* STOCK */
            $stock = $db->prepare("
                UPDATE products
                SET stock = stock - ?
                WHERE id = ?
            ");

            $stock->bind_param("ii", $item['quantity'], $item['product_id']);
            $stock->execute();
        }

        /* =====================
           CLEAR CART
        ===================== */
        $clear = $db->prepare("DELETE FROM carts WHERE user_id = ?");
        $clear->bind_param("i", $user_id);
        $clear->execute();

        $db->commit();

        header("Location: ../success.php?order_id=" . $order_id);
        exit;

    } catch (Exception $e) {

        $db->rollback();

        setFlash("danger", "Đặt hàng thất bại!");
        header("Location: ../cart.php");
        exit;
    }
}
?>