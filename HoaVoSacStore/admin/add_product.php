<?php
require '../config/database.php';

// =========================
// LẤY DANH SÁCH DANH MỤC
// =========================
$categories = mysqli_query(
    $db,
    "SELECT * FROM categories ORDER BY name"
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($db, trim($_POST['name']));
    $category_id = (int) $_POST['category_id'];
    $description = mysqli_real_escape_string($db, trim($_POST['description']));
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    // =========================
    // UPLOAD ẢNH
    // =========================
    $imageName = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $targetDir = "../uploads/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . "_" . basename($_FILES['image']['name']);

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $targetDir . $imageName
        );
    }

    // =========================
    // THÊM SẢN PHẨM
    // =========================
    $sql = "INSERT INTO products 
            (name, category_id, description, price, stock, image) 
            VALUES 
            ('$name', '$category_id', '$description', '$price', '$stock', '$imageName')";

    mysqli_query($db, $sql);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: #fff8f9; /* Nền trắng hồng nhẹ nhàng đồng bộ hệ thống */
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Thẻ Card lớn bọc ngoài Form */
        .form-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 25px rgba(255, 179, 193, 0.05);
            padding: 32px;
        }

        /* Tinh chỉnh các ô nhập liệu bo góc mềm mại */
        .form-label {
            font-weight: 600;
            color: #5c4d50;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 1px solid #f8e9ec;
            background-color: #fffbfb;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            color: #495057;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
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
        <div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">➕ Thêm Sản Phẩm Mới</h2>
                <p class="text-muted small m-0 mt-1">Điền thông tin và đăng tải hình ảnh để hiển thị gói hoa mới lên cửa hàng</p>
            </div>

            <div class="card form-card">
                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-tag-fill me-1 text-muted"></i> Tên sản phẩm hoa</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên gói hoa..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label"><i class="bi bi-folder-fill me-1 text-muted"></i> Danh mục phân loại</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label"><i class="bi bi-boxes me-1 text-muted"></i> Số lượng tồn kho</label>
                            <input type="number" name="stock" class="form-control" placeholder="0" min="0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-currency-dollar me-1 text-muted"></i> Giá bán gốc (đ)</label>
                        <input type="number" name="price" class="form-control" placeholder="Nhập giá tiền..." min="0" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-file-earmark-text-fill me-1 text-muted"></i> Mô tả chi tiết gói hoa</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Viết vài dòng giới thiệu về ý nghĩa hoặc thành phần của gói hoa..."></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label"><i class="bi bi-cloud-arrow-up-fill me-1 text-muted"></i> Hình ảnh đại diện sản phẩm</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text text-muted small mt-1">Vui lòng chọn ảnh định dạng JPG, PNG, hoặc WEBP.</div>
                    </div>

                    <div class="d-flex align-items-center gap-3 pt-3 border-top border-light">
                        <button type="submit" class="btn btn-submit-save d-inline-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle-fill"></i> Lưu & Đăng Sản Phẩm
                        </button>
                        
                        <a href="products.php" class="btn btn-back-list d-inline-flex align-items-center gap-1">
                            <i class="bi bi-arrow-left-short fs-5"></i> Quay lại danh sách
                        </a>
                    </div>

                </form>
            </div> </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>