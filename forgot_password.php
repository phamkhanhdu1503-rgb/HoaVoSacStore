<?php
session_start();
require 'config/guest.php';
// Kết nối database
require '../config/database.php'; // Hoặc dùng new mysqli như cũ

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if (empty($username) || empty($email) || empty($new_password)) {

        $error = "Vui lòng nhập đầy đủ thông tin.";

    } else {

        $sql = "SELECT id FROM users WHERE username = ? AND email = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $db->prepare(
                "UPDATE users SET password = ? WHERE username = ?"
            );

            $update->bind_param(
                "ss",
                $hashed_password,
                $username
            );

            if ($update->execute()) {

                $_SESSION['success'] = "Đổi mật khẩu thành công! Vui lòng đăng nhập lại.";
                header("Location: login.php");
                exit;

            } else {

                $error = "Có lỗi xảy ra. Vui lòng thử lại.";

            }

            $update->close();

        } else {

            $error = "Tên đăng nhập hoặc Email không chính xác.";

        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="card shadow">
                    <div class="card-body">

                        <h3 class="text-center mb-4">Quên mật khẩu</h3>

                        <form action="process_forgot_password.php" method="POST">

                            <div class="mb-3">
                                <label>Tên đăng nhập</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>

                            <button class="btn btn-primary w-100">
                                Đổi mật khẩu
                            </button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>