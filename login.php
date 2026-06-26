<?php
session_start();

// Kết nối database
require '../config/database.php'; // Nếu chưa có thì thay bằng new mysqli như cũ

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Kiểm tra dữ liệu
    if (empty($username) || empty($password)) {

        $error = "Vui lòng nhập đầy đủ thông tin.";

    } else {

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $db->prepare($sql);

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['username'] = $user['username'];

                // Nếu có cột role thì mở dòng dưới
                // $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit;

            } else {

                $error = "Sai mật khẩu.";

            }

        } else {

            $error = "Tên đăng nhập không tồn tại.";

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
    <title>Đăng nhập - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/login.css">

</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-7 col-lg-5">

                <div class="card shadow-lg login-card">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <div class="flower-icon mb-2">
                                <i class="bi bi-flower1"></i>
                            </div>
                            <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Đăng Nhập Hệ Thống</h3>
                            <p class="text-muted small m-0 mt-1">Chào mừng bạn đến với không gian của Hoa Vô Sắc</p>
                        </div>

                        <form action="process_login.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <div class="input-group-custom">
                                    <input type="text" class="form-control form-control-custom w-100" name="username"
                                        placeholder="Nhập tên tài khoản..." required>
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <div class="input-group-custom">
                                    <input type="password" class="form-control form-control-custom w-100"
                                        name="password" placeholder="Nhập mật khẩu..." required>
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label text-secondary small fw-medium" for="remember"
                                        style="cursor: pointer;">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>

                                <a href="forgot_password.php" class="text-decoration-none link-secondary-custom small">
                                    Quên mật khẩu?
                                </a>
                            </div>

                            <button type="submit"
                                class="btn btn-login w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-box-arrow-in-right fs-5"></i> Vào cửa hàng ngay
                            </button>

                        </form>

                        <hr class="my-4">

                        <div class="text-center text-secondary small fw-medium">
                            Bạn chưa có tài khoản?
                            <a href="register.php" class="text-decoration-none register-link ms-1">
                                Đăng ký ngay <i class="bi bi-arrow-right-short"></i>
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