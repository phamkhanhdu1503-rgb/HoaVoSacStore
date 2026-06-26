<?php
require 'config/auth.php';
require 'config/database.php';

$user_id = $_SESSION['user_id'];

// ============================
// LẤY ORDER ID
// ============================
$order_id = isset($_GET['order_id'])
    ? (int)$_GET['order_id']
    : 0;

if ($order_id <= 0) {

    $_SESSION['error'] = "Đơn hàng không hợp lệ.";

    header("Location: my_orders.php");
    exit;
}

// ============================
// LẤY THÔNG TIN ĐƠN HÀNG
// CHỈ CHO PHÉP XEM ĐƠN CỦA CHÍNH MÌNH
// ============================
$sql = "
SELECT *
FROM orders
WHERE id = ?
AND user_id = ?
";

$stmt = $db->prepare($sql);

$stmt->bind_param(
    "ii",
    $order_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $_SESSION['error'] = "Đơn hàng không tồn tại hoặc bạn không có quyền xem.";

    header("Location: my_orders.php");
    exit;
}

$order = $result->fetch_assoc();

$stmt->close();
$db->close();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt hàng thành công - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/success.css">

   
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-11 col-md-9 col-lg-6">

            <div class="card success-card text-center">

                <div class="success-icon mb-3">
                    <i class="bi bi-check-circle-fill" style="color: #2a9d8f;"></i>
                </div>

                <h3 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">Đặt Hàng Thành Công!</h3>
                <p class="text-muted small px-3">
                    Cảm ơn bạn đã tin tưởng và lựa chọn những bông hoa tươi thắm tại <b>Hoa Vô Sắc</b>. Đơn hàng của bạn đang được hệ thống tiếp nhận xử lý.
                </p>

                <hr class="my-4">

                <div class="text-start mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-receipt me-2" style="color: #ff758f;"></i>Tóm tắt đơn hàng</h6>
                    
                    <div class="order-info-box">
                        <div class="info-row">
                            <span class="info-label">Mã đơn hàng</span>
                            <span class="info-value">#<?= $order['id']; ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Ngày đặt hàng</span>
                            <span class="info-value"><?= date("d/m/Y H:i", strtotime($order['created_at'])); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Trạng thái</span>
                            <div>
                                <span class="status-badge">
                                    <i class="bi bi-clock-history me-1"></i> <?= htmlspecialchars($order['status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="info-row border-0 pt-3">
                            <span class="info-label text-dark fw-bold" style="font-size: 16px;">Tổng thanh toán</span>
                            <span class="info-value text-danger" style="font-size: 18px; color: #a81c39 !important;">
                                <?= number_format($order['total'] ?? 0); ?>₫
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-center flex-wrap pt-2">
                    <a href="index.php" class="btn btn-continue d-inline-flex align-items-center gap-2">
                        <i class="bi bi-bag-heart-fill"></i> Tiếp tục mua sắm
                    </a>

                    <a href="my_orders.php" class="btn btn-view-order d-inline-flex align-items-center gap-2">
                        <i class="bi bi-box-seam-fill"></i> Xem đơn hàng
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>