<?php
require 'config/database.php';
session_start();

// ============================
// CHECK LOGIN
// ============================
if (!isset($_SESSION['user_id'])) {
    die("Vui lòng đăng nhập!");
}

$user_id = $_SESSION['user_id'];

// ============================
// CHECK ID ĐƠN HÀNG
// ============================
if (!isset($_GET['id'])) {
    die("Thiếu mã đơn hàng!");
}

$order_id = (int)$_GET['id'];

// ============================
// LẤY ĐƠN HÀNG
// ============================
$stmt = $db->prepare("
    SELECT *
    FROM orders
    WHERE id = ?
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $order_id,
    $user_id
);

$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Không tìm thấy đơn hàng!");
}

// ============================
// LẤY CHI TIẾT ĐƠN HÀNG
// ============================
$stmt = $db->prepare("
    SELECT
        od.*,
        p.name,
        p.image
    FROM order_details od
    JOIN products p
        ON od.product_id = p.id
    WHERE od.order_id = ?
");

$stmt->bind_param(
    "i",
    $order_id
);

$stmt->execute();

$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $order['id'] ?> - HoaVoSacStore</title>

    <!-- Thư viện chuẩn Bootstrap 5 & Icons đồng bộ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: #fff8f9; /* Nền trắng hồng nhẹ nhàng toàn trang cho khách hàng */
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Thẻ khối thông tin tóm tắt đơn hàng */
        .info-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 25px rgba(255, 179, 193, 0.05);
            padding: 24px;
        }

        /* Tinh chỉnh bảng sản phẩm phẳng, bo góc mềm mại */
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

        /* Bo khung ảnh sản phẩm nhỏ nhắn */
        .product-img-thump {
            border-radius: 10px;
            border: 1px solid #f8e9ec;
            object-fit: cover;
        }

        /* Định dạng hiển thị trạng thái đơn hàng viên thuốc */
        .status-badge {
            background-color: #ffe5ec;
            color: #ff758f;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            display: inline-block;
        }

        .price-text {
            font-weight: 700;
            color: #8a3a4b;
        }

        .total-price-banner {
            font-size: 22px;
            font-weight: 800;
            color: #ff4d6d;
        }

        /* Nút quay lại viên thuốc tinh tế */
        .btn-back-list {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            border: 1px solid #e9ecef;
            border-radius: 50px;
            padding: 10px 24px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-back-list:hover {
            background-color: #e2e6ea;
            color: #495057;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <!-- Phần Tiêu Đề Trên Cùng & Nút Quay Lại -->
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-dark m-0">📦 Chi Tiết Đơn Hàng #<?= $order['id'] ?></h2>
                <p class="text-muted small m-0 mt-1">Thông tin chi tiết các sản phẩm hoa bạn đã đặt mua tại hệ thống</p>
            </div>
            <div>
                <a href="my_orders.php" class="btn btn-back-list d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-short fs-5"></i> Trở lại danh sách
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- CỘT TRÁI (8 COL): DANH SÁCH SẢN PHẨM MUA -->
            <div class="col-xl-8 col-lg-7">
                <div class="card info-card h-100">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-flower1 text-secondary me-1"></i> Sản phẩm đã chọn đặt</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-custom table-hover m-0">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Hình ảnh</th>
                                    <th>Tên sản phẩm hoa</th>
                                    <th>Đơn giá</th>
                                    <th style="width: 80px;" class="text-center">SL</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = $items->fetch_assoc()) { ?>
                                    <tr>
                                        <!-- Hình ảnh gói hoa -->
                                        <td>
                                            <img src="uploads/<?= htmlspecialchars($item['image']) ?>" 
                                                 width="70" height="70" class="product-img-thump">
                                        </td>

                                        <!-- Tên gói hoa -->
                                        <td class="fw-bold text-dark">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </td>

                                        <!-- Đơn giá -->
                                        <td class="fw-semibold text-secondary">
                                            <?= number_format($item['price']) ?>₫
                                        </td>

                                        <!-- Số lượng mua -->
                                        <td class="text-center fw-bold text-dark">
                                            x<?= $item['quantity'] ?>
                                        </td>

                                        <!-- Thành tiền từng mục -->
                                        <td class="price-text text-end">
                                            <?= number_format($item['price'] * $item['quantity']) ?>₫
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI (4 COL): TRẠNG THÁI & TỔNG TIỀN -->
            <div class="col-xl-4 col-lg-5">
                <div class="d-flex flex-column gap-4 h-100">
                    
                    <!-- Card tóm tắt trạng thái vận đơn -->
                    <div class="card info-card flex-grow-1">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-info-circle text-secondary me-1"></i> Trạng thái đơn hàng</h5>
                        
                        <div class="mb-4">
                            <label class="text-muted small d-block mb-2">Trạng thái xử lý hiện tại</label>
                            <span class="status-badge">
                                <i class="bi bi-truck me-1 small"></i> <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </div>

                        <div class="mb-2">
                            <label class="text-muted small d-block mb-1">Thời gian ghi nhận đặt hàng</label>
                            <span class="fw-semibold text-dark small">
                                <i class="bi bi-clock text-muted me-1 small"></i> <?= date('H:i - d/m/Y', strtotime($order['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Card tổng thành toán hóa đơn -->
                    <div class="card info-card border-top border-pink border-4" style="border-top-color: #ff758f !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">Tổng thanh toán</span>
                                <div class="total-price-banner mt-1"><?= number_format($order['total']) ?>₫</div>
                            </div>
                            <div class="bg-danger-subtle text-danger rounded-3 p-3" style="background-color: #ffe5ec !important; color: #ff758f !important;">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> <!-- End Row -->

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>