<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ===== FONT & NỀN ĐỒNG BỘ HỆ THỐNG ===== */
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            min-height: 100vh;
            background: #fff8f9; /* Đồng bộ màu nền với trang Đăng nhập và Chi tiết */
            font-family: 'Quicksand', sans-serif;
            display: flex;
            align-items: center;
        }

        /* Thẻ register card bo góc mềm mại, đổ bóng nhẹ tinh tế */
        .register-card {
            border: none;
            border-radius: 24px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.06);
            overflow: hidden;
        }

        /* Biểu tượng bông hoa thương hiệu */
        .flower-icon {
            font-size: 3rem;
            color: #ff758f;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }

        /* Ô nhập liệu tinh chỉnh viền pastel và tích hợp icon */
        .form-label {
            font-weight: 600;
            color: #4a3040;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a8b2bd;
            z-index: 10;
            transition: color 0.2s;
        }

        .form-control-custom {
            padding: 12px 16px 12px 45px;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            color: #4a3040;
            background-color: #fffbfb;
            transition: all 0.25s ease;
        }

        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: #ff758f;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 117, 143, 0.15);
        }

        .form-control-custom:focus + i {
            color: #ff758f;
        }

        /* Custom Checkbox */
        .form-check-input:checked {
            background-color: #c0405a;
            border-color: #c0405a;
        }
        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(192, 64, 90, 0.15);
            border-color: #fbd0dd;
        }

        /* Nút đăng ký dạng viên thuốc */
        .btn-register {
            background-color: #c0405a;
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(192, 64, 90, 0.2);
            transition: all 0.25s ease;
        }

        .btn-register:hover {
            background-color: #a81c39;
            color: white;
            box-shadow: 0 6px 20px rgba(192, 64, 90, 0.3);
            transform: translateY(-1px);
        }

        /* Đường liên kết phụ trợ điều hướng */
        .login-link {
            color: #c0405a;
            font-weight: 700;
        }

        .login-link:hover {
            color: #a81c39;
        }

        hr {
            border-top: 1px solid #fde8ef;
            opacity: 1;
        }
    </style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-11 col-md-9 col-lg-6">

            <div class="card shadow-lg register-card">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <div class="flower-icon mb-2">
                            <i class="bi bi-flower3"></i>
                        </div>
                        <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Đăng Ký Tài Khoản</h3>
                        <p class="text-muted small m-0 mt-1">Trở thành thành viên để nhận nhiều ưu đãi từ Hoa Vô Sắc</p>
                    </div>

                    <form action="process_register.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <div class="input-group-custom">
                                <input type="text" 
                                       class="form-control form-control-custom w-100" 
                                       name="fullname" 
                                       placeholder="Nhập họ và tên của bạn..." 
                                       required>
                                <i class="bi bi-card-text"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <div class="input-group-custom">
                                <input type="text" 
                                       class="form-control form-control-custom w-100" 
                                       name="username" 
                                       placeholder="Tạo tên tài khoản đăng nhập..." 
                                       required>
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group-custom">
                                <input type="email" 
                                       class="form-control form-control-custom w-100" 
                                       name="email" 
                                       placeholder="Nhập địa chỉ email của bạn..." 
                                       required>
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <div class="input-group-custom">
                                <input type="tel" 
                                       class="form-control form-control-custom w-100" 
                                       name="phone" 
                                       placeholder="Nhập số điện thoại nhận hàng..." 
                                       required>
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <div class="input-group-custom">
                                <input type="password" 
                                       class="form-control form-control-custom w-100" 
                                       name="password" 
                                       placeholder="Tạo mật khẩu bảo mật..." 
                                       required>
                                <i class="bi bi-lock-fill"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu</label>
                            <div class="input-group-custom">
                                <input type="password" 
                                       class="form-control form-control-custom w-100" 
                                       name="confirm_password" 
                                       placeholder="Nhập lại mật khẩu phía trên..." 
                                       required>
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agree" required>
                            <label class="form-check-label text-secondary small fw-medium" for="agree" style="cursor: pointer;">
                                Tôi đồng ý với các điều khoản sử dụng của cửa hàng
                            </label>
                        </div>

                        <button type="submit" class="btn btn-register w-100 d-flex align-items-center justify-content-center gap-2">
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