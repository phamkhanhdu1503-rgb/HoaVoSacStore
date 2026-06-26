<?php
session_start();
require "../config/database.php";

// Khởi tạo mảng 12 tháng với doanh thu ban đầu bằng 0
$chart_data = array_fill(1, 12, 0);

// Câu lệnh SQL Backend lấy tiền thật theo đúng từng tháng của năm 2026 từ database
$query_chart = "SELECT MONTH(created_at) AS thang, SUM(total) AS doanh_thu 
                FROM orders 
                WHERE YEAR(created_at) = 2026 
                GROUP BY MONTH(created_at)";

$result_chart = $db->query($query_chart);
if ($result_chart) {
    while ($row = $result_chart->fetch_assoc()) {
        $thang = (int)$row['thang'];
        $chart_data[$thang] = (float)$row['doanh_thu'];
    }
}

// Chuẩn bị mảng tên tháng để hiển thị dưới chân biểu đồ
$months = [
    "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", 
    "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
];

// Lấy mảng số tiền thật (đã tự động xếp đúng từ tháng 1 đến tháng 12)
$revenue_data = array_values($chart_data);

// Tính tổng doanh thu thực tế cả năm để hiển thị lên ô bộ lọc/tóm tắt phía dưới
$total_revenue = array_sum($revenue_data);
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
</head>

<body>

    <?php include '../sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="mb-4">
                <h2 class="fw-bold text-dark m-0">Báo Cáo & Biểu Đồ Doanh Thu</h2>
                <p class="text-muted small m-0 mt-1">Phân tích thống kê doanh số bán hàng theo dòng thời gian của Hoa Vô Sắc Store</p>
            </div>

            <div class="chart-container-card">
                <div class="row align-items-center g-3">
                    <div class="col-md-6 d-flex gap-2">
                        <select class="form-select filter-select w-auto">
                            <option value="2026" selected>Năm 2026</option>
                            <option value="2025">Năm 2025</option>
                        </select>
                        <select class="form-select filter-select w-auto">
                            <option value="all">Tất cả các tháng</option>
                            <option value="q1">Quý 1 (Tháng 1-3)</option>
                            <option value="q2">Quý 2 (Tháng 4-6)</option>
                        </select>
                        <button class="btn btn-filter"><i class="bi bi-funnel-fill me-1"></i> Lọc</button>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end gap-3">
                            <div class="stat-mini-box text-center">
                                <div class="title text-uppercase">Tổng Doanh Thu Quý</div>
                                <div class="value"><?= number_format($total_revenue, 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="stat-mini-box text-center">
                                <div class="title text-uppercase">Đơn Hàng Thành Công</div>
                                <div class="value" style="color: #2ec4b6;">148 Đơn</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-container-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="chart-title"><i class="bi bi-graph-up-arrow me-2"></i>Thống kê doanh số theo tháng</span>
                    <span class="badge px-3 py-2 text-dark" style="background-color: #ffccd5; border-radius: 50px; font-weight: 600;">Đơn vị: Việt Nam Đồng (VNĐ)</span>
                </div>
                
                <div style="position: relative; width: 100%; height: 400px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Lấy thẻ canvas ra
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Cấu hình các tham số vẽ biểu đồ cột (Bar Chart) phối màu hồng thương hiệu
        const revenueChart = new Chart(ctx, {
            type: 'bar', // Kiểu biểu đồ hình cột (bạn có thể đổi thành 'line' nếu thích biểu đồ đường)
            data: {
                labels: <?php echo json_encode($months); ?>, // Đổ mảng tháng từ PHP sang Javascript
                datasets: [{
                    label: 'Doanh thu bán hoa (đ)',
                    data: <?php echo json_encode($revenue_data); ?>, // Đổ mảng tiền từ PHP sang Javascript
                    backgroundColor: 'rgba(255, 117, 143, 0.75)', // Màu cột hồng phấn
                    borderColor: '#8a3a4b', // Đường viền cột màu đỏ nâu thẫm
                    borderWidth: 2,
                    borderRadius: 8, // Bo tròn nhẹ đầu cột cho mướt mắt
                    hoverBackgroundColor: '#8a3a4b', // Khi di chuột vào cột đổi sang màu đỏ thẫm mượt mà
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                family: 'system-ui',
                                weight: '600'
                            },
                            color: '#4a4a4a'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#fff0f2' // Đường lưới mờ màu hồng sữa
                        },
                        ticks: {
                            color: '#5c4d50',
                            font: {
                                weight: '500'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false // Tắt đường lưới dọc cho đỡ rối mắt
                        },
                        ticks: {
                            color: '#5c4d50',
                            font: {
                                weight: '600'
                            }
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