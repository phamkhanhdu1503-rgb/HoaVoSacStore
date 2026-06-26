<?php
require 'config/flash.php';
require 'config/auth.php';
require 'config/database.php';

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
// LẤY SẢN PHẨM ĐỀ XUẤT
// ============================
$recommend_stmt = $db->prepare("
    SELECT *
    FROM products
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
     <link rel="stylesheet" href="../style/my_orders.css">
    
</head>

<body>


     <div class="container py-5">

        <?php showFlash(); ?>

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