<?php
require 'config/guest.php';
require 'config/database.php';

// nếu đã login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

/* =========================
   XỬ LÝ REGISTER
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // check rỗng
    if (
        empty($fullname) ||
        empty($username) ||
        empty($email) ||
        empty($phone) ||
        empty($address) ||
        empty($password) ||
        empty($confirm)
    ) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    }

    // check mật khẩu
    elseif ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {

        // check tồn tại user/email
        $check = $db->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $error = "Tên đăng nhập hoặc email đã tồn tại.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $db->prepare("
                INSERT INTO users(fullname, username, email, phone, address, password)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $insert->bind_param(
                "ssssss",
                $fullname,
                $username,
                $email,
                $phone,
                $address,
                $hash
            );

            if ($insert->execute()) {
                $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Đăng ký thất bại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng ký - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/register.css">
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-sm-11 col-md-9 col-lg-6">

                <div class="card shadow-lg register-card">
                    <div class="card-body p-4 p-md-5">

                        <!-- HEADER -->
                        <div class="text-center mb-4">

                            <img src="logo/logo.png" style="width:90px;margin-bottom:10px">

                            <h3 class="fw-bold">Đăng Ký Tài Khoản</h3>

                            <p class="text-muted small">
                                Trở thành thành viên Hoa Vô Sắc
                            </p>
                        </div>

                        <!-- ERROR -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <!-- FORM -->
                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="fullname" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>

                            <!-- ADDRESS -->
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="address" class="form-control" rows="2"
                                    placeholder="Nhập địa chỉ giao hàng..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" required>
                                <label class="form-check-label">
                                    Tôi đồng ý điều khoản
                                </label>
                            </div>

                            <button
                                class="btn btn-register w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-plus-fill fs-5"></i>
                                Tạo tài khoản ngay
                            </button>
                            <hr class="my-4">

                            <div class="text-center text-secondary small fw-medium">
                                Bạn đã có tài khoản rồi?
                                <a href="login.php" class="text-decoration-none ms-1 login-link">
                                    Đăng nhập ngay <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>