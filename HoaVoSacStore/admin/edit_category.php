<?php
require '../config/database.php';

// ==================================
// 1. KIỂM TRA ID DANH MỤC
// ==================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID danh mục!");
}

$id = (int) $_GET['id'];

// ==================================
// 2. LẤY DỮ LIỆU DANH MỤC HIỆN TẠI
// ==================================
$stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    die("Không tìm thấy danh mục!");
}

// ==================================
// 3. XỬ LÝ LƯU CẬP NHẬT KHI SUBMIT FORM
// ==================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);

    $stmt = $db->prepare("
        UPDATE categories
        SET name = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $name, $id);
    $stmt->execute();

    // Chuyển hướng về lại đúng trang quản lý danh mục (dạng số ít)
    header("Location: category.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa danh mục phân loại - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: #fff8f9; /* Nền trắng hồng nhẹ nhàng đồng bộ hệ thống */
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Thẻ Card lớn bọc ngoài Form tinh tế */
        .form-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 25px rgba(255, 179, 193, 0.05);
            padding: 32px;
        }

        /* Tinh chỉnh nhãn tên và ô nhập liệu mềm mại */
        .form-label {
            font-weight: 600;
            color: #5c4d50;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid #f8e9ec;
            background-color: #fffbfb;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 14px;
            color: #495057;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #ffb3c1;
            box-shadow: 0 0 0 4px rgba(255, 179, 193, 0.15);
            color: #212529;
        }

        /* Kiểu dáng các nút bấm chức năng viên thuốc */
        .btn-submit-save {
            background-color: #ff758f;
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 10px 28px;
            box-shadow: 0 4px 12px rgba(255, 117, 143, 0.2);
            transition: all 0.25s ease;
        }
        .btn-submit-save:hover {
            background-color: #ff4d6d;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(255, 117, 143, 0.3);
            transform: translateY(-1px);
        }

        .btn-back-list {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            border: 1px solid #e9ecef;
            border-radius: 50px;
            padding: 10px 24px;
            transition: all 0.2s;
        }
        .btn-back-list:hover {
            background-color: #e2e6ea;
            color: #495057;
        }
    </style>
</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0" style="max-width: 700px; margin: 0 auto;">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">✏ Sửa Danh Mục Phân Loại</h2>
                <p class="text-muted small m-0 mt-1">Thay đổi tên nhóm sản phẩm để sắp xếp các gói hoa trên hệ thống hợp lý hơn</p>
            </div>

            <div class="card form-card">
                <form method="POST">

                    <div class="mb-5">
                        <label class="form-label">
                            <i class="bi bi-folder-fill text-muted me-1"></i> Tên danh mục hiện tại
                        </label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= htmlspecialchars($category['name']) ?>" 
                               placeholder="Ví dụ: Hoa Chúc Mừng, Hoa Sinh Nhật..." required>
                    </div>

                    <div class="d-flex align-items-center gap-3 pt-3 border-top border-light">
                        <button type="submit" class="btn btn-submit-save d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Cập Nhật Danh Mục
                        </button>
                        
                        <a href="category.php" class="btn btn-back-list d-inline-flex align-items-center gap-1">
                            <i class="bi bi-arrow-left-short fs-5"></i> Quay lại
                        </a>
                    </div>

                </form>
            </div> </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>