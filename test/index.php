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
  <title>cancanh - Trang chủ</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ===== FONT GLOBAL ===== */
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
      from { opacity: 0; transform: translateY(-6px); }
      to   { opacity: 1; transform: translateY(0); }
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

    /* ===== SEARCH ===== */
    .search-wrap { position: relative; }
    .search-wrap input {
      border: 1.5px solid #fbd0dd;
      border-radius: 20px;
      padding: 6px 36px 6px 14px;
      font-size: 0.82rem;
      width: 180px;
      background: #fff9fb;
      color: #4a3040;
      transition: border-color 0.18s, width 0.25s;
      outline: none;
    }
    .search-wrap input:focus { border-color: #e8748a; width: 220px; background: #fff; }
    .search-wrap .search-btn {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      border: none; background: none; color: #e8748a; font-size: 1rem; cursor: pointer;
    }

    /* ===== ICON ACTIONS ===== */
    .nav-icon-btn {
      background: none; border: none; color: #4a3040; font-size: 1.3rem; padding: 6px 8px;
      border-radius: 8px; display: flex; align-items: center; transition: background 0.18s, color 0.18s;
      position: relative; cursor: pointer; text-decoration: none;
    }
    .nav-icon-btn:hover { background: #fde8ef; color: #c0405a; }
    .cart-badge {
      position: absolute; top: 2px; right: 2px; background: #c0405a; color: #fff;
      font-size: 0.6rem; font-weight: 700; width: 16px; height: 16px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center; line-height: 1;
    }

    .navbar-toggler { border: 1.5px solid #fbd0dd; border-radius: 8px; padding: 5px 9px; }
    .navbar-toggler:focus { box-shadow: none; }
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

    /* ===== SEARCH SUGGESTIONS ===== */
    .search-suggestions-box {
      position: absolute;
      top: 110%;
      left: 0;
      width: 100%;
      background: #ffffff;
      border: 1px solid #fde8ef;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(192, 64, 90, 0.1);
      max-height: 250px;
      overflow-y: auto;
      z-index: 1100;
    }
    .suggestion-item {
      padding: 10px 14px;
      font-size: 0.85rem;
      color: #4a3040;
      cursor: pointer;
      transition: background 0.15s;
      border-bottom: 1px solid #fff5f7;
      display: block;
      text-decoration: none;
    }
    .suggestion-item:last-child { border-bottom: none; }
    .suggestion-item:hover { background: #fde8ef; color: #c0405a; }

    /* ===== FOOTER HOVER EFFECTS ===== */
    .text-hover-pink {
        transition: all 0.2s ease-in-out;
    }
    .text-hover-pink:hover {
        color: #f472b6 !important;
        padding-left: 4px;
    }
    .text-pink {
        color: #db2777 !important;
    }
    div.fs-5 a:hover {
        transform: translateY(-3px);
        color: #f472b6 !important;
    }

    /* CSS định dạng tạm thời cho Khung Product-Card để tránh vỡ giao diện */
    .product-card {
      background: #fff; border-radius: 12px; padding: 12px; height: 100%;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s;
    }
    .product-card:hover { transform: translateY(-4px); }
    .img-container img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
    .product-title { display: block; font-weight: 600; color: #4a3040; text-decoration: none; margin: 8px 0 4px; }
    .product-price { color: #c0405a; font-weight: 700; margin-bottom: 10px; }
    .action-row { display: flex; gap: 4px; flex-wrap: wrap; }
    .btn-circle-action { background: #fff5f7; border: none; color: #c0405a; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
    .btn-outline-hoa { border: 1px solid #fbd0dd; color: #c0405a; border-radius: 20px; padding: 3px 10px; font-size: 0.75rem; text-decoration: none; }
    .btn-submit-hoa { background: #c0405a; color: #fff; border-radius: 20px; padding: 3px 10px; font-size: 0.75rem; text-decoration: none; }

    @media (max-width: 991.98px) {
      .navbar-collapse { background: #fff; border-top: 1px solid #fde8ef; margin-top: 8px; padding: 8px 4px 12px; border-radius: 0 0 12px 12px; }
      .search-wrap input { width: 100%; }
      .search-wrap input:focus { width: 100%; }
      .search-wrap { width: 100%; margin-bottom: 8px; }
      .navbar-hoa .nav-link { border-radius: 8px; margin: 1px 0; }
    }
  </style>
</head>

<body class="bg-light">

  <nav class="navbar navbar-hoa navbar-expand-lg fixed-top">
    <div class="container">


   <a class="navbar-brand d-flex flex-column align-items-center" href="index.php" style="gap: 4px; text-decoration: none; margin-right: 15px;">
  <div style="width: 65px; height: 65px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px;">
    <img src="logo/logo1.png" alt="Hoa Vô Sắc" style="width: 200%; height: 400%; object-fit: contain; mix-blend-mode: multiply;">
  </div>
 
</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="index.php"><i class="bi bi-house-door"></i> Trang chủ</a></li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-flower2"></i> Sản phẩm
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <?php if ($categories && mysqli_num_rows($categories) > 0): ?>
                  <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <li>
                      <a class="dropdown-item" href="categories.php?id=<?= $cat['id'] ?>">
                        <i class="bi bi-flower2"></i> <?= htmlspecialchars($cat['name']) ?>
                      </a>
                    </li>
                  <?php endwhile; ?> <?php else: ?>
                  <li><a class="dropdown-item disabled text-muted">Chưa có danh mục</a></li>
              <?php endif; ?>
            </ul>
          </li>

          <li class="nav-item">
              <a class="nav-link" id="contactMenuBtn" href="#tu-van-hoa"><i class="bi bi-telephone"></i> Liên hệ</a>
          </li>
        </ul>

        <form action="search_results.php" method="GET" class="search-wrap me-2" id="searchForm" autocomplete="off">
          <input type="search" name="keyword" id="searchInput" placeholder="Tìm hoa..." required>
          <button class="search-btn" type="submit"><i class="bi bi-search"></i></button>
          <div id="searchSuggestions" class="search-suggestions-box d-none"></div>
        </form>

        <div class="dropdown me-2">
            <button class="nav-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
                <i class="bi bi-person-circle"></i>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="ms-1" style="font-size: 0.8rem; font-weight: 600; color: #4a3040;">
                        <?= htmlspecialchars($_SESSION['username'] ?? 'Thành viên') ?>
                    </span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</a></li>
                    <li><a class="dropdown-item" href="register.php"><i class="bi bi-person-plus"></i> Đăng ký</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="my_orders.php"><i class="bi bi-box-seam"></i> Đơn hàng của tôi</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person-badge"></i> Hồ sơ cá nhân</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
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
  </nav>

  <div id="mainBannerCarousel" class="carousel slide" data-bs-ride="carousel" style="background-color: #FCE7F3;">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#mainBannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#mainBannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    </div>

    <div class="carousel-inner text-center">
        <div class="carousel-item active py-5" data-bs-interval="4000"> 
            <div class="py-5">
                <h1 class="carousel-banner-title">Nơi Cảm Xúc Nở Hoa</h1>
                <p class="carousel-banner-sub">Mỗi Bó Hoa – Một Câu Chuyện Yêu Thương 💐 </p>
            </div>
        </div>
        <div class="carousel-item py-5" data-bs-interval="4000">
            <div class="py-5">
                <h1 class="carousel-banner-title">Hoa Thay Lời Yêu</h1>
                <p class="carousel-banner-sub">Trao nhau một đóa hoa, gửi nhau ngàn lời thương 🎀 </p>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span> 
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
        <span class="visually-hidden">Next</span>
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
    </div>
  </div>

  <div id="tu-van-hoa" class="container my-5 py-5 bg-light rounded-4 shadow-sm">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 bg-light p-4 p-md-5 rounded-4">
                <div class="text-center mb-4">
                    <h2 class="text-center fw-bold mb-4" style="color: #a50920;">Đăng Ký Nhận Tư Vấn Hoa</h2>
                </div>
                
                <form id="tuVanForm" action="xacnhanyeucau.html" method="GET" onsubmit="return validateForm(event)" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control py-2" id="txtHoTen" name="txtHoTen" placeholder="Ví dụ: Nguyễn Văn A">
                            <div id="errorHoTen" class="text-danger small mt-1" style="display:none; font-weight: 500;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Số Điện Thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control py-2" id="txtDienThoai" name="txtDienThoai" placeholder="Ví dụ: 0912345xxx">
                            <div id="errorDienThoai" class="text-danger small mt-1" style="display:none; font-weight: 500;"></div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="reset" class="btn btn-outline-secondary px-4 rounded-pill" onclick="clearErrors()">Nhập Lại</button>
                        <button type="submit" class="btn btn-danger px-4 rounded-pill shadow-sm" style="background-color: #a50920; border: none;">Gửi Yêu Cầu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>

  <footer class="text-white-50 py-5 mt-5" style="background-color: #1a1616; border-top: 4px solid #db2777;">
    <div class="container text-md-start text-center">
        <div class="row g-4">
            
            <div class="col-md-4">
                <h5 class="text-white fw-bold mb-3" style="font-family: 'Playfair Display', serif; color: #db2777 !important;">
                    
                </h5>
                <p class="small lh-lg mb-3">
                    Nơi cảm xúc nở hoa — Chúng tôi tự hào mang đến những tác phẩm hoa nghệ thuật đầy mới mẻ và tinh tế để lưu giữ trọn vẹn từng khoảnh khắc yêu thương của bạn.
                </p>
                <div class="d-flex justify-content-md-start justify-content-center gap-3 fs-5">
                    <a href="#" class="text-white-50 text-hover-pink"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white-50 text-hover-pink"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white-50 text-hover-pink"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <h6 class="text-white fw-bold text-uppercase small mb-3" style="letter-spacing: 1px;">Hỗ Trợ</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2">
                    <li><a href="#" class="text-white-50 text-decoration-none text-hover-pink">Chính sách đổi trả bảo hành</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none text-hover-pink">Hướng dẫn thanh toán</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none text-hover-pink">Cam kết hoa tươi mới</a></li>
                </ul>
            </div>

            <div class="col-md-5">
                <h6 class="text-white fw-bold text-uppercase small mb-3" style="letter-spacing: 1px;">Liên Hệ</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-start">
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-map-marker-alt mt-1 text-pink"></i>
                        <span>123 Đường Cách Mạng Tháng Tám, Quận Bình Thủy, Cần Thơ</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="fas fa-phone-alt text-pink"></i>
                        <span>Hotline: 0981.8794.98</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="fas fa-envelope text-pink"></i>
                        <span>contact@hoavosac.vn</span>
                    </li>
                </ul>
            </div>

        </div>

        <hr class="border-secondary my-4 opacity-25">

        <div class="row align-items-center small">
            <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                © 2026 <span class="text-white fw-semibold">𝓗𝓸𝓪 𝓥𝓸 𝓢𝓪𝓬</span>. Toàn bộ quyền được bảo lưu.
            </div>
        </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/validate.js"></script>
  <script src="js/load_xml.js"></script>

  <script>
    // FORM VALIDATION TƯ VẤN
    function validateForm(event) {
        event.preventDefault();

        const hoTen = document.getElementById('txtHoTen').value.trim();
        const dienThoai = document.getElementById('txtDienThoai').value.trim();

        const errorHoTen = document.getElementById('errorHoTen');
        const errorDienThoai = document.getElementById('errorDienThoai');

        let isValid = true;

        if (hoTen === "") {
            errorHoTen.innerText = "Bạn chưa nhập Họ và Tên!";
            errorHoTen.style.display = "block";
            document.getElementById('txtHoTen').classList.add('is-invalid');
            isValid = false;
        } else {
            errorHoTen.style.display = "none";
            document.getElementById('txtHoTen').classList.remove('is-invalid');
        }

        const phoneRegex = /^[0-9]{10,11}$/;
        if (dienThoai === "") {
            errorDienThoai.innerText = "Bạn chưa nhập Số Điện Thoại!";
            errorDienThoai.style.display = "block";
            document.getElementById('txtDienThoai').classList.add('is-invalid');
            isValid = false;
        } else if (!phoneRegex.test(dienThoai)) {
            errorDienThoai.innerText = "Số điện thoại không hợp lệ (Phải là chuỗi số từ 10-11 ký tự)!";
            errorDienThoai.style.display = "block";
            document.getElementById('txtDienThoai').classList.add('is-invalid');
            isValid = false;
        } else {
            errorDienThoai.style.display = "none";
            document.getElementById('txtDienThoai').classList.remove('is-invalid');
        }

        if (isValid) {
            window.location.href = document.getElementById('tuVanForm').getAttribute('action');
        }
        
        return isValid;
    }

    function clearErrors() {
        document.getElementById('errorHoTen').style.display = "none";
        document.getElementById('errorDienThoai').style.display = "none";
        document.getElementById('txtHoTen').classList.remove('is-invalid');
        document.getElementById('txtDienThoai').classList.remove('is-invalid');
    }

    // ĐỢI DOM SẴN SÀNG ĐỂ CHẠY CÁC TÍNH NĂNG KHÁC
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Khởi tạo Carousel Banner
        var myCarouselEl = document.querySelector('#mainBannerCarousel');
        if (myCarouselEl) {
            new bootstrap.Carousel(myCarouselEl, {
                interval: 4000,
                ride: 'carousel',
                wrap: true
            });
        }

        // 2. Click nút liên hệ cuộn êm xuống Form
        const contactBtn = document.getElementById("contactMenuBtn");
        if (contactBtn) {
            contactBtn.addEventListener("click", function(event) {
                event.preventDefault();
                const targetSection = document.getElementById("tu-van-hoa");
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                }
            });
        }

        // 3. Xử lý Thêm Giỏ Hàng bằng AJAX ngầm
        document.querySelectorAll('.btn-add-to-cart-ajax').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');

                fetch(url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
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

        // 4. Tìm kiếm gợi ý (Live Search Suggestion)
        const searchInput = document.getElementById("searchInput");
        const suggestionsBox = document.getElementById("searchSuggestions");
        const searchForm = document.getElementById("searchForm");

        if(searchInput && suggestionsBox) {
            searchInput.addEventListener("input", function () {
                let keyword = this.value.trim();

                if (keyword.length >= 1) {
                    fetch(`ajax_search.php?keyword=${encodeURIComponent(keyword)}`)
                        .then(response => response.text())
                        .then(html => {
                            suggestionsBox.innerHTML = html;
                            suggestionsBox.classList.remove("d-none");
                        });
                } else {
                    suggestionsBox.classList.add("d-none");
                }
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
        }
    });
  </script>
</body>
</html>