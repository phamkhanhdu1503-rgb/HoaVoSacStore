<?php
require '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID danh mục!");
}

$id = (int) $_GET['id'];

$stmt = $db->prepare("
    DELETE FROM categories
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: category.php");
exit;