<?php
session_start();
require "../config/database.php";

// 1. KIỂM TRA QUYỀN TRUY CẬP (Chỉ Admin mới được vào trang này)
// Bạn có thể chỉnh sửa lại logic check session này cho đúng với hệ thống đăng nhập của bạn
if (!isset($_SESSION['username']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../login.php");
    exit();
}

// 2. XỬ LÝ CẬP NHẬT QUYỀN (Khi Admin nhấn nút Lưu)
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];

    // Kiểm tra giá trị hợp lệ của ENUM
    if (in_array($new_role, ['admin', 'user'])) {
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-custom-success'>Cập nhật quyền tài khoản thành công!</div>";
        } else {
            $message = "<div class='alert alert-custom-danger'>Đã xảy ra lỗi, vui lòng thử lại.</div>";
        }
        $stmt->close();
    }
}

// 3. LẤY DANH SÁCH THÀNH VIÊN
$query_users = "SELECT id, fullname, username, email, role, created_at FROM users ORDER BY id DESC";
$result_users = $db->query($query_users);
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Phân Quyền Tài Khoản - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style/dashboard.css">

    <style>
        /* ===================================================
           CUSTOM STYLE CHO TRANG PHÂN QUYỀN - HOA VÔ SẮC
        =================================================== */
        .permissions-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: none;
        }

        /* Custom Dropdown Select */
        .filter-select {
            display: inline-block;
            width: auto;
            padding: 0.4rem 2.5rem 0.4rem 1.2rem !important;
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.5;
            color: #5c4d50 !important;
            background-color: #fff0f2 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ff758f' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 1rem center !important;
            background-size: 11px 11px !important;
            border: 1.5px solid #ffccd5 !important;
            border-radius: 50px !important;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            transition: all 0.25s ease-in-out;
        }

        .filter-select:hover,
        .filter-select:focus {
            border-color: #ff758f !important;
            background-color: #ffffff !important;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(255, 117, 143, 0.15) !important;
        }

        /* Nút Lưu Đồng Bộ */
        .btn-save-custom {
            background-color: #ff758f !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 50px !important;
            padding: 0.4rem 1.2rem !important;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease-in-out;
        }

        .btn-save-custom:hover {
            background-color: #8a3a4b !important;
            box-shadow: 0 4px 10px rgba(138, 58, 75, 0.15);
        }

        /* Custom Table */
        .table-custom th {
            background-color: #fff0f2 !important;
            color: #5c4d50 !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none !important;
            padding: 12px 16px !important;
        }

        .table-custom td {
            padding: 16px !important;
            vertical-align: middle !important;
            color: #4a4a4a;
            border-bottom: 1px solid #fff0f2 !important;
        }

        /* Role Badges */
        .badge-admin {
            background-color: #ffe5ec !important;
            color: #ff758f !important;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
        }

        .badge-user {
            background-color: #f1f3f5 !important;
            color: #6c757d !important;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
        }

        /* Thông báo alert custom */
        .alert-custom-success {
            background-color: #d1e7dd !important;
            color: #0f5132 !important;
            border: none !important;
            border-radius: 12px !important;
            font-weight: 600;
        }

        .alert-custom-danger {
            background-color: #f8d7da !important;
            color: #842029 !important;
            border: none !important;
            border-radius: 12px !important;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <?php include '../sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="mb-4">
                <h2 class="fw-bold text-dark m-0">Phân Quyền Tài Khoản</h2>
                <p class="text-muted small m-0 mt-1">Quản trị và cấp quyền điều hành cho thành viên trên hệ thống Hoa Vô
                    Sắc</p>
            </div>

            <!-- Hiển thị thông báo kết quả cập nhật -->
            <?= $message ?>

            <div class="card permissions-card p-4">
                <div class="table-responsive">
                    <table class="table table-custom align-middle m-0">
                        <thead>
                            <tr>
                                <th>ID th viên</th>
                                <th>Thông Tin Tài Khoản</th>
                                <th>Email</th>
                                <th>Quyền Hiện Tại</th>
                                <th class="text-end">Hành Động Cấp Quyền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_users && $result_users->num_rows > 0): ?>
                                <?php while ($row = $result_users->fetch_assoc()): ?>
                                    <tr>
                                        <!-- ID -->
                                        <td class="fw-bold text-muted">#<?= $row['id'] ?></td>

                                        <!-- Tên & Username -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-mini d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                    style="background: #ffccd5; width: 35px; height: 35px; font-size: 0.85rem; color: #ff758f !important;">
                                                    <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                                                        <?= htmlspecialchars($row['fullname']) ?></div>
                                                    <span
                                                        class="text-muted small">@<?= htmlspecialchars($row['username']) ?></span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Email -->
                                        <td style="font-size: 0.9rem;"><?= htmlspecialchars($row['email']) ?></td>

                                        <!-- Badge Quyền hiện tại -->
                                        <td>
                                            <?php if ($row['role'] === 'admin'): ?>
                                                <span class="badge-admin"><i class="bi bi-shield-fill-check me-1"></i>Admin</span>
                                            <?php else: ?>
                                                <span class="badge-user"><i class="bi bi-person-fill me-1"></i>User</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Form đổi quyền hành động -->
                                        <td class="text-end">
                                            <form action="" method="POST"
                                                class="d-inline-flex gap-2 align-items-center justify-content-end">
                                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">

                                                <select name="role" class="form-select filter-select">
                                                    <option value="user" <?= ($row['role'] === 'user') ? 'selected' : '' ?>>Thành
                                                        viên (User)</option>
                                                    <option value="admin" <?= ($row['role'] === 'admin') ? 'selected' : '' ?>>Quản
                                                        trị (Admin)</option>
                                                </select>

                                                <button type="submit" name="update_role" class="btn btn-save-custom">
                                                    <i class="bi bi-floppy-fill"></i> Lưu
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Không tìm thấy thành viên nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php $db->close(); ?>