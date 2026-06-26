<?php
session_start();

require 'config/database.php';

// ============================
// TÍNH TỔNG SỐ LƯỢNG GIỎ HÀNG TỪ DATABASE
// ============================
$user_id = 1; // ⚠ Tạm thời set bằng 1 cho khớp với file add_to_cart của bạn
$total_quantity = 0;

$cart_count_query = $db->prepare("SELECT SUM(quantity) AS total FROM carts WHERE user_id = ?");
$cart_count_query->bind_param("i", $user_id);
$cart_count_query->execute();
$cart_count_result = $cart_count_query->get_result();

if ($cart_row = $cart_count_result->fetch_assoc()) {
  // Nếu tổng khác null thì gán vào biến, ngược lại (giỏ trống) thì gán bằng 0
  $total_quantity = $cart_row['total'] ?? 0;
}
// ============================
// DANH MỤC
// ============================
$categories = mysqli_query(
  $db,
  "SELECT * FROM categories ORDER BY name"
);

// ============================
// 4 SẢN PHẨM MỚI NHẤT
// ============================
$sql = "
    SELECT *
    FROM products
    ORDER BY id DESC
    LIMIT 4
";

$result = mysqli_query($db, $sql);

// ============================
// 4 SẢN PHẨM NỔI BẬT
// ============================
$sql_featured = "
    SELECT *
    FROM products
    ORDER BY RAND()
    LIMIT 4
";

$result_featured = mysqli_query(
  $db,
  $sql_featured
);

// ============================
// 4 SẢN PHẨM BÁN CHẠY
// ============================
$sql_best_seller = "
    SELECT *
    FROM products
    ORDER BY sold DESC
    LIMIT 4
";

$result_best_seller = mysqli_query(
  $db,
  $sql_best_seller
);
?>

<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hoa Vô Sắc – Trang chủ</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style/footer.css">
  <style>
    /* ===== FONT ===== */
    @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

    body {
      font-family: 'Quicksand', sans-serif;
      padding-top: 74px;
    }

    /* ===== NAVBAR WRAPPER ===== */
    .navbar-hoa {
      background: #fff;
      border-bottom: 2px solid #fde8ef;
      box-shadow: 0 2px 16px rgba(255, 105, 135, 0.08);
    }

    /* ===== LOGO ===== */
    .navbar-brand-hoa {
      font-size: 1.35rem;
      font-weight: 700;
      color: #c0405a !important;
      letter-spacing: 0.5px;
      gap: 8px;
    }

    .navbar-brand-hoa .logo-icon {
      font-size: 1.6rem;
      color: #e8748a;
      line-height: 1;
    }

    /* ===== NAV LINKS ===== */
    .navbar-hoa .nav-link {
      font-size: 0.875rem;
      font-weight: 600;
      color: #4a3040 !important;
      padding: 0.5rem 0.75rem;
      border-radius: 8px;
      transition: background 0.18s, color 0.18s;
      display: flex;
      align-items: center;
      gap: 5px;
      white-space: nowrap;
    }

    .navbar-hoa .nav-link:hover,
    .navbar-hoa .nav-link.active {
      background: #fde8ef;
      color: #c0405a !important;
    }

    /* ===== DROPDOWN ===== */
    .navbar-hoa .dropdown-menu {
      border: none;
      border-radius: 12px;
      box-shadow: 0 8px 28px rgba(192, 64, 90, 0.13);
      padding: 6px;
      min-width: 170px;
      animation: dropIn 0.18s ease;
    }

    @keyframes dropIn {
      from {
        opacity: 0;
        transform: translateY(-6px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .navbar-hoa .dropdown-item {
      font-size: 0.845rem;
      font-weight: 600;
      color: #4a3040;
      border-radius: 8px;
      padding: 7px 12px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: background 0.15s, color 0.15s;
    }

    .navbar-hoa .dropdown-item:hover {
      background: #fde8ef;
      color: #c0405a;
    }


    /* ===== ICON ACTIONS ===== */
    .nav-icon-btn {
      background: none;
      border: none;
      color: #4a3040;
      font-size: 1.3rem;
      padding: 6px 8px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      transition: background 0.18s, color 0.18s;
      position: relative;
      cursor: pointer;
      text-decoration: none;
    }

    .nav-icon-btn:hover {
      background: #fde8ef;
      color: #c0405a;
    }

    .cart-badge {
      position: absolute;
      top: 2px;
      right: 2px;
      background: #c0405a;
      color: #fff;
      font-size: 0.6rem;
      font-weight: 700;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
    }

    .navbar-toggler {
      border: 1.5px solid #fbd0dd;
      border-radius: 8px;
      padding: 5px 9px;
    }

    .navbar-toggler:focus {
      box-shadow: none;
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23c0405a' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    
    /* ===== BANNER INTERFACE ===== */

    .carousel-banner-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #4a3040;
      line-height: 1.25;
    }

    .carousel-banner-sub {
      font-size: 0.95rem;
      color: #6c757d;
      margin: 12px 0 20px 0;
      line-height: 1.5;
    }

    
    /* ===================================================
       ===== STYLE PRODUCT CARD MỚI (VƯỜN HOA TƯƠI) =====
       =================================================== */
    .product-card {
      background: #ffffff;
      border: 1px solid #f3f3f3;
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
      position: relative;
      width: 100%;
      height: 260px;
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

    .product-card .btn-submit-hoa {
      flex: 1;
      height: 36px;
      background: #a81c39;
      color: #fff;
      font-size: 0.82rem;
      font-weight: 600;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: background 0.2s;
      border: none;
    }

    .product-card .btn-submit-hoa:hover {
      background: #c0405a;
      color: #fff;
    }

    /* ===== MOBILE ADJUSTMENTS ===== */
    @media (max-width: 991.98px) {
      .navbar-collapse {
        background: #fff;
        border-top: 1px solid #fde8ef;
        margin-top: 8px;
        padding: 8px 4px 12px;
        border-radius: 0 0 12px 12px;
      }

      .search-wrap input {
        width: 100%;
      }

      .search-wrap input:focus {
        width: 100%;
      }

      .search-wrap {
        width: 100%;
        margin-bottom: 8px;
      }

      .navbar-hoa .nav-link {
        border-radius: 8px;
        margin: 1px 0;
      }

      .navbar-actions {
        border-top: 1px solid #fde8ef;
        padding-top: 10px;
        margin-top: 6px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        align-items: center;
      }

      .product-card .img-container {
        height: 190px;
      }

      .hero-banner {
        padding: 25px 0;
        text-align: center;
      }

      .hero-title {
        font-size: 1.8rem;
      }

      .hero-subtitle {
        font-size: 0.88rem;
        margin: 10px 0 15px 0;
      }

      .hero-img {
        max-height: 200px;
      }

      .hero-img-wrapper::before {
        width: 180px;
        height: 180px;
      }

      .hero-img-wrapper {
        margin-top: 20px;
      }

  /* Search đẹp mắt*/
  .search-suggestions-box{
    position:absolute;
    top:110%;
    left:0;
    width:100%;
    background:#fff;
    border:1px solid #f3d7df;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    z-index:9999;
}

.suggestion-item{
    display:flex;
    align-items:center;
    gap:12px;

    padding:10px 12px;

    text-decoration:none;
    color:#333;

    transition:.2s;

    border-bottom:1px solid #f5f5f5;
}

.suggestion-item:last-child{
    border-bottom:none;
}

.suggestion-item:hover{
    background:#fff3f7;
}

.suggestion-img{
    width:55px;
    height:55px;

    border-radius:10px;

    object-fit:cover;

    border:1px solid #eee;

    flex-shrink:0;
}

.suggestion-info{
    flex:1;
}

.suggestion-name{
    font-size:15px;
    font-weight:600;
    color:#333;
    margin-bottom:4px;
}

.suggestion-price{
    color:#d63384;
    font-size:14px;
    font-weight:bold;
}

.suggestion-item-empty{
    padding:15px;
    text-align:center;
    color:#888;
    font-size:14px;
}
    }

    /* ===== SEARCH ===== */

.search-wrap {
    position: relative;
}

.search-wrap input {
    border: 1.5px solid #fbd0dd;
    border-radius: 20px;
    padding: 6px 36px 6px 14px;
    font-size: 0.82rem;
    width: 180px;
    background: #fff9fb;
    color: #4a3040;
    transition: all .2s ease;
    outline: none;
}

.search-wrap input:focus {
    border-color: #e8748a;
    width: 220px;
    background: #fff;
}

.search-wrap .search-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: none;
    color: #e8748a;
    font-size: 1rem;
    cursor: pointer;
}

/* ===== HỘP GỢI Ý ===== */

.search-suggestions-box {
    position: absolute;
    top: 110%;
    left: 0;
    width: 100%;
    background: #fff;
    border: 1px solid #fde8ef;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(192,64,90,.12);
    max-height: 320px;
    overflow-y: auto;
    z-index: 9999;
}

/* ===== ITEM ===== */

.suggestion-item {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 10px 14px;

    text-decoration: none;
    color: #333;

    transition: .2s;

    border-bottom: 1px solid #f8f8f8;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover {
    background: #fff3f6;
}

/* ===== ẢNH ===== */

.suggestion-item .suggestion-img {
    width: 60px !important;
    height: 60px !important;

    min-width: 60px;
    min-height: 60px;

    max-width: 60px;
    max-height: 60px;

    object-fit: cover;

    border-radius: 8px;

    border: 1px solid #eee;

    display: block;

    flex-shrink: 0;
}

/* ===== THÔNG TIN ===== */

.suggestion-info {
    flex: 1;
    overflow: hidden;
}

.suggestion-name {
    font-size: 15px;
    font-weight: 600;
    color: #333;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.suggestion-price {
    margin-top: 5px;
    color: #d63384;
    font-size: 14px;
    font-weight: bold;
}

/* ===== KHÔNG CÓ KẾT QUẢ ===== */

.suggestion-item-empty {
    padding: 15px;
    text-align: center;
    color: #888;
    font-size: 14px;
}
/*logo*/
.logo-img{
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.shop-name{
    font-size: 1.4rem;
    font-weight: 700;
    color: #4a3040;
}
html {
    scroll-behavior: smooth;
}
  </style>
</head>

<body class="bg-light">

  <nav class="navbar navbar-hoa navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center navbar-brand-hoa" href="index.php">
    <img src="logo/logo.png" alt="Hoa Vô Sắc" class="logo-img">
    <span class="shop-name ms-2">𝓗𝓸𝓪 𝓥𝓸 𝓢𝓪𝓬</span>
</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="index.html"><i class="bi bi-house-door"></i> Trang
              chủ</a></li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-flower2"></i> Sản
              phẩm</a>
            <ul class="dropdown-menu">
              <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                <li>
                  <a class="dropdown-item" href="categories.php?id=<?= $cat['id'] ?>">
                    <i class="bi bi-flower2"></i> <?= htmlspecialchars($cat['name']) ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>

          
          <li class="nav-item">
    <a class="nav-link" href="#contact">
        <i class="bi bi-telephone"></i> Liên hệ
    </a>
</li>
        </ul>

        <form action="search_results.php" method="GET" class="search-wrap" id="searchForm" autocomplete="off">
          <input type="search" name="keyword" id="searchInput" placeholder="Tìm hoa..." required>
          <button class="search-btn" type="submit"><i class="bi bi-search"></i></button>

          <div id="searchSuggestions" class="search-suggestions-box d-none"></div>
        </form>

        <div class="dropdown">
          <button class="nav-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
            <i class="bi bi-person-circle"></i>
            <?php if (isset($_SESSION['user_id'])): ?>
              <!-- Nếu đã đăng nhập, có thể hiện kèm tên user nhỏ nhỏ kế bên nếu muốn -->
              <span class="ms-1" style="font-size: 0.8rem; font-weight: 600; color: #4a3040;">
                <?= htmlspecialchars($_SESSION['username'] ?? 'Thành viên') ?>
              </span>
            <?php endif; ?>
          </button>

          <ul class="dropdown-menu dropdown-menu-end">
            <?php if (!isset($_SESSION['user_id'])): ?>
              <!-- CHƯA ĐĂNG NHẬP: Chỉ hiện Đăng nhập & Đăng ký -->
              <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</a></li>
              <li><a class="dropdown-item" href="register.php"><i class="bi bi-person-plus"></i> Đăng ký</a></li>
            <?php else: ?>
              <!-- ĐÃ ĐĂNG NHẬP: Ẩn đăng nhập/đăng ký, hiện các tính năng thành viên -->
              <li><a class="dropdown-item" href="my_orders.php"><i class="bi bi-box-seam"></i> Đơn hàng của tôi</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-person-badge"></i> Hồ sơ cá nhân</a></li>
              <li>
                <hr class="dropdown-divider my-1">
              </li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng
                  xuất</a></li>
            <?php endif; ?>
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

  <div id="mainBannerCarousel" class="carousel slide" data-bs-ride="carousel" style="background-color: #FCE7F3;">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#mainBannerCarousel" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#mainBannerCarousel" data-bs-slide-to="1"></button>
    </div>

    <div class="carousel-inner text-center">

      <div class="carousel-item active py-5" data-bs-interval="4000">
        <div class="py-5">
          <h1 class="carousel-banner-title">
            Nơi Cảm Xúc Nở Hoa
          </h1>

          <p class="carousel-banner-sub">
            Mỗi Bó Hoa – Một Câu Chuyện Yêu Thương 💐
          </p>
        </div>
      </div>

      <div class="carousel-item py-5" data-bs-interval="4000">
        <div class="py-5">
          <h1 class="carousel-banner-title">
            Hoa Thay Lời Yêu
          </h1>

          <p class="carousel-banner-sub">
            Trao nhau một đóa hoa, gửi nhau ngàn lời thương 🎀
          </p>
        </div>
      </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="prev">

      <span class="carousel-control-prev-icon" style="filter: invert(1);"></span>

    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="next">

      <span class="carousel-control-next-icon" style="filter: invert(1);"></span>

    </button>
  </div>

  <div class="container py-4" id="san-pham-moi">
    <h3 class="mb-4 fw-bold">✨ Sản phẩm mới nhất</h3>
    <div class="row g-4">
      <?php while ($row = mysqli_fetch_assoc($result)) {
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
                <button type="button" data-url="carts/add_to_cart.php?id=<?= $row['id'] ?>"
                  class="btn-circle-action btn-add-to-cart-ajax" title="Thêm nhanh vào giỏ">
                  <i class="bi bi-cart-plus"></i>
                </button>
                <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn-outline-hoa">Chi tiết</a>
                <a href="carts/add_to_cart.php?id=<?= $row['id'] ?>" class="btn-submit-hoa">Mua ngay</a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="container mt-4">
    <h3 class="mb-4 fw-bold">🔥 Sản phẩm nổi bật</h3>
    <div class="row g-4">
      <?php mysqli_data_seek($result_featured, 0);
      while ($row = mysqli_fetch_assoc($result_featured)) {
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
                <button type="button" data-url="carts/add_to_cart.php?id=<?= $row['id'] ?>"
                  class="btn-circle-action btn-add-to-cart-ajax" title="Thêm nhanh vào giỏ">
                  <i class="bi bi-cart-plus"></i>
                </button>
                <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn-outline-hoa">Chi tiết</a>
                <a href="carts/add_to_cart.php?id=<?= $row['id'] ?>" class="btn-submit-hoa">Mua ngay</a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="container mt-5 mb-5">
    <h3 class="mb-4 fw-bold">👑 Sản phẩm bán chạy</h3>
    <div class="row g-4">
      <?php while ($row = mysqli_fetch_assoc($result_best_seller)) {
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
                <button type="button" data-url="carts/add_to_cart.php?id=<?= $row['id'] ?>"
                  class="btn-circle-action btn-add-to-cart-ajax" title="Thêm nhanh vào giỏ">
                  <i class="bi bi-cart-plus"></i>
                </button>
                <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn-outline-hoa">Chi tiết</a>
                <a href="carts/add_to_cart.php?id=<?= $row['id'] ?>" class="btn-submit-hoa">Mua ngay</a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
  
  <div id="tu-van-hoa" class="container my-5 py-5 bg-light rounded-4 shadow-sm">
    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0 bg-light p-4 p-md-5 rounded-4">

                <div class="text-center mb-4">
                    <h2 class="text-center fw-bold mb-4" style="color:#a50920;">
                        Đăng Ký Nhận Tư Vấn Hoa
                    </h2>
                </div>

                <form id="tuVanForm"
                      action="submit_consult.php"
                      method="POST"
                      onsubmit="return validateForm(event)"
                      novalidate>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label small fw-bold text-secondary">
                                Họ và Tên
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control py-2"
                                id="txtHoTen"
                                name="txtHoTen"
                                placeholder="Ví dụ: Nguyễn Văn A">

                            <div
                                id="errorHoTen"
                                class="text-danger small mt-1"
                                style="display:none;font-weight:500;">
                            </div>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label small fw-bold text-secondary">
                                Số Điện Thoại
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control py-2"
                                id="txtDienThoai"
                                name="txtDienThoai"
                                placeholder="Ví dụ: 0912345678">

                            <div
                                id="errorDienThoai"
                                class="text-danger small mt-1"
                                style="display:none;font-weight:500;">
                            </div>

                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <button
                            type="reset"
                            class="btn btn-outline-secondary px-4 rounded-pill"
                            onclick="clearErrors()">

                            Nhập Lại

                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger px-4 rounded-pill shadow-sm"
                            style="background:#a50920;border:none;">

                            Gửi Yêu Cầu

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>

  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.querySelectorAll('.btn-add-to-cart-ajax').forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        const url = this.getAttribute('data-url');

        // Thực hiện gửi request ngầm lên server
        fetch(url, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Định vị lại vùng Badge
              let badge = document.querySelector('.cart-badge');
              let cartBtn = document.querySelector('.nav-icon-btn[title="Giỏ hàng"]');

              if (badge) {
                // Nếu đã có badge (số lượng > 0), cộng thêm 1 vào số lượng cũ
                let currentQty = parseInt(badge.textContent) || 0;
                badge.textContent = currentQty + 1;
              } else if (cartBtn) {
                // Nếu giỏ hàng đang trống hoàn toàn, tạo mới thẻ span badge
                cartBtn.insertAdjacentHTML('beforeend', '<span class="cart-badge">1</span>');
              }

              // Hiển thị thông báo nhỏ thành công (Có thể thay thế bằng Toast/Modal tùy thích)
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

    document.addEventListener("DOMContentLoaded", function () {
      const searchInput = document.getElementById("searchInput");
      const suggestionsBox = document.getElementById("searchSuggestions");
      const searchForm = document.getElementById("searchForm");

      if (!searchInput || !suggestionsBox || !searchForm) return;

      const renderSuggestions = (keyword) => {
        const trimmed = keyword.trim();

        if (trimmed.length < 1) {
          suggestionsBox.innerHTML = "";
          suggestionsBox.classList.add("d-none");
          return;
        }

        fetch(`ajax_search.php?keyword=${encodeURIComponent(trimmed)}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(response => response.text())
          .then(html => {
            suggestionsBox.innerHTML = html;
            if (html.trim()) {
              suggestionsBox.classList.remove("d-none");
            } else {
              suggestionsBox.classList.add("d-none");
            }
          })
          .catch(() => {
            suggestionsBox.innerHTML = '<div class="suggestion-item text-muted">Không thể tải gợi ý lúc này.</div>';
            suggestionsBox.classList.remove("d-none");
          });
      };

      searchInput.addEventListener("input", function () {
        renderSuggestions(this.value);
      });

      searchInput.addEventListener("focus", function () {
        renderSuggestions(this.value);
      });

      suggestionsBox.addEventListener("click", function (e) {
        const item = e.target.closest(".suggestion-item");
        if (item && item.dataset.name) {
          searchInput.value = item.dataset.name;
          suggestionsBox.classList.add("d-none");
          searchForm.submit();
        }
      });

      document.addEventListener("click", function (e) {
        if (!searchForm.contains(e.target)) {
          suggestionsBox.classList.add("d-none");
        }
      });
    });

    //=================================//
    var myCarouselEl = document.querySelector('#mainBannerCarousel');

    if (myCarouselEl) {
      new bootstrap.Carousel(myCarouselEl, {
        interval: 4000,
        ride: 'carousel',
        wrap: true
      });
    }

    
function clearErrors() {
    document.getElementById("errorHoTen").style.display = "none";
    document.getElementById("errorDienThoai").style.display = "none";
}

function validateForm(event) {

    event.preventDefault();

    clearErrors();

    let isValid = true;

    const hoTen = document.getElementById("txtHoTen").value.trim();
    const dienThoai = document.getElementById("txtDienThoai").value.trim();

    if (hoTen === "") {
        document.getElementById("errorHoTen").innerHTML = "Vui lòng nhập họ và tên.";
        document.getElementById("errorHoTen").style.display = "block";
        isValid = false;
    }

    if (!/^[0-9]{10,11}$/.test(dienThoai)) {
        document.getElementById("errorDienThoai").innerHTML = "Số điện thoại không hợp lệ.";
        document.getElementById("errorDienThoai").style.display = "block";
        isValid = false;
    }

    if (isValid) {
        document.getElementById("tuVanForm").submit();
    }

    return false;
}

  </script>

</body>

</html>