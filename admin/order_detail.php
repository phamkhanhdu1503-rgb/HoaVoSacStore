<?php

// Kết nối đatabase 
require '../config/database.php';

// Kiểm tra xem có truyền id hay không
if (!isset($_GET['id'])) {
    die("Thiếu mã đơn hàng!");
}

// lấy id và ép kiểu
$order_id = (int) $_GET['id'];

//lấy thông tin đơn hàng
$stmt = $db->prepare("
    SELECT
        o.*,
        u.fullname,
        u.email,
        u.phone
    FROM orders o
    JOIN users u
        ON o.user_id = u.id
    WHERE o.id = ?
");

// Gán giá trị và thực thi lệnh
$stmt->bind_param("i", $order_id);
$stmt->execute();

// lấy kết quả trả về thành mảng
$order = $stmt->get_result()->fetch_assoc();

// kiểm tra có tìm thấy đơn hàng hay không
if (!$order) {
    die("Không tìm thấy đơn hàng!");
}

// lấy chi tiết sản phẩm trong đơn
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

// gắn id vào đơn hàng và thực thi câu lệnh
$stmt->bind_param("i", $order_id);
$stmt->execute();

// lấy danh sách sản phẩm trong đơn hàng
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $order['id'] ?> - HoaVoSacStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style/order_detail.css">

</head>

<body>

    <?php include '../sidebar.php'; ?>


    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0">📦 Chi Tiết Đơn Hàng #<?= $order['id'] ?></h2>
                    <p class="text-muted small m-0 mt-1">Xem thông tin vận chuyển và danh sách sản phẩm gói hoa khách
                        hàng đã đặt</p>
                </div>
                <div>
                    <a href="orders.php" class="btn btn-back-list d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left-short fs-5"></i> Quay lại danh sách
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="card info-card h-100">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-flower1 text-secondary me-1"></i> Danh sách
                            gói hoa đặt mua</h5>

                        <div class="table-responsive">
                            <table class="table table-custom table-hover m-0">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Hình ảnh</th>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th style="width: 80px;" class="text-center">SL</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = $items->fetch_assoc()) { ?>
                                        <tr>
                                            <td>
                                                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" width="70"
                                                    height="70" class="product-img-thump">
                                            </td>

                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </td>

                                            <td class="fw-semibold text-secondary">
                                                <?= number_format($item['price']) ?>₫
                                            </td>

                                            <td class="text-center fw-bold text-dark">
                                                x<?= $item['quantity'] ?>
                                            </td>

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

                <div class="col-xl-4 col-lg-5">
                    <div class="d-flex flex-column gap-4 h-100">

                        <div class="card info-card flex-grow-1">
                            <h5 class="fw-bold text-dark mb-4"><i
                                    class="bi bi-person-lines-fill text-secondary me-1"></i> Thông tin khách hàng</h5>

                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">Họ và tên người nhận</label>
                                <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($order['fullname']) ?></span>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">Địa chỉ Email</label>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($order['email']) ?></span>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">Số điện thoại liên hệ</label>
                                <span class="fw-semibold text-dark"><i
                                        class="bi bi-telephone text-muted me-1 small"></i>
                                    <?= htmlspecialchars($order['phone']) ?></span>
                            </div>

                            <div class="mb-2">
                                <label class="text-muted small d-block mb-1">Trạng thái xử lý hiện tại</label>
                                <span class="status-badge">
                                    <i class="bi bi-info-circle-fill me-1 small"></i>
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="card info-card border-top border-pink border-4"
                            style="border-top-color: #ff758f !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase">Tổng giá trị hóa đơn</span>
                                    <div class="total-price-banner mt-1"><?= number_format($order['total']) ?>₫</div>
                                </div>
                                <div class="bg-danger-subtle text-danger rounded-3 p-3"
                                    style="background-color: #ffe5ec !important; color: #ff758f !important;">
                                    <i class="bi bi-wallet2 fs-3"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>