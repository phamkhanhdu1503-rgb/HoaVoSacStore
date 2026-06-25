<?php
//Kết nối cơ sở dữ liệu
require '../config/database.php';

// Lấy danh sách danh mục từ cơ sở dữ liệu để hiển thị trong dropdown
$categories = mysqli_query(
    $db,
    "SELECT * FROM categories ORDER BY name"
);

// Kiểm tra nếu form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ form và loại bỏ khoảng trắng thừa
    $name = mysqli_real_escape_string($db, trim($_POST['name']));
    $category_id = (int) $_POST['category_id'];
    $description = mysqli_real_escape_string($db, trim($_POST['description']));
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    // Khởi tạo biến để lưu tên file hình ảnh
    $imageName = '';

    // Kiểm tra nếu có file hình ảnh được upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        // upload hình ảnh vào thư mục uploads và lưu tên file vào cơ sở dữ liệu
        $targetDir = "../uploads/";

        // Tạo thư mục uploads nếu chưa tồn tại
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Tạo tên file duy nhất để tránh trùng lặp
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        // Di chuyển file từ thư mục tạm sang thư mục uploads
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $targetDir . $imageName
        );
    }

    // Chuẩn bị câu lệnh SQL để chèn dữ liệu vào bảng products
    $sql = "INSERT INTO products 
            (name, category_id, description, price, stock, image) 
            VALUES 
            ('$name', '$category_id', '$description', '$price', '$stock', '$imageName')";
    // Thực thi câu lệnh SQL
    mysqli_query($db, $sql);
    // Chuyển hướng người dùng về trang danh sách sản phẩm sau khi thêm thành công
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
    <link rel="stylesheet" href="../style/add_product.css">
</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">➕ Thêm Sản Phẩm Mới</h2>
                <p class="text-muted small m-0 mt-1">Điền thông tin và đăng tải hình ảnh để hiển thị gói hoa mới lên cửa
                    hàng</p>
            </div>

            <div class="card form-card">
                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-tag-fill me-1 text-muted"></i> Tên sản phẩm
                            hoa</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên gói hoa..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label"><i class="bi bi-folder-fill me-1 text-muted"></i> Danh mục phân
                                loại</label>
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
                            <label class="form-label"><i class="bi bi-boxes me-1 text-muted"></i> Số lượng tồn
                                kho</label>
                            <input type="number" name="stock" class="form-control" placeholder="0" min="0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-currency-dollar me-1 text-muted"></i> Giá bán gốc
                            (đ)</label>
                        <input type="number" name="price" class="form-control" placeholder="Nhập giá tiền..." min="0"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-file-earmark-text-fill me-1 text-muted"></i> Mô tả chi
                            tiết gói hoa</label>
                        <textarea name="description" class="form-control" rows="5"
                            placeholder="Viết vài dòng giới thiệu về ý nghĩa hoặc thành phần của gói hoa..."></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label"><i class="bi bi-cloud-arrow-up-fill me-1 text-muted"></i> Hình ảnh đại
                            diện sản phẩm</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text text-muted small mt-1">Vui lòng chọn ảnh định dạng JPG, PNG, hoặc WEBP.
                        </div>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>