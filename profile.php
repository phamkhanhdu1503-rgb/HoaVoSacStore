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

/* =========================
   GET USER
========================= */
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    die("User không tồn tại!");
}

/* =========================
   UPDATE PROFILE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    $update = $db->prepare("
        UPDATE users 
        SET fullname=?, email=?, phone=?, address=? 
        WHERE id=?
    ");

    $update->bind_param("ssssi", $fullname, $email, $phone, $address, $id);

    if ($update->execute()) {

        // 🔥 sync SESSION ngay lập tức
        $_SESSION['fullname'] = $fullname;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        $_SESSION['address'] = $address;

        $success = "Cập nhật thông tin thành công!";
        
        // Làm mới dữ liệu hiển thị trên giao diện sau khi update thành công
        $user['fullname'] = $fullname;
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['address'] = $address;
    } else {
        $error = "Cập nhật thất bại!";
    }

    /* =========================
       AVATAR UPLOAD
    ========================= */
    if (!empty($_FILES['avatar']['name'])) {

        $targetDir = "uploads/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['avatar']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
            $img = $db->prepare("UPDATE users SET avatar=? WHERE id=?");
            $img->bind_param("si", $fileName, $id);

            if ($img->execute()) {
                // 🔥 sync avatar session
                $_SESSION['avatar'] = $fileName;

                // reload để update UI lập tức
                header("Location: profile.php");
                exit;
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
    <title>Hồ sơ cá nhân - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #fff8f9; /* Nền trắng hồng nhẹ nhàng */
            font-family: 'Quicksand', sans-serif;
        }

        /* Thẻ Card trắng bo góc phẳng mềm mại */
        .glass-card {
            border: none;
            border-radius: 24px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(255, 179, 193, 0.06);
            padding: 30px;
        }

        /* Avatar bên cột trái */
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 6px 20px rgba(255, 117, 143, 0.15);
        }

        /* Tinh chỉnh nhãn text nhỏ, đậm */
        .form-label-custom {
            font-size: 13px;
            font-weight: 700;
            color: #8a3a4b;
            margin-left: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Input dạng viên thuốc mượt mà */
        .form-control-custom {
            border-radius: 50px !important;
            padding: 12px 24px !important;
            border: 1px solid #f8e9ec;
            font-size: 14px;
            background-color: #fffbfb;
            color: #495057;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            background-color: #fff;
            border-color: #ffb3c1;
            box-shadow: 0 0 0 4px rgba(255, 179, 193, 0.15);
            color: #212529;
        }

        textarea.form-control-custom {
            border-radius: 20px !important;
            padding: 16px 24px !important;
        }

        /* Nút chọn file */
        .form-control[type="file"] {
            border-radius: 50px;
            background-color: #fffbfb;
            border: 1px solid #f8e9ec;
            padding: 10px 18px;
            font-size: 14px;
        }
        .form-control[type="file"]::file-selector-button {
            background-color: #ffccd5;
            color: #8a3a4b;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            padding: 4px 16px;
            margin-right: 12px;
            transition: all 0.2s;
        }
        .form-control[type="file"]:hover::file-selector-button {
            background-color: #ff758f;
            color: #fff;
        }

        /* Nút lưu thông tin */
        .btn-submit-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 30px;
            background: linear-gradient(135deg, #ff758f 0%, #ff4d6d 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(255, 117, 143, 0.2);
            transition: all 0.25s ease;
        }
        .btn-submit-brand:hover {
            background: linear-gradient(135deg, #ff4d6d 0%, #c9184a 100%);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(255, 117, 143, 0.3);
        }

        .btn-back-home {
            background-color: #ffffff;
            color: #6c757d;
            font-weight: 600;
            border: 1px solid #f8e9ec;
            border-radius: 50px;
            padding: 10px 24px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .btn-back-home:hover {
            background-color: #fff0f2;
            color: #ff758f;
        }

        /* Huy hiệu trạng thái nhỏ */
        .badge-status {
            background-color: #fff0f2;
            color: #ff758f;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-block;
        }

        .alert {
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            border: none;
        }
    </style>
</head>

<body>

<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark m-0">🌸 Cài Đặt Tài Khoản</h2>
            <p class="text-muted small m-0 mt-1">Quản lý và cập nhật thông tin cá nhân của bạn trên hệ thống</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-back-home d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Quay lại cửa hàng
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-xl-4 col-lg-4">
            <div class="card glass-card text-center py-5">
                <div class="mb-3">
                    <img src="<?= ($user['avatar'] ?? '') ? 'uploads/'.$user['avatar'] : 'https://api.dicebear.com/7.x/adventurer/svg?seed='.urlencode($user['username'] ?? 'guest') ?>"
                         class="avatar-preview" alt="Avatar">
                </div>
                
                <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['fullname'] ?? 'Chưa cập nhật tên') ?></h4>
                <p class="text-muted small mb-3">@<?= htmlspecialchars($user['username'] ?? '') ?></p>
                
                <div class="mb-4">
                    <span class="badge-status">
                        <i class="bi bi-patch-check-fill me-1"></i> Thành viên Store
                    </span>
                </div>

                <hr class="mx-4 my-4" style="border-top: 1px dashed #f8e9ec;">

                <div class="text-start px-3">
                    <div class="d-flex align-items-center gap-3 mb-3 text-secondary small">
                        <i class="bi bi-shield-lock-fill fs-5 text-muted"></i>
                        <div>
                            <div class="fw-bold text-dark">Bảo mật tài khoản</div>
                            <span class="text-muted" style="font-size: 12px;">Thông tin được mã hóa an toàn</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 text-secondary small">
                        <i class="bi bi-truck fs-5 text-muted"></i>
                        <div>
                            <div class="fw-bold text-dark">Địa chỉ mặc định</div>
                            <span class="text-muted" style="font-size: 12px;">Dùng để định vị khi ship hoa tốc hành</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-8">
            <div class="card glass-card">
                
                <div class="mb-4">
                    <h5 class="fw-bold text-dark m-0">Thông tin chi tiết</h5>
                    <p class="text-muted small m-0 mt-1">Vui lòng điền đúng thông tin để nhận được các ưu đãi thành viên</p>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success p-3 mb-4 d-flex align-items-center gap-2" style="background-color: #e8f5e9; color: #2e7d32;">
                        <i class="bi bi-check-circle-fill"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger p-3 mb-4 d-flex align-items-center gap-2" style="background-color: #ffe5ec; color: #dc3545;">
                        <i class="bi bi-exclamation-circle-fill"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label-custom"><i class="bi bi-person-fill me-1"></i> Họ và tên</label>
                            <input type="text" name="fullname"
                                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                                   class="form-control form-control-custom" placeholder="Nhập họ và tên đầy đủ" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label-custom"><i class="bi bi-envelope-fill me-1"></i> Địa chỉ Email</label>
                            <input type="email" name="email"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                   class="form-control form-control-custom" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="bi bi-telephone-fill me-1"></i> Số điện thoại</label>
                        <input type="text" name="phone"
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               class="form-control form-control-custom" placeholder="Ví dụ: 0912345678">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="bi bi-geo-alt-fill me-1"></i> Địa chỉ giao nhận hoa</label>
                        <textarea name="address" class="form-control form-control-custom" rows="3"
                                  placeholder="Nhập chi tiết số nhà, tên đường, phường/xã, quận/huyện..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="bi bi-image me-1"></i> Thay đổi ảnh đại diện mới</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <div class="form-text ps-2 small text-muted mt-2">Định dạng khuyên dùng: JPG, PNG, WEBP tỷ lệ vuông.</div>
                    </div>

                    <hr class="my-4" style="border-top: 1px solid #f8e9ec;">

                    <div class="text-end">
                        <button type="submit" class="btn-submit-brand">
                            <i class="bi bi-cloud-arrow-up-fill fs-5 me-1"></i> Lưu thông tin thay đổi
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>