<?php
require '../config/auth.php';
require '../config/database.php';

$cart_id = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? '';

$row = $db->query("SELECT quantity FROM carts WHERE id = $cart_id")->fetch_assoc();

if (!$row) exit;

if ($type == 'inc') {
    $newQty = $row['quantity'] + 1;
} else {
    $newQty = $row['quantity'] - 1;
}

if ($newQty <= 0) {
    $db->query("DELETE FROM carts WHERE id = $cart_id");
} else {
    $db->query("UPDATE carts SET quantity = $newQty WHERE id = $cart_id");
}

header("Location: cart.php");
exit;