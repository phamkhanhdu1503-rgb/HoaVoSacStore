<?php
require 'config/guest.php';
// Kết nối database
require 'config/database.php'; // Nếu chưa dùng database.php thì thay bằng new mysqli như cũ

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Kiểm tra dữ liệu
    if (
        empty($fullname) ||
        empty($username) ||
        empty($email) ||
        empty($phone) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Vui lòng nhập đầy đủ thông tin.";

    } elseif ($password !== $confirm_password) {

        $error = "Mật khẩu xác nhận không khớp.";

    } else {

        // Kiểm tra username hoặc email đã tồn tại
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $error = "Tên đăng nhập hoặc Email đã tồn tại.";

        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users(fullname, username, email, phone, password)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);

            $stmt->bind_param(
                "sssss",
                $fullname,
                $username,
                $email,
                $phone,
                $hashed_password
            );

            if ($stmt->execute()) {

                $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                header("Location: login.php");
                exit;

            } else {

                $error = "Đăng ký thất bại. Vui lòng thử lại.";

            }
        }

        $stmt->close();
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

                        <div class="text-center mb-4">

    <img src="logo/logo.png"
         alt="Logo"
         style="width: 90px; height: auto; margin-bottom: 10px;">

    <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">
        Đăng Ký Tài Khoản
    </h3>

    <p class="text-muted small m-0 mt-1">
        Trở thành thành viên để nhận nhiều ưu đãi từ Hoa Vô Sắc
    </p>
</div>

                        <form action="process_register.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control form-control-custom w-100" name="fullname"
                                        placeholder="Nhập họ và tên của bạn..." required>
                                    <i class="bi bi-card-text"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control form-control-custom w-100" name="username"
                                        placeholder="Tạo tên tài khoản đăng nhập..." required>
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group-custom">
                                    <input type="email" class="form-control form-control-custom w-100" name="email"
                                        placeholder="Nhập địa chỉ email của bạn..." required>
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <div class="input-group-custom">
                                    <input type="tel" class="form-control form-control-custom w-100" name="phone"
                                        placeholder="Nhập số điện thoại nhận hàng..." required>
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <div class="input-group-custom">
                                    <input type="password" class="form-control form-control-custom w-100"
                                        name="password" placeholder="Tạo mật khẩu bảo mật..." required>
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu</label>
                                <div class="input-group-custom">
                                    <input type="password" class="form-control form-control-custom w-100"
                                        name="confirm_password" placeholder="Nhập lại mật khẩu phía trên..." required>
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="agree" required>
                                <label class="form-check-label text-secondary small fw-medium" for="agree"
                                    style="cursor: pointer;">
                                    Tôi đồng ý với các điều khoản sử dụng của cửa hàng
                                </label>
                            </div>

                            <button type="submit"
                                class="btn btn-register w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-plus-fill fs-5"></i> Tạo tài khoản ngay
                            </button>

                        </form>

                        <hr class="my-4">

                        <div class="text-center text-secondary small fw-medium">
                            Bạn đã có tài khoản rồi?
                            <a href="login.php" class="text-decoration-none login-link ms-1">
                                Đăng nhập ngay <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>