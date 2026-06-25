<?php
session_start();
require "config/database.php";

// Lấy order_id từ URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    die("Đơn hàng không hợp lệ.");
}

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Không tìm thấy đơn hàng.");
}

$order = $result->fetch_assoc();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt hàng thành công - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ===== FONT & NỀN ĐỒNG BỘ HỆ THỐNG ===== */
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            min-height: 100vh;
            background: #fff8f9; /* Nền trắng hồng nhẹ nhàng đồng bộ hệ thống */
            font-family: 'Quicksand', sans-serif;
            display: flex;
            align-items: center;
        }

        /* Thẻ chúc mừng bo góc lớn mềm mại, đổ bóng nhẹ */
        .success-card {
            border: none;
            border-radius: 24px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.06);
            overflow: hidden;
            padding: 40px !important;
        }

        /* Icon pháo hoa chúc mừng động viên */
        .success-icon {
            font-size: 4rem;
            color: #2a9d8f;
            display: inline-block;
            animation: pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }

        @keyframes pop {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }

        /* Bảng tóm tắt thông tin đơn hàng */
        .order-info-box {
            background-color: #fffbfb;
            border: 1px dashed #fbd0dd;
            border-radius: 16px;
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #fff0f3;
            font-size: 15px;
        }
        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #8a6d7d;
            font-weight: 500;
        }

        .info-value {
            color: #4a3040;
            font-weight: 700;
        }

        /* Huy hiệu trạng thái đơn hàng */
        .status-badge {
            background-color: #eefdfa;
            color: #2a9d8f;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Nút hành động viên thuốc */
        .btn-continue {
            background-color: #c0405a;
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            padding: 12px 28px;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(192, 64, 90, 0.2);
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-continue:hover {
            background-color: #a81c39;
            color: white;
            box-shadow: 0 6px 20px rgba(192, 64, 90, 0.3);
            transform: translateY(-1px);
        }

        .btn-view-order {
            background-color: #ffffff;
            color: #c0405a;
            font-weight: 700;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            padding: 12px 28px;
            font-size: 15px;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-view-order:hover {
            background-color: #fde8ef;
            color: #a81c39;
            border-color: #ff758f;
        }

        hr {
            border-top: 1px solid #fde8ef;
            opacity: 1;
        }
    </style>
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