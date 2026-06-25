<?php
// Kết nối cơ sở dữ liệu
require '../config/database.php';
// Kiểm tra xem ID danh mục có được truyền qua URL hay không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID danh mục!");
}
// Lấy ID danh mục từ URL và chuyển đổi sang kiểu số nguyên
$id = (int) $_GET['id'];

// Lấy thông tin danh mục từ cơ sở dữ liệu để hiển thị trong form
$stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Lấy kết quả truy vấn và kiểm tra xem danh mục có tồn tại hay không
$result = $stmt->get_result();
$category = $result->fetch_assoc();

// Nếu danh mục không tồn tại, hiển thị thông báo lỗi
if (!$category) {
    die("Không tìm thấy danh mục!");
}

// Xử lý dữ liệu khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);

    $stmt = $db->prepare("
        UPDATE categories
        SET name = ?
        WHERE id = ?
    ");
    // Gán tham số và thực thi câu lệnh
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
    <link rel="stylesheet" href="../style/edit_category.css">
    
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