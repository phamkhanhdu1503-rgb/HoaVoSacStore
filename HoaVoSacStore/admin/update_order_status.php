<?php
require '../config/database.php';

if (
    !isset($_GET['id']) ||
    !isset($_GET['status'])
) {
    die("Thiếu dữ liệu!");
}

$id = (int)$_GET['id'];
$status = $_GET['status'];

$stmt = $db->prepare("
    UPDATE orders
    SET status = ?
    WHERE id = ?
");

$stmt->bind_param(
    "si",
    $status,
    $id
);

$stmt->execute();

header("Location: orders.php");
exit;
?>
