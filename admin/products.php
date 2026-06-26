<?php
require '../config/database.php';
require '../config/admin_auth.php';
// GIỮ NGUYÊN HOÀN TOÀN LOGIC TRUY VẤN VÀ API GỐC CỦA BẠN
$sql = "
SELECT
    p.*,
    c.name AS category_name
FROM products p
LEFT JOIN categories c
    ON p.category_id = c.id
ORDER BY p.id DESC
";

$result = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style/products.css">
    
</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0">Quản Lý Danh Sách Sản Phẩm</h2>
                    <p class="text-muted small m-0 mt-1">Xem, sửa đổi thông tin hoặc xóa bớt các gói hoa trong hệ thống</p>
                </div>
                <div>
                    <a href="add_product.php" class="btn btn-add-brand d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i> Thêm Sản Phẩm Mới
                    </a>
                </div>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table table-custom table-hover m-0">

                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th style="width: 120px;">Tồn kho</th>
                                <th>Ngày tạo</th>
                                <th style="width: 180px;" class="text-center">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($result) > 0) { ?>

                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                    <tr>
                                        <td class="fw-bold text-secondary">
                                            #<?= $row['id'] ?>
                                        </td>

                                        <td class="fw-bold text-dark">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </td>

                                        <td>
                                            <span class="badge-category">
                                                <i class="bi bi-folder2 me-1"></i>
                                                <?= htmlspecialchars($row['category_name'] ?? 'Chưa phân loại') ?>
                                            </span>
                                        </td>

                                        <td class="price-text">
                                            <?= number_format($row['price']) ?>đ
                                        </td>

                                        <td class="fw-semibold">
                                            <?= $row['stock'] ?> sp
                                        </td>

                                        <td class="text-muted small">
                                            <?= $row['created_at'] ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-action-edit d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                </a>

                                                <a href="delete_product.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-action-delete d-inline-flex align-items-center gap-1"
                                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này không?')">
                                                    <i class="bi bi-trash3-fill"></i> Xóa
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted fw-semibold">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        Hệ thống chưa có sản phẩm nào được tạo
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>
                </div>
            </div> </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>