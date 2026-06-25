<?php
// Kết nối database
require '../config/database.php';

// Câu lệnh sql
$sql = "
SELECT
    o.*,
    u.fullname
FROM orders o
JOIN users u
    ON o.user_id = u.id
ORDER BY o.id DESC
";

// Trả kết quả
$result = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style/order.css">

</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">📦 Quản Lý Đơn Hàng</h2>
                <p class="text-muted small m-0 mt-1">Cập nhật trạng thái xử lý và theo dõi lịch sử mua hoa của khách
                    hàng</p>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table table-custom table-hover m-0">

                        <thead>
                            <tr>
                                <th style="width: 100px;">Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th style="width: 180px;">Trạng thái đơn</th>
                                <th>Ngày đặt</th>
                                <th style="width: 100px;" class="text-center">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($row = mysqli_fetch_assoc($result)) {
                                // Gán màu sắc phù hợp cho từng loại trạng thái đơn hàng để giao diện trực quan hơn
                                $status = $row['status'];
                                $btn_class = 'btn-warning text-dark'; // Mặc định chờ xử lý
                            
                                if ($status == 'Đang chuẩn bị') {
                                    $btn_class = 'btn-info text-dark';
                                } elseif ($status == 'Đang giao') {
                                    $btn_class = 'btn-primary text-white';
                                } elseif ($status == 'Đã giao') {
                                    $btn_class = 'btn-success text-white';
                                } elseif ($status == 'Đã hủy') {
                                    $btn_class = 'btn-secondary text-white';
                                }
                                ?>

                                <tr>
                                    <td class="fw-bold text-secondary">
                                        #<?= $row['id'] ?>
                                    </td>

                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-person-circle text-muted me-1"></i>
                                        <?= htmlspecialchars($row['fullname']) ?>
                                    </td>

                                    <td class="price-text">
                                        <?= number_format($row['total']) ?>₫
                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm dropdown-toggle status-dropdown-btn <?= $btn_class ?>"
                                                data-bs-toggle="dropdown">
                                                <?= htmlspecialchars($status) ?>
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="update_order_status.php?id=<?= $row['id'] ?>&status=Chờ xác nhận">
                                                        <i class="bi bi-hourglass-split me-1 text-warning"></i> Chờ xác nhận
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="update_order_status.php?id=<?= $row['id'] ?>&status=Đang chuẩn bị">
                                                        <i class="bi bi-box-seam me-1 text-info"></i> Đang chuẩn bị
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="update_order_status.php?id=<?= $row['id'] ?>&status=Đang giao">
                                                        <i class="bi bi-truck me-1 text-primary"></i> Đang giao
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="update_order_status.php?id=<?= $row['id'] ?>&status=Đã giao">
                                                        <i class="bi bi-check-circle-fill me-1 text-success"></i> Đã giao
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="update_order_status.php?id=<?= $row['id'] ?>&status=Đã hủy">
                                                        <i class="bi bi-x-circle-fill me-1"></i> Đã hủy đơn
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td class="text-muted small">
                                        <?= $row['created_at'] ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="order_detail.php?id=<?= $row['id'] ?>"
                                            class="btn btn-action-view d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-eye-fill"></i> Xem
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>