<?php
// Lấy tên file hiện tại để bắt class active chính xác
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* --- GIỮ NGUYÊN CẤU TRÚC STYLE HỒNG PHẤN CỦA BẠN --- */
    .admin-sidebar {
        width: 280px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: linear-gradient(180deg, #ffb3c1 0%, #ffccd5 100%);
        color: #4a4a4a;
        padding: 24px;
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(255, 179, 193, 0.15);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 8px;
        margin-bottom: 40px;
    }
    .sidebar-brand span {
        color: #8a3a4b !important;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 6px;
        max-height: calc(100vh - 180px);
        overflow-y: auto;
        padding-right: 4px;
    }

    .sidebar-menu::-webkit-scrollbar { width: 4px; }
    .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.4); border-radius: 10px; }
    
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #5c4d50;
        text-decoration: none;
        padding: 12px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.25s ease;
    }
    
    .sidebar-link i {
        font-size: 16px;
    }

    .sidebar-link:hover {
        background-color: rgba(255, 255, 255, 0.4);
        color: #8a3a4b;
    }

    .sidebar-link.active {
        background-color: #ffffff;
        color: #ff758f;
        box-shadow: 0 4px 15px rgba(255, 117, 143, 0.15);
    }

    .main-content {
        margin-left: 280px;
        padding: 40px;
        min-height: 100vh;
    }

    @media (max-width: 991.98px) {
        .admin-sidebar { 
            position: static; 
            width: 100%; 
            height: auto; 
            padding: 20px;
        }
        .sidebar-brand { margin-bottom: 20px; }
        .main-content { margin-left: 0; padding: 20px; }
    }
</style>

<div class="admin-sidebar">
    <div>
        <div class="sidebar-brand">
            <i class="bi bi-heart-fill fs-3" style="color: #ff758f;"></i>
            <span class="fw-bold fs-5 tracking-wide text-uppercase">Hoa Vô Sắc</span>
        </div>

        <div class="sidebar-menu">
            <a class="sidebar-link <?= ($current_page == 'dashboard.php' || $current_page == 'index.php') ? 'active' : '' ?>" href="/HoaVoSacStore/dashboard.php">
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
            
            <a class="sidebar-link <?= ($current_page == 'order_detail.php') ? 'active' : '' ?>" href="/HoaVoSacStore/admin/order_detail.php">
                <i class="bi bi-file-earmark-text-fill"></i> Chi Tiết Đơn Hàng
            </a>
            
            <a class="sidebar-link <?= ($current_page == 'cart.php') ? 'active' : '' ?>" href="/HoaVoSacStore/carts/cart.php">
                <i class="bi bi-cart-fill"></i> Xem Giỏ Hàng
            </a>
            
            <a class="sidebar-link <?= ($current_page == 'checkout.php') ? 'active' : '' ?>" href="/HoaVoSacStore/carts/checkout.php">
                <i class="bi bi-credit-card-fill"></i> Thanh Toán Giao Dịch
            </a>
        </div>
    </div>

    <div class="pt-3 border-top border-white-50">
        <a class="sidebar-link text-center justify-content-center" style="background-color: #8a3a4b; color: white;" href="/HoaVoSacStore/logout.php">
            <i class="bi bi-box-arrow-right"></i> Đăng Xuất Hệ Thống
        </a>
    </div>
</div>