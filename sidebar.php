<?php
// Lấy tên file hiện tại để bắt class active chính xác
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="/HoaVoSacStore/style/sidebar.css">

<div class="admin-sidebar">
    <div>
        <div class="sidebar-brand">
            <i class="bi bi-heart-fill fs-3" style="color: #ff758f;"></i>
            <span class="fw-bold fs-5 tracking-wide text-uppercase">Hoa Vô Sắc</span>
        </div>

        <div class="sidebar-menu">
            <a class="sidebar-link <?= ($current_page == 'admin/dashboard.php' || $current_page == 'index.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/dashboard.php">
                <i class="bi bi-speedometer2"></i> Tổng Quan Dashboard
            </a>
            
            <a class="sidebar-link <?= ($current_page == 'products.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/products.php">
                <i class="bi bi-box-seam-fill"></i> Quản Lý Sản Phẩm
            </a>
            
            <a class="sidebar-link <?= ($current_page == 'category.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/category.php">
                <i class="bi bi-folder-fill"></i> Danh Mục Phân Loại
            </a>
            
            <a class="sidebar-link <?= ($current_page == 'orders.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/orders.php">
                <i class="bi bi-receipt"></i> Quản Lý Đơn Hàng
            </a>

            <a class="sidebar-link <?= ($current_page == 'login_history.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/login_history.php">
                <i class="bi bi-clock-history"></i> Lịch Sử Đăng Nhập
            </a>

            <a class="sidebar-link <?= ($current_page == 'revenue_chart.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/revenue_chart.php">
                <i class="bi bi-bar-chart-line-fill"></i> Biểu Đồ Doanh Thu
            </a>

            <a class="sidebar-link <?= ($current_page == 'transaction_history.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/transaction_history.php">
                <i class="bi bi-credit-card-2-front-fill"></i> Lịch Sử Giao Dịch
            </a>
        </div>
    </div>

    <div class="pt-3 border-top border-white-50">
        <a class="sidebar-link text-center justify-content-center" style="background-color: #8a3a4b; color: white;" href="/HoaVoSacStore/logout.php">
            <i class="bi bi-box-arrow-right"></i> Đăng Xuất Hệ Thống
        </a>
    </div>
</div>