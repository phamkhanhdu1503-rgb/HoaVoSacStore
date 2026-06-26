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
    <link rel="stylesheet" href="../style/search_results.css">


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
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Trang
                            chủ</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i
                                class="bi bi-flower2"></i> Sản phẩm</a>
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
                    <form action="search_results.php" method="GET" class="search-wrap" id="searchForm"
                        autocomplete="off">
                        <input type="search" name="keyword" id="searchInput" value="<?= htmlspecialchars($keyword) ?>"
                            placeholder="Tìm hoa..." required>
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
            <?php } else { ?>
                <div class="col-12">
                    <div class="alert alert-warning rounded-4 border-0 shadow-sm text-center py-5">
                        <i class="bi bi-search-heart fs-1 d-block mb-3 text-muted"></i>
                        Tiếc quá, "Hoa Vô Sắc" không tìm thấy bông hoa nào khớp với từ khóa của bạn rồi! Có thể thử từ khóa
                        khác xem sao nha.
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
                button.addEventListener('click', function (e) {
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