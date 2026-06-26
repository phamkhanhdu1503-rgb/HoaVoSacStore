<?php
session_start();

require 'config/database.php';

// ============================
// TÍNH TỔNG GIỎ HÀNG
// ============================

$user_id = $_SESSION['user_id'] ?? 0;
$total_quantity = 0;

if ($user_id > 0) {

  $cart_count_query = $db->prepare("
        SELECT COALESCE(SUM(quantity),0) AS total
        FROM carts
        WHERE user_id = ?
    ");

  $cart_count_query->bind_param("i", $user_id);
  $cart_count_query->execute();

  $cart_count_result = $cart_count_query->get_result();
  $total_quantity = $cart_count_result->fetch_assoc()['total'];
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
  <link rel="stylesheet" href="style/index.css">
</head>

<body class="bg-light">

  <nav class="navbar navbar-hoa navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center navbar-brand-hoa" href="index.php">
        <img src="logo/logo.png" alt="Hoa Vô Sắc" class="logo-img">
        <span class="shop-name ms-2">Read Thư 𝓗𝓸𝓪 𝓥𝓸 𝓢𝓪𝓬</span>
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
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['avatar'])): ?>

              <img src="uploads/<?= htmlspecialchars($_SESSION['avatar']) ?>"
                style="width:32px;height:32px;border-radius:50%;object-fit:cover;">

            <?php else: ?>

              <i class="bi bi-person-circle"></i>

            <?php endif; ?>
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
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-badge"></i> Hồ sơ cá nhân</a></li>

              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li>
                  <hr class="dropdown-divider my-1">
                </li>
                <li>
                  <a class="dropdown-item fw-bold text-brand" href="admin/dashboard.php" style="color: #ff758f;">
                    <i class="bi bi-speedometer2"></i> Quản lý Dashboard
                  </a>
                </li>
              <?php endif; ?>

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

          <form id="tuVanForm" action="submit_consult.php" method="POST" onsubmit="return validateForm(event)"
            novalidate>

            <div class="row g-3">

              <div class="col-md-6">

                <label class="form-label small fw-bold text-secondary">
                  Họ và Tên
                  <span class="text-danger">*</span>
                </label>

                <input type="text" class="form-control py-2" id="txtHoTen" name="txtHoTen"
                  placeholder="Ví dụ: Nguyễn Văn A">

                <div id="errorHoTen" class="text-danger small mt-1" style="display:none;font-weight:500;">
                </div>

              </div>

              <div class="col-md-6">

                <label class="form-label small fw-bold text-secondary">
                  Số Điện Thoại
                  <span class="text-danger">*</span>
                </label>

                <input type="text" class="form-control py-2" id="txtDienThoai" name="txtDienThoai"
                  placeholder="Ví dụ: 0912345678">

                <div id="errorDienThoai" class="text-danger small mt-1" style="display:none;font-weight:500;">
                </div>

              </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

              <button type="reset" class="btn btn-outline-secondary px-4 rounded-pill" onclick="clearErrors()">

                Nhập Lại

              </button>

              <button type="submit" class="btn btn-danger px-4 rounded-pill shadow-sm"
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