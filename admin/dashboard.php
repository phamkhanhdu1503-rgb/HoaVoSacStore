<?php
session_start();
require "../config/database.php";
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
     <link rel="stylesheet" href="../style/dashboard.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">Hệ Thống Tổng Quan Dashboard</h2>
                <p class="text-muted small m-0 mt-1">Báo cáo số liệu thời gian thực và các lối tắt điều hành cửa hàng Hoa Vô Sắc</p>
            </div>

            <?php
            // GIỮ NGUYÊN TOÀN BỘ CÂU LỆNH SQL GỐC CỦA BẠN
            $user = $db->query("SELECT COUNT(*) AS t FROM users")->fetch_assoc()['t'];
            $product = $db->query("SELECT COUNT(*) AS t FROM products")->fetch_assoc()['t'];
            $order = $db->query("SELECT COUNT(*) AS t FROM orders")->fetch_assoc()['t'];
            $revenue = $db->query("SELECT SUM(total) AS t FROM orders")->fetch_assoc()['t'] ?? 0;
            ?>

            <div class="row g-4 mb-5">

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stat-custom p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase tracking-wider">Người Dùng</span>
                                <h2 class="fw-bold text-dark mt-2 mb-0"><?= $user ?></h2>
                            </div>
                            <div class="icon-shape bg-info-subtle text-info">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stat-custom p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase tracking-wider">Sản Phẩm Gói</span>
                                <h2 class="fw-bold text-dark mt-2 mb-0"><?= $product ?></h2>
                            </div>
                            <div class="icon-shape bg-success-subtle text-success">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stat-custom p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase tracking-wider">Đơn Hàng Đặt</span>
                                <h2 class="fw-bold text-dark mt-2 mb-0"><?= $order ?></h2>
                            </div>
                            <div class="icon-shape bg-warning-subtle text-warning">
                                <i class="bi bi-receipt-cutoff"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stat-custom p-4 border-start border-pink border-4" style="border-left-color: #ff758f !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase tracking-wider">Tổng Doanh Thu</span>
                                <h2 class="fw-bold text-brand mt-2 mb-0"><?= number_format($revenue) ?>đ</h2>
                            </div>
                            <div class="icon-shape bg-danger-subtle text-danger" style="background-color: #ffe5ec !important; color: #ff758f !important;">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="mb-4">
                <h5 class="fw-bold text-dark m-0">Lối Tắt Thao Tác Nhanh</h5>
                <p class="text-muted small m-0">Truy cập nhanh đến các phân vùng chức năng xử lý dữ liệu</p>
            </div>

            <div class="row g-3">
                
                <?php
                $menus = [
                    ['url' => 'admin/products.php', 'icon' => 'bi-grid-1x2-fill', 'title' => 'Quản lý sản phẩm', 'color' => 'text-secondary'],
                    ['url' => 'admin/add_product.php', 'icon' => 'bi-plus-circle-fill', 'title' => 'Thêm sản phẩm mới', 'color' => 'text-secondary'],
                    ['url' => 'categories.php', 'icon' => 'bi-folder-fill', 'title' => 'Danh mục phân loại', 'color' => 'text-secondary'],
                    ['url' => 'admin/orders.php', 'icon' => 'bi-receipt', 'title' => 'Danh sách đơn hàng', 'color' => 'text-secondary'],
                    ['url' => 'admin/order_detail.php', 'icon' => 'bi-file-earmark-text-fill', 'title' => 'Chi tiết đơn đặt', 'color' => 'text-secondary'],
                    ['url' => 'carts/cart.php', 'icon' => 'bi-cart-fill', 'title' => 'Xem giỏ hàng', 'color' => 'text-secondary'],
                    ['url' => 'carts/checkout.php', 'icon' => 'bi-credit-card-fill', 'title' => 'Cổng thanh toán', 'color' => 'text-secondary']
                ];

                foreach($menus as $item):
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <a href="<?= $item['url'] ?>" class="text-decoration-none">
                        <div class="card menu-grid-card p-4 d-flex align-items-center gap-3 text-center">
                            <div class="menu-icon <?= $item['color'] ?> fs-4">
                                <i class="<?= $item['icon'] ?>"></i>
                            </div>
                            <h6 class="fw-bold text-dark m-0 small"><?= $item['title'] ?></h6>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>