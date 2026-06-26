<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $stmt = $db->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!password_verify($old, $user['password'])) {
        $error = "Mật khẩu cũ không đúng";
    } else {

        $hash = password_hash($new, PASSWORD_DEFAULT);

        $update = $db->prepare("UPDATE users SET password=? WHERE id=?");
        $update->bind_param("si", $hash, $id);
        $update->execute();

        $success = "Đổi mật khẩu thành công!";
    }
}
?>