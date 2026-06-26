<?php
session_start();

require 'config/database.php';

// ============================
// KIỂM TRA ID DANH MỤC
// ============================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Danh mục không tồn tại!");
}

$category_id = (int) $_GET['id'];

// ============================
// TÍNH TỔNG SỐ LƯỢNG GIỎ HÀNG TỪ DATABASE (Đồng bộ Navbar)
// ============================
$user_id = 1; // ⚠ Tạm thời set bằng 1 cho khớp với file add_to_cart của bạn
$total_quantity = 0;

$cart_count_query = $db->prepare("SELECT SUM(quantity) AS total FROM carts WHERE user_id = ?");
$cart_count_query->bind_param("i", $user_id);
$cart_count_query->execute();
$cart_count_result = $cart_count_query->get_result();

if ($cart_row = $cart_count_result->fetch_assoc()) {
    $total_quantity = $cart_row['total'] ?? 0;
}

// ============================
// LẤY TẤT CẢ DANH MỤC (Cho Menu Navbar)
// ============================
$categories = mysqli_query($db, "SELECT * FROM categories ORDER BY name");

// ============================
// LẤY THÔNG TIN DANH MỤC HIỆN TẠI
// ============================
$stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    die("Danh mục không tồn tại!");
}

// ============================
// LẤY SẢN PHẨM THEO DANH MỤC
// ============================
$stmt = $db->prepare("
    SELECT *
    FROM products
    WHERE category_id = ?
    ORDER BY id DESC
");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$products = $stmt->get_result();
?>

<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($category['name']) ?> – Hoa Vô Sắc</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style/categories.css">
</head>

<body class="bg-light">

  <nav class="navbar navbar-hoa navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center navbar-brand-hoa" href="index.php">
        <i class="bi bi-flower1 logo-icon"></i>
        <span>Hoa Vô Sắc</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Trang chủ</a></li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown"><i class="bi bi-flower2"></i> Sản phẩm</a>
            <ul class="dropdown-menu">
              <?php 
              // Reset con trỏ dữ liệu categories để loop menu điều hướng
              mysqli_data_seek($categories, 0);
              while ($cat = mysqli_fetch_assoc($categories)) { 
                $is_active = ($cat['id'] == $category_id) ? 'active' : '';
              ?>
                <li>
                  <a class="dropdown-item <?= $is_active ?>" href="categories.php?id=<?= $cat['id'] ?>">
                    <i class="bi bi-flower2"></i> <?= htmlspecialchars($cat['name']) ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-gift"></i> Hoa theo dịp</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#"><i class="bi bi-balloon-heart"></i> Sinh nhật</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-heart"></i> Tình yêu</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-shop"></i> Khai trương</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-trophy"></i> Chúc mừng</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-cloud-drizzle"></i> Chia buồn</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-event"></i> Ngày lễ</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-basket2"></i> Kiểu cắm hoa</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#"><i class="bi bi-bag-heart"></i> Bó hoa</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-basket"></i> Giỏ hoa</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-lamp"></i> Lẵng hoa</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-box2-heart"></i> Hộp hoa</a></li>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-newspaper"></i> Tin tức</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-telephone"></i> Liên hệ</a></li>
        </ul>

        <div class="navbar-actions d-flex align-items-center gap-2 flex-wrap">
          <div class="search-wrap">
            <input type="search" placeholder="Tìm hoa...">
            <button class="search-btn" type="button"><i class="bi bi-search"></i></button>
          </div>

          <div class="dropdown">
            <button class="nav-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
              <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</a></li>
              <li><a class="dropdown-item" href="register.php"><i class="bi bi-person-plus"></i> Đăng ký</a></li>
              <li><hr class="dropdown-divider my-1"></li>
              <li><a class="dropdown-item" href="my_orders.php"><i class="bi bi-box-seam"></i> Đơn hàng của tôi</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-person-badge"></i> Hồ sơ cá nhân</a></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
            </ul>
          </div>

          <a class="nav-icon-btn" href="carts/cart.php" title="Giỏ hàng">
            <i class="bi bi-cart3"></i> 
            <?php if ($total_quantity > 0): ?>
              <span class="cart-badge"><?= $total_quantity ?></span>
            <?php endif; ?>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container py-4">

    <div class="d-flex justify-content-between align-items-center category-header">
      <h3 class="category-title mb-0">
        🌸 Danh mục: <span><?= htmlspecialchars($category['name']) ?></span>
      </h3>
      <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Quay lại
      </a>
    </div>

    <div class="row g-4">
        <?php if ($products->num_rows > 0) { ?>
            <?php while ($row = $products->fetch_assoc()) { 
                $img = !empty($row['image']) ? $row['image'] : 'default.png';
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <div class="img-container">
                            <img src="uploads/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>

                        <div class="card-body-hoa">
                            <a href="product_detail.php?id=<?= $row['id'] ?>" class="product-title">
                                <?= htmlspecialchars($row['name']) ?>
                            </a>

                            <div class="product-price">
                                <?= number_format($row['price']) ?>₫
                            </div>

                            <div class="action-row">
                                <a href="#" class="btn-circle-action" title="Giảm số lượng"><i class="bi bi-dash-lg"></i></a>
                                
                                <button type="button" data-url="carts/add_to_cart.php?id=<?= $row['id'] ?>" class="btn-circle-action btn-add-to-cart-ajax" title="Thêm nhanh vào giỏ">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                
                                <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn-outline-hoa">Chi tiết</a>
                                <a href="carts/add_to_cart.php?id=<?= $row['id'] ?>" class="btn-submit-hoa">Mua ngay</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-warning rounded-4 border-0 shadow-sm text-center py-4">
                     <i class="bi bi-exclamation-triangle fs-4 d-block mb-2 text-warning"></i>
                     Chưa có sản phẩm nào trong danh mục này. Bạn vui lòng quay lại sau nhé!
                </div>
            </div>
        <?php } ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.querySelectorAll('.btn-add-to-cart-ajax').forEach(button => {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          const url = this.getAttribute('data-url');

          fetch(url, {
              method: 'GET',
              headers: {
                  'X-Requested-With': 'XMLHttpRequest'
              }
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  let badge = document.querySelector('.cart-badge');
                  let cartBtn = document.querySelector('.nav-icon-btn[title="Giỏ hàng"]');
                  
                  if (badge) {
                      let currentQty = parseInt(badge.textContent) || 0;
                      badge.textContent = currentQty + 1;
                  } else if (cartBtn) {
                      cartBtn.insertAdjacentHTML('beforeend', '<span class="cart-badge">1</span>');
                  }
                  
                  alert('Đã thêm sản phẩm vào giỏ hàng! ✨');
              } else {
                  alert('Có lỗi xảy ra: ' + (data.message || 'Không thể thêm sản phẩm.'));
              }
          })
          .catch(error => {
              console.error('Lỗi AJAX:', error);
              alert('Không thể kết nối đến máy chủ.');
          });
      });
  });
  </script>
</body>
</html>