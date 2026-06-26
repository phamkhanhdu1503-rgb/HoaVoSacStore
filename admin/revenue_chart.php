<?php
session_start();
require "../config/database.php";

/* =========================
   YEAR DYNAMIC (KHÔNG FIX 2026 NỮA)
========================= */
$year = date('Y');

/* =========================
   KHỞI TẠO 12 THÁNG
========================= */
$chart_data = array_fill(1, 12, 0);

/* =========================
   LẤY DOANH THU THEO THÁNG
========================= */
$query_chart = "SELECT MONTH(created_at) AS thang, SUM(total) AS doanh_thu 
                FROM orders 
                WHERE YEAR(created_at) = $year
                GROUP BY MONTH(created_at)";

$result_chart = $db->query($query_chart);

if ($result_chart) {
    while ($row = $result_chart->fetch_assoc()) {
        $thang = (int)$row['thang'];
        $chart_data[$thang] = (float)($row['doanh_thu'] ?? 0);
    }
}

/* =========================
   LABEL THÁNG
========================= */
$months = [
    "Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6",
    "Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"
];

$revenue_data = array_values($chart_data);

/* =========================
   TỔNG DOANH THU
========================= */
$total_revenue = array_sum($revenue_data);

/* =========================
   SỐ ĐƠN HÀNG THẬT
========================= */
$order_count = $db->query("
    SELECT COUNT(*) AS t 
    FROM orders 
    WHERE YEAR(created_at) = $year
")->fetch_assoc()['t'];
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biểu Đồ Doanh Thu - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../style/revenue_chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ===================================================
           CUSTOM DROPDOWN SELECT - HOA VÔ SẮC STYLE
        =================================================== */
        .filter-select {
            display: inline-block;
            width: auto;
            padding: 0.5rem 2.5rem 0.5rem 1.5rem !important;
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1.5;
            color: #5c4d50 !important;
            background-color: #fff0f2 !important;
            /* Thay thế mũi tên mặc định bằng SVG màu hồng mảnh */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ff758f' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 1rem center !important;
            background-size: 12px 12px !important;
            border: 1.5px solid #ffccd5 !important;
            border-radius: 50px !important;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            transition: all 0.25s ease-in-out;
        }

        .filter-select:hover {
            border-color: #ff758f !important;
            background-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(255, 117, 143, 0.1);
        }

        .filter-select:focus {
            border-color: #ff758f !important;
            background-color: #ffffff !important;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(255, 117, 143, 0.25) !important;
        }

        .filter-select option {
            background-color: #ffffff;
            color: #5c4d50;
            font-weight: 500;
            padding: 10px;
        }

        /* Nút lọc custom đồng bộ */
        .btn-filter-custom {
            background-color: #ff758f !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 50px !important;
            padding: 0.4rem 1.8rem !important;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 10px rgba(255, 117, 143, 0.2);
            transition: all 0.2s ease-in-out;
        }

        .btn-filter-custom:hover {
            background-color: #8a3a4b !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(138, 58, 75, 0.2);
        }

        .btn-filter-custom:active {
            transform: translateY(1px);
        }

        /* Thẻ hiển thị số liệu nhỏ */
        .stat-mini-box {
            background: #ffffff;
            border: 1px solid #fff0f2;
            border-radius: 16px;
            padding: 0.6rem 1.5rem;
            min-width: 140px;
            box-shadow: 0 2px 8px rgba(255, 117, 143, 0.04);
        }

        .stat-mini-box .title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #a38f93;
            letter-spacing: 0.5px;
        }

        .stat-mini-box .value {
            font-size: 1.1rem;
            font-weight: 800;
            color: #2d2526;
            margin-top: 2px;
        }
    </style>
</head>

<body>

<?php include '../sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid p-0">

        <div class="mb-4">
            <h2 class="fw-bold text-dark m-0">
                Báo Cáo & Biểu Đồ Doanh Thu (<?= $year ?>)
            </h2>
            <p class="text-muted small m-0 mt-1">
                Phân tích doanh số bán hàng theo thời gian thực của cửa hàng Hoa Vô Sắc
            </p>
        </div>

        <div class="chart-container-card p-4" style="background: #ffffff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
            <div class="row align-items-center g-3">

                <div class="col-md-6 d-flex gap-2 flex-wrap">
                    <select class="form-select filter-select">
                        <option selected>Năm <?= $year ?></option>
                    </select>

                    <select class="form-select filter-select">
                        <option>Tất cả tháng</option>
                        <option>Quý 1</option>
                        <option>Quý 2</option>
                        <option>Quý 3</option>
                        <option>Quý 4</option>
                    </select>

                    <button class="btn btn-filter-custom">
                        <i class="bi bi-funnel-fill"></i> Lọc dữ liệu
                    </button>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end gap-3 flex-wrap">

                        <div class="stat-mini-box text-center">
                            <div class="title">Tổng Doanh Thu</div>
                            <div class="value" style="color: #ff758f;"><?= number_format($total_revenue) ?>đ</div>
                        </div>

                        <div class="stat-mini-box text-center">
                            <div class="title">Đơn Hàng</div>
                            <div class="value"><?= $order_count ?> đơn</div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="chart-container-card mt-4 p-4" style="background: #ffffff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="chart-title" style="font-weight: 700; color: #2d2526; font-size: 1.1rem;">
                    <i class="bi bi-graph-up-arrow me-2" style="color: #ff758f;"></i>
                    Xu Hướng Doanh Thu Các Tháng
                </span>

                <span class="badge px-3 py-2 text-dark"
                      style="background:#ffccd5; border-radius:50px; font-weight: 600; color: #8a3a4b !important;">
                    Đơn vị tính: VNĐ
                </span>
            </div>

            <div style="position: relative; height: 380px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Doanh thu thực tế (đ)',
            data: <?= json_encode($revenue_data) ?>,
            backgroundColor: 'rgba(255, 117, 143, 0.75)',
            borderColor: '#8a3a4b',
            borderWidth: 2,
            borderRadius: 8,
            hoverBackgroundColor: '#8a3a4b'
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
                grid: {
                    color: '#fff0f2'
                },
                ticks: {
                    color: '#5c4d50',
                    font: { size: 12 }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#5c4d50',
                    font: { weight: '600', size: 12 }
                }
            }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $db->close(); ?>