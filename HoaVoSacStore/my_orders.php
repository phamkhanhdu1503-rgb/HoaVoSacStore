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
// LẤY DANH SÁCH ĐƠN HÀNG
// ============================
$stmt = $db->prepare("
    SELECT *
    FROM orders
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

// ============================
// LẤY SẢN PHẨM ĐỀ XUẤT (Ngẫu nhiên 4 sản phẩm)
// ============================
$recommend_stmt = $db->prepare("
    SELECT * FROM products 
    ORDER BY RAND() 
    LIMIT 4
");
$recommend_stmt->execute();
$recommended_products = $recommend_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* ===== FONT & NỀN ĐỒNG BỘ ===== */
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            background: #fff8f9;
            font-family: 'Quicksand', sans-serif;
        }

        /* Thẻ Card bọc ngoài danh sách đơn hàng */
        .table-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.04);
            padding: 30px;
            overflow: hidden;
        }

        /* Tinh chỉnh bảng phẳng, bo góc mềm mại */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            border: none;
        }
        .table-custom thead th {
            background-color: #fde8ef !important; 
            color: #c0405a !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 16px;
            border: none;
        }
        .table-custom tbody td {
            padding: 16px;
            vertical-align: middle;
            color: #4a3040;
            font-size: 14px;
            border-bottom: 1px solid #fde8ef;
        }
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .table-custom tbody tr:hover td {
            background-color: #fff5f7 !important;
        }

        .table-custom thead tr th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .table-custom thead tr th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        /* Badge Trạng thái dạng viên thuốc mềm mại */
        .status-badge-info {
            background-color: #ffe5ec;
            color: #ff758f;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            display: inline-block;
        }

        .btn-view-detail {
            background-color: #ffffff;
            color: #c0405a;
            font-weight: 600;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 13px;
            transition: all 0.25s ease;
            text-decoration: none;
        }
        .btn-view-detail:hover {
            background-color: #c0405a;
            color: #ffffff;
            border-color: #c0405a;
            box-shadow: 0 4px 10px rgba(192, 64, 90, 0.15);
        }

        .btn-home-back {
            background-color: #ffffff;
            color: #4a3040;
            font-weight: 600;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            padding: 10px 24px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-home-back:hover {
            background-color: #fde8ef;
            color: #c0405a;
        }

        .alert-custom-empty {
            background-color: #ffffff;
            border: 1px dashed #fbd0dd;
            color: #4a3040;
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.03);
        }

        .price-text {
            font-weight: 700;
            color: #a81c39;
        }

        /* ===== ĐỀ XUẤT SẢN PHẨM STYLE HOA VÔ SẮC ===== */
        .section-recommend-title {
            font-weight: 700;
            color: #4a3040;
            position: relative;
            margin-top: 60px;
            margin-bottom: 25px;
        }
        .section-recommend-title span {
            color: #c0405a;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #fde8ef;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(232, 116, 138, 0.12);
        }
        .product-card .img-container {
            width: 100%;
            height: 240px;
            overflow: hidden;
            background: #fafafb;
        }
        .product-card .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .img-container img {
            transform: scale(1.04);
        }
        .product-card .card-body-hoa {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: center;
        }
        .product-card .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #3f6897;
            margin-bottom: 8px;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8rem;
            line-height: 1.4;
        }
        .product-card .product-title:hover {
            color: #c0405a;
        }
        .product-card .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #a81c39;
            margin-bottom: 15px;
        }
        .product-card .action-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
        }
        .product-card .btn-circle-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #e1e1e1;
            background: #fff;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
            flex-shrink: 0;
        }
        .product-card .btn-circle-action:hover {
            background: #fde8ef;
            border-color: #fbd0dd;
            color: #c0405a;
        }
        .product-card .btn-outline-hoa {
            flex: 1;
            height: 36px;
            border: 1px solid #4a5568;
            background: #fff;
            color: #4a5568;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        .product-card .btn-outline-hoa:hover {
            background: #f4f5f7;
            color: #222;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-dark m-0">📦 Đơn Hàng Của Tôi</h2>
                <p class="text-muted small m-0 mt-1">Theo dõi trạng thái và lịch sử tất cả các gói hoa bạn đã đặt mua</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-home-back d-inline-flex align-items-center gap-1">
                    <i class="bi bi-house-door"></i> Quay lại trang chủ
                </a>
            </div>
        </div>

        <?php if ($orders->num_rows > 0) { ?>
            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table table-custom table-hover m-0">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Mã đơn hàng</th>
                                <th>Tổng thanh toán</th>
                                <th>Trạng thái xử lý</th>
                                <th>Ngày đặt hàng</th>
                                <th style="width: 160px;" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders->fetch_assoc()) { ?>
                                <tr>
                                    <td class="fw-bold text-dark">#<?= $order['id'] ?></td>
                                    <td class="price-text"><?= number_format($order['total']) ?>₫</td>
                                    <td>
                                        <span class="status-badge-info">
                                            <i class="bi bi-truck me-1"></i> <?= htmlspecialchars($order['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-view-detail d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-eye-fill"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-custom-empty text-center" role="alert">
                <i class="bi bi-bag-x fs-1 d-block mb-3" style="color: #ff758f;"></i>
                <span class="fw-bold d-block mb-1 fs-5 text-dark">Bạn chưa có đơn hàng nào</span>
                <p class="small text-muted m-0 mb-4">Hãy ghé thăm cửa hàng hoa và chọn cho mình những sản phẩm tươi thắm nhất nhé!</p>
                <a href="index.php" class="btn btn-view-detail rounded-pill px-4 py-2">Khám phá cửa hàng ngay</a>
            </div>
        <?php } ?>


        <h3 class="section-recommend-title">
            🌸 Có thể bạn sẽ <span>yêu thích...</span>
        </h3>

        <div class="row g-4">
            <?php if ($recommended_products && $recommended_products->num_rows > 0) { ?>
                <?php while ($prod = $recommended_products->fetch_assoc()) { 
                    $img = !empty($prod['image']) ? $prod['image'] : 'default.png';
                ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <div class="img-container">
                                <img src="uploads/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                            </div>

                            <div class="card-body-hoa">
                                <a href="product_detail.php?id=<?= $prod['id'] ?>" class="product-title">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>

                                <div class="product-price">
                                    <?= number_format($prod['price']) ?>₫
                                </div>

                                <div class="action-row">
                                    <button type="button" data-url="carts/add_to_cart.php?id=<?= $prod['id'] ?>" class="btn-circle-action btn-add-to-cart-ajax" title="Thêm nhanh vào giỏ">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                    
                                    <a href="product_detail.php?id=<?= $prod['id'] ?>" class="btn-outline-hoa">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12 text-center text-muted small">Đang cập nhật các mẫu hoa mới...</div>
            <?php } ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.querySelectorAll('.btn-add-to-cart-ajax').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã thêm sản phẩm vào giỏ hàng! ✨');
                    // Nếu trang này có nhét Navbar vào, bạn có thể cộng dồn số lượng Badge ở đây
                } else {
                    alert('Thêm vào giỏ hàng thành công! ✨');
                }
            }).catch(() => {
                // Đề phòng trường hợp file add_to_cart của bạn chuyển hướng thay vì trả json
                window.location.reload(); 
            });
        });
    });
    </script>
</body>

</html>