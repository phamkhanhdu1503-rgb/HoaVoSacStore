<?php
session_start();
require 'config/database.php';

// ============================
// NHẬN TỪ KHÓA TÌM KIẾM
// ============================
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (empty($keyword)) {
    header("Location: index.php");
    exit();
}

// ============================
// TÍNH TỔNG SỐ LƯỢNG GIỎ HÀNG TỪ DATABASE (Đồng bộ Navbar)
// ============================
$user_id = 1; 
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
// LẤY SẢN PHẨM THEO TỪ KHÓA TÌM KIẾM (Không phân biệt hoa thường)
// ============================
$search_term = "%" . $keyword . "%";
$stmt = $db->prepare("
    SELECT * FROM products 
    WHERE name LIKE ? 
    ORDER BY id DESC
");
$stmt->bind_param("s", $search_term);
$stmt->execute();
$products = $stmt->get_result();
?>

<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kết quả tìm kiếm: <?= htmlspecialchars($keyword) ?> – Hoa Vô Sắc</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    /* ===== FONT ===== */
    @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

    body {
      font-family: 'Quicksand', sans-serif;
      padding-top: 90px;
    }

    /* ===== NAVBAR WRAPPER ===== */
    .navbar-hoa {
      background: #fff;
      border-bottom: 2px solid #fde8ef;
      box-shadow: 0 2px 16px rgba(255, 105, 135, 0.08);
    }

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

    .navbar-hoa .dropdown-menu {
      border: none;
      border-radius: 12px;
      box-shadow: 0 8px 28px rgba(192, 64, 90, 0.13);
      padding: 6px;
      min-width: 170px;
      animation: dropIn 0.18s ease;
      z-index: 1050;
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

    /* ===== HỘP ĐỀ XUẤT TÌM KIẾM ===== */
    .search-suggestions-box {
      position: absolute; top: 110%; left: 0; width: 100%; background: #ffffff;
      border: 1px solid #fde8ef; border-radius: 12px; box-shadow: 0 8px 24px rgba(192, 64, 90, 0.1);
      max-height: 250px; overflow-y: auto; z-index: 1100;
    }
    .suggestion-item {
      padding: 10px 14px; font-size: 0.85rem; color: #4a3040; cursor: pointer;
      transition: background 0.15s; border-bottom: 1px solid #fff5f7; display: block; text-decoration: none;
    }
    .suggestion-item:hover { background: #fde8ef; color: #c0405a; }

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

    /* ===== TITLE HEADER ===== */
    .category-header {
      border-bottom: 2px dashed #fbd0dd;
      padding-bottom: 15px;
      margin-bottom: 30px;
    }
    .category-title {
      font-weight: 700;
      color: #4a3040;
    }
    .category-title span {
      color: #c0405a;
    }

    /* ===== PRODUCT CARD ===== */
    .product-card {
      background: #ffffff; border: 1px solid #f3f3f3; border-radius: 16px;
      overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex; flex-direction: column; height: 100%;
    }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(232, 116, 138, 0.12); }
    .product-card .img-container { position: relative; width: 100%; height: 260px; overflow: hidden; background: #fafafb; }
    .product-card .img-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover .img-container img { transform: scale(1.04); }
    .product-card .card-body-hoa { padding: 16px; display: flex; flex-direction: column; flex-grow: 1; text-align: center; }
    .product-card .product-title {
      font-size: 0.95rem; font-weight: 600; color: #3f6897; margin-bottom: 8px; text-decoration: none;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8rem; line-height: 1.4;
    }
    .product-card .product-title:hover { color: #c0405a; }
    .product-card .product-price { font-size: 1.1rem; font-weight: 700; color: #a81c39; margin-bottom: 15px; }
    
    .product-card .action-row { display: flex; align-items: center; gap: 6px; margin-top: auto; }
    .product-card .btn-circle-action {
      width: 36px; height: 36px; border-radius: 50%; border: 1px solid #e1e1e1; background: #fff;
      color: #555; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: all 0.2s; text-decoration: none; flex-shrink: 0;
    }
    .product-card .btn-circle-action:hover { background: #fde8ef; border-color: #fbd0dd; color: #c0405a; }
    .product-card .btn-outline-hoa {
      flex: 1; height: 36px; border: 1px solid #4a5568; background: #fff; color: #4a5568;
      font-size: 0.82rem; font-weight: 600; border-radius: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;
    }
    .product-card .btn-outline-hoa:hover { background: #f4f5f7; color: #222; }
    .product-card .btn-submit-hoa {
      flex: 1; height: 36px; background: #a81c39; color: #fff; font-size: 0.82rem; font-weight: 600;
      border-radius: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: background 0.2s; border: none;
    }
    .product-card .btn-submit-hoa:hover { background: #c0405a; color: #fff; }

    @media (max-width: 991.98px) {
      .navbar-collapse { background: #fff; border-top: 1px solid #fde8ef; margin-top: 8px; padding: 8px 4px 12px; border-radius: 0 0 12px 12px; }
      .search-wrap input { width: 100%; }
      .search-wrap input:focus { width: 100%; }
      .search-wrap { width: 100%; margin-bottom: 8px; }
      .navbar-actions { border-top: 1px solid #fde8ef; padding-top: 10px; margin-top: 6px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
      .product-card .img-container { height: 190px; }
    }
  </style>
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
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-flower2"></i> Sản phẩm</a>
            <ul class="dropdown-menu">
              <?php 
              mysqli_data_seek($categories, 0);
              while ($cat = mysqli_fetch_assoc($categories)) { 
              ?>
                <li>
                  <a class="dropdown-item" href="categories.php?id=<?= $cat['id'] ?>">
                    <i class="bi bi-flower2"></i> <?= htmlspecialchars($cat['name']) ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-newspaper"></i> Tin tức</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-telephone"></i> Liên hệ</a></li>
        </ul>

        <div class="navbar-actions d-flex align-items-center gap-2 flex-wrap">
          <form action="search_results.php" method="GET" class="search-wrap" id="searchForm" autocomplete="off">
            <input type="search" name="keyword" id="searchInput" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tìm hoa..." required>
            <button class="search-btn" type="submit"><i class="bi bi-search"></i></button>
            <div id="searchSuggestions" class="search-suggestions-box d-none"></div>
          </form>

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
        🔍 Kết quả tìm kiếm cho: <span>"<?= htmlspecialchars($keyword) ?>"</span>
      </h3>
      <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Về trang chủ
      </a>
    </div>

    <div class="row g-4">
        <?php if ($products && $products->num_rows > 0) { ?>
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
                                <a href="#" class="btn-circle-action"><i class="bi bi-dash-lg"></i></a>
                                
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
                <div class="alert alert-warning rounded-4 border-0 shadow-sm text-center py-5">
                     <i class="bi bi-search-heart fs-1 d-block mb-3 text-muted"></i>
                     Tiếc quá, "Hoa Vô Sắc" không tìm thấy bông hoa nào khớp với từ khóa của bạn rồi! Có thể thử từ khóa khác xem sao nha.
                </div>
            </div>
        <?php } ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
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

      // AJAX Thêm nhanh vào giỏ hàng
      document.querySelectorAll('.btn-add-to-cart-ajax').forEach(button => {
          button.addEventListener('click', function(e) {
              e.preventDefault();
              const url = this.getAttribute('data-url');

              fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      let badge = document.querySelector('.cart-badge');
                      let cartBtn = document.querySelector('.nav-icon-btn[title="Giỏ hàng"]');
                      if (badge) {
                          badge.textContent = (parseInt(badge.textContent) || 0) + 1;
                      } else if (cartBtn) {
                          cartBtn.insertAdjacentHTML('beforeend', '<span class="cart-badge">1</span>');
                      }
                      alert('Đã thêm sản phẩm vào giỏ hàng! ✨');
                  } else {
                      alert('Có lỗi xảy ra!');
                  }
              });
          });
      });
  });
  </script>
</body>
</html>