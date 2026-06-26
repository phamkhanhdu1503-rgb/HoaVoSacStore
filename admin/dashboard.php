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

    <link rel="stylesheet" href="../style/revenue_chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <?php include '../sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">Hệ Thống Tổng Quan Dashboard</h2>
                <p class="text-muted small m-0 mt-1">Báo cáo số liệu thời gian thực và các lối tắt điều hành cửa hàng
                    Hoa Vô Sắc</p>
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
                    <div class="card card-stat-custom p-4 border-start border-pink border-4"
                        style="border-left-color: #ff758f !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase tracking-wider">Tổng Doanh
                                    Thu</span>
                                <h2 class="fw-bold text-brand mt-2 mb-0"><?= number_format($revenue) ?>đ</h2>
                            </div>
                            <div class="icon-shape bg-danger-subtle text-danger"
                                style="background-color: #ffe5ec !important; color: #ff758f !important;">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark m-0">Lối Tắt Thao Tác Nhanh</h5>
                <p class="text-muted small m-0">Truy cập nhanh đến các phân vùng chức năng xử lý dữ liệu và giao diện hệ
                    thống</p>
            </div>

            <div class="row g-3">

                <?php
                // Mảng danh sách lối tắt đã được bổ sung thêm nút phân quyền và trang chủ
                $menus = [
                    ['url' => 'products.php', 'icon' => 'bi-grid-1x2-fill', 'title' => 'Quản lý sản phẩm', 'color' => 'text-secondary'],
                    ['url' => 'category.php', 'icon' => 'bi-folder-fill', 'title' => 'Danh mục phân loại', 'color' => 'text-secondary'],
                    ['url' => 'orders.php', 'icon' => 'bi-receipt', 'title' => 'Quản lý đơn hàng', 'color' => 'text-secondary'],
                    ['url' => 'permissions.php', 'icon' => 'bi-shield-check', 'title' => 'Phân quyền tài khoản', 'color' => 'text-secondary'],
                    ['url' => 'login_history.php', 'icon' => 'bi-shield-lock-fill', 'title' => 'Lịch sử đăng nhập', 'color' => 'text-secondary'],
                    ['url' => 'revenue_chart.php', 'icon' => 'bi-bar-chart-line-fill', 'title' => 'Biểu đồ doanh thu', 'color' => 'text-secondary'],
                    ['url' => 'transaction_history.php', 'icon' => 'bi-clock-history', 'title' => 'Lịch sử giao dịch', 'color' => 'text-secondary'],
                    ['url' => '../index.php', 'icon' => 'bi-house-heart-fill', 'title' => 'Giao diện trang chủ', 'color' => 'text-secondary']
                ];

                foreach ($menus as $item):
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


            <?php
            $chart_data = array_fill(1, 12, 0);
            $query_chart = "SELECT MONTH(created_at) AS thang, SUM(total) AS doanh_thu 
                            FROM orders 
                            WHERE YEAR(created_at) = 2026 
                            GROUP BY MONTH(created_at)";

            $result_chart = $db->query($query_chart);
            if ($result_chart) {
                while ($row = $result_chart->fetch_assoc()) {
                    $thang = (int) $row['thang'];
                    $chart_data[$thang] = (float) $row['doanh_thu'];
                }
            }
            $data_json = json_encode(array_values($chart_data));
            ?>

            <div class="chart-container-card mt-5 mb-5">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                    <div>
                        <span class="chart-title"><i class="bi bi-graph-up-arrow me-2"></i>Phân Tích Xu Hướng Doanh
                            Số</span>
                        <p class="text-muted small m-0 mt-1">Biểu đồ thống kê chi tiết theo từng tháng kinh doanh của hệ
                            thống</p>
                    </div>
                    <span class="badge px-3 py-2 text-dark d-flex align-items-center gap-1"
                        style="background-color: #ffccd5; border-radius: 50px; font-weight: 600;">
                        <i class="bi bi-cash-stack text-brand"></i> Tổng tích lũy: <?= number_format($revenue) ?> đ
                    </span>
                </div>

                <div style="position: relative; width: 100%; height: 320px;">
                    <canvas id="dashboardRevenueChart"></canvas>
                </div>
            </div>

            <script>
                const ctxDash = document.getElementById('dashboardRevenueChart').getContext('2d');
                new Chart(ctxDash, {
                    type: 'bar',
                    data: {
                        labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
                        datasets: [{
                            label: 'Doanh số thực tế (đ)',
                            data: <?= $data_json ?>,
                            backgroundColor: 'rgba(255, 117, 143, 0.75)',
                            borderColor: '#8a3a4b',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: '#8a3a4b',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#fff0f2' },
                                ticks: { color: '#5c4d50', font: { size: 12 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#5c4d50', font: { weight: '600', size: 12 } }
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>