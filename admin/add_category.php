<?php
require '../config/database.php';

// GIỮ NGUYÊN HOÀN TOÀN LOGIC CỦA BẠN - KHÔNG ĐỔI API
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);

    if (!empty($name)) {

        $stmt = $db->prepare("
            INSERT INTO categories (name)
            VALUES (?)
        ");

        $stmt->bind_param("s", $name);
        $stmt->execute();

        header("Location: category.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục mới - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: #fff8f9;
            /* Nền trắng hồng nhẹ nhàng đồng bộ hệ thống */
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Thẻ Card lớn bọc ngoài Form tinh tế */
        .glass-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 4px 25px rgba(255, 179, 193, 0.05);
            overflow: hidden;
        }

        /* Khung gợi ý / trạng thái bên cạnh */
        .stat-card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(255, 179, 193, 0.03);
        }

        /* Tinh chỉnh nhãn tên và ô nhập liệu mềm mại */
        .form-label-custom {
            font-size: 13px;
            font-weight: 700;
            color: #5c4d50;
            margin-left: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-custom {
            border-radius: 50px !important;
            padding: 14px 24px !important;
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

        /* Thiết kế nút bấm chuẩn pastel viên thuốc */
        .btn-submit-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px;
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
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0">➕ Thêm Danh Mục Mới</h2>
                    <p class="text-muted small m-0 mt-1">Cập nhật phân loại mới vào cơ sở dữ liệu hệ thống</p>
                </div>
                <div>
                    <a href="category.php"
                        class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm bg-white border-light-subtle text-secondary small">
                        <i class="bi bi-arrow-left me-1"></i> Quay Lại Danh Sách
                    </a>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-xl-8 col-lg-7">
                    <div class="card glass-card p-4 p-md-5">

                        <div class="mb-4">
                            <h4 class="fw-bold text-dark m-0">Chi tiết thông tin danh mục</h4>
                            <p class="text-muted small mt-1">Hệ thống sẽ tự động đồng bộ hóa lên trang bộ lọc tìm kiếm
                                khách hàng</p>
                        </div>

                        <form method="POST" id="categoryForm" novalidate class="needs-validation">

                            <div class="mb-4">
                                <label class="form-label-custom">Tên danh mục hoa mới</label>
                                <input type="text" name="name" id="categoryName"
                                    class="form-control form-control-custom"
                                    placeholder="Ví dụ: Bó Hoa Hướng Dương, Giỏ Hoa Khai Trương..." required>
                                <div class="invalid-feedback ps-3 small mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i> Không được bỏ trống trường thông tin
                                    này.
                                </div>
                            </div>
                            <div class="mt-4 pt-2">
                                <button type="submit"
                                    class="btn-submit-brand d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-cloud-arrow-up-fill fs-5 me-2"></i> Lưu cấu trúc danh mục
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="d-flex flex-column gap-3">

                        <div class="card stat-card p-4 border-start border-danger border-4"
                            style="border-color: #ff758f !important;">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Mẹo
                                nhỏ cho bạn</h6>
                            <p class="text-secondary small m-0 lh-base">
                                Hãy gom các sản phẩm có cùng đặc điểm sử dụng hoặc phân khúc giá vào một danh mục để tối
                                ưu giao diện tìm kiếm tốt hơn cho website.
                            </p>
                        </div>

                        <div class="card stat-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase">Cơ sở dữ liệu</span>
                                    <h5 class="fw-bold text-success mt-1 mb-0"><i
                                            class="bi bi-circle-fill fs-6 me-1 small"></i> Hoạt động tốt</h5>
                                </div>
                                <div class="bg-body-tertiary text-danger rounded-3 p-3"
                                    style="color: #ff758f !important; background-color: #fff0f2 !important;">
                                    <i class="bi bi-database-check fs-4"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Giữ nguyên hoàn toàn logic kiểm tra giá trị Enter & Validation gốc của bạn
        const form = document.getElementById('categoryForm');
        const inputName = document.getElementById('categoryName');

        inputName.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                if (inputName.value.trim() !== '') {
                    inputName.setCustomValidity('');
                    form.classList.add('was-validated');
                } else {
                    e.preventDefault();
                    inputName.setCustomValidity('Trống');
                    form.classList.add('was-validated');
                }
            }
        });

        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    </script>
</body>

</html>