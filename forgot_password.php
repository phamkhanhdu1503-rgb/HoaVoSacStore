<?php

require 'config/guest.php';
// Kết nối database
require 'config/database.php'; // Hoặc dùng new mysqli như cũ

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
    <link rel="stylesheet" href="style/forgot_password.css">    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-7 col-lg-5">

                <div class="card shadow-lg login-card">
                    <div class="card-body p-4 p-md-5">

                        <!-- HEADER -->
                        <div class="text-center mb-4">

                            <img src="logo/logo.png" alt="Logo" style="width: 85px; height: auto; margin-bottom: 10px;">

                            <h3 class="fw-bold m-0">Quên mật khẩu</h3>

                            <p class="text-muted small mt-1 m-0">
                                Khôi phục tài khoản của bạn
                            </p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <div class="input-group-custom">
                                    <input type="text" name="username" class="form-control form-control-custom w-100"
                                        required>
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group-custom">
                                    <input type="email" name="email" class="form-control form-control-custom w-100"
                                        required>
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <div class="input-group-custom">
                                    <input type="password" name="new_password"
                                        class="form-control form-control-custom w-100" required>
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                            </div>

                            <button class="btn btn-login w-100">
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