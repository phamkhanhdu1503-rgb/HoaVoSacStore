<?php
require '../config/auth.php';
require '../config/database.php';

$cart_id = (int)($_GET['id'] ?? 0);

$db->query("DELETE FROM carts WHERE id = $cart_id");

header("Location: cart.php");
exit;