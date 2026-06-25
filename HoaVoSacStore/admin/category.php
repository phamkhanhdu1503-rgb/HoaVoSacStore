<?php
require '../config/database.php';

// Truy vấn lấy toàn bộ danh mục và đếm xem mỗi danh mục đang có bao nhiêu sản phẩm
$sql = "
SELECT 
    c.id, 
    c.name, 
    COUNT(p.id) AS total_products
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
GROUP BY c.id
ORDER BY c.id DESC
";

$result = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: #fff8f9; /* Nền trắng hồng nhẹ nhàng đồng bộ hệ thống */
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Thẻ Card lớn bọc ngoài bảng danh mục */
        .table-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 25px rgba(255, 179, 193, 0.05);
            padding: 24px;
            overflow: hidden;
        }

        /* Tinh chỉnh bảng Table phẳng, bo góc mềm mại */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            border: none;
        }
        .table-custom thead th {
            background-color: #ffccd5 !important; /* Đầu bảng màu hồng pastel sữa */
            color: #8a3a4b !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border: none;
        }
        .table-custom tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            color: #495057;
            font-size: 14px;
            border-bottom: 1px solid #f8e9ec;
            border-top: none;
        }
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .table-custom tbody tr:hover td {
            background-color: #fff0f2 !important; /* Hiệu ứng hover dòng nhẹ */
        }

        /* Bo góc cho phần đầu bảng */
        .table-custom thead tr th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .table-custom thead tr th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        /* Kiểu dáng nút Thêm danh mục viên thuốc */
        .btn-add-brand {
            background-color: #ff758f;
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 10px 24px;
            box-shadow: 0 4px 12px rgba(255, 117, 143, 0.2);
            transition: all 0.25s ease;
        }
        .btn-add-brand:hover {
            background-color: #ff4d6d;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(255, 117, 143, 0.3);
            transform: translateY(-1px);
        }

        /* Nút hành động sửa / xóa viên thuốc nhỏ gọn */
        .btn-action-edit {
            background-color: #ffe5ec;
            color: #ff4d6d;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 13px;
            transition: all 0.2s;
        }
        .btn-action-edit:hover {
            background-color: #ff4d6d;
            color: #ffffff;
        }

        .btn-action-delete {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 13px;
            transition: all 0.2s;
        }
        .btn-action-delete:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        /* Huy hiệu đếm số lượng sản phẩm */
        .badge-count {
            background-color: #ffe5ec;
            color: #ff758f;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0">Quản Lý Danh Mục Phân Loại</h2>
                    <p class="text-muted small m-0 mt-1">Quản lý cấu trúc nhóm sản phẩm hoa hiển thị trên giao diện bộ lọc</p>
                </div>
                <div>
                    <a href="add_category.php" class="btn btn-add-brand d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i> Thêm Danh Mục Mới
                    </a>
                </div>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table table-custom table-hover m-0">

                        <thead>
                            <tr>
                                <th style="width: 100px;">ID</th>
                                <th>Tên danh mục phân loại</th>
                                <th style="width: 220px;">Số lượng gói hoa</th>
                                <th style="width: 200px;" class="text-center">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($result && mysqli_num_rows($result) > 0) { ?>

                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                    <tr>
                                        <td class="fw-bold text-secondary">
                                            #<?= $row['id'] ?>
                                        </td>

                                        <td class="fw-bold text-dark fs-5">
                                            <i class="bi bi-folder2-open text-muted me-2"></i>
                                            <?= htmlspecialchars($row['name']) ?>
                                        </td>

                                        <td>
                                            <span class="badge-count">
                                                <i class="bi bi-flower1 me-1"></i>
                                                <?= $row['total_products'] ?> sản phẩm
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="edit_category.php?id=<?= $row['id'] ?>" class="btn btn-action-edit d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                </a>

                                                <a href="delete_category.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-action-delete d-inline-flex align-items-center gap-1"
                                                   onclick="return confirm('Bạn có chắc muốn xóa danh mục này? Các sản phẩm thuộc danh mục này sẽ về trạng thái Chưa phân loại!')">
                                                    <i class="bi bi-trash3-fill"></i> Xóa
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted fw-semibold">
                                        <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                        Hệ thống chưa ghi nhận danh mục phân loại nào.
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