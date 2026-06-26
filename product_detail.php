<?php
require 'config/auth.php';
require 'config/database.php';

// =============================
// CHECK ID
// =============================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID sản phẩm!");
}

$id = (int) $_GET['id'];

// =============================
// LẤY SẢN PHẨM
// =============================
$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($db, $sql);

$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Không tìm thấy sản phẩm!");
}

// ảnh mặc định nếu rỗng
$img = !empty($product['image']) ? $product['image'] : 'default.png';
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/product_detail.css">


</head>

<body>

    <div class="container py-5" style="max-width: 1150px;">

        <div class="mb-4 small">
            <a href="index.php" class="text-decoration-none" style="color: #ff758f; font-weight: 500;">Cửa hàng</a>
            <span class="text-muted mx-2">/</span>
            <span class="text-secondary">Chi tiết sản phẩm hoa</span>
        </div>

        <div class="row g-4">

            <div class="col-lg-5 col-md-6">
                <div class="img-container">
                    <img src="uploads/<?= htmlspecialchars($img) ?>" class="product-img"
                        alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>

            <div class="col-lg-7 col-md-6">
                <div class="info-card h-100 d-flex flex-column justify-content-between">

                    <div>
                        <h2 class="fw-bold text-dark mb-3" style="font-size: 28px; letter-spacing: -0.5px;">
                            <?= htmlspecialchars($product['name']) ?>
                        </h2>

                        <div class="mb-3 d-flex align-items-center gap-2">
                            <span class="stock-badge">
                                <i class="bi bi-box-seam me-1"></i> Tồn kho: <?= $product['stock'] ?> sản phẩm
                            </span>
                            <span class="text-muted small"><i class="bi bi-shield-check text-success"></i> Đã kiểm định
                                tươi mới</span>
                        </div>

                        <div class="price-box mb-4">
                            <span class="price-text"><?= number_format($product['price']) ?>₫</span>
                        </div>

                        <ul class="nav nav-tabs nav-tabs-custom border-bottom mb-3" id="productTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="desc-tab" data-bs-toggle="tab"
                                    data-bs-target="#desc-pane" type="button" role="tab">Mô tả sản phẩm</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="policy-tab" data-bs-toggle="tab"
                                    data-bs-target="#policy-pane" type="button" role="tab">Chính sách giao hoa</button>
                            </li>
                        </ul>

                        <div class="tab-content pb-3" id="productTabContent">
                            <div class="tab-pane fade show active" id="desc-pane" role="tabpanel" tabindex="0">
                                <p class="text-muted lh-base m-0" style="font-size: 14px; text-align: justify;">
                                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                                </p>
                            </div>
                            <div class="tab-pane fade" id="policy-pane" role="tabpanel" tabindex="0">
                                <ul class="text-muted small lh-lg ps-3 m-0">
                                    <li>Cam kết hoa tươi kéo dài trên 3 ngày trong điều kiện phòng bình thường.</li>
                                    <li>Miễn phí thiệp chúc mừng, băng rôn thiết kế riêng theo yêu cầu.</li>
                                    <li>Chụp hình sản phẩm hoàn thiện gửi khách duyệt trước khi giao tận nơi.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <form action="carts/add_to_cart.php" method="GET">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">

                            <div class="d-flex align-items-center gap-3 mb-4">
                                <span class="text-secondary small fw-bold">Số lượng:</span>
                                <div class="qty-input-group d-flex align-items-center">
                                    <button type="button" class="qty-btn" onclick="adjustQty(-1)"><i
                                            class="bi bi-dash"></i></button>
                                    <input type="text" name="quantity" id="quantity-input" class="qty-val" value="1"
                                        readonly>
                                    <button type="button" class="qty-btn" onclick="adjustQty(1)"><i
                                            class="bi bi-plus"></i></button>
                                </div>
                            </div>

                            <div class="d-flex gap-3 flex-wrap pt-2">
                                <button type="submit"
                                    class="btn btn-add-cart-lg d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-cart-plus-fill fs-5"></i> Thêm vào giỏ hàng
                                </button>

                                <a href="index.php" class="btn btn-back-shop d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-arrow-left"></i> Quay lại cửa hàng
                                </a>
                            </div>
                        </form>

                        <div class="policy-box">
                            <div class="row g-3">
                                <div class="col-6 col-md-4">
                                    <div class="policy-item">
                                        <i class="bi bi-flower2"></i>
                                        <span>Hoa tươi 100% trong ngày</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="policy-item">
                                        <i class="bi bi-truck"></i>
                                        <span>Giao hỏa tốc trong 2h</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="policy-item">
                                        <i class="bi bi-patch-check"></i>
                                        <span>Đổi trả nếu hoa héo úa</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logic tăng giảm số lượng an toàn bằng Javascript
        function adjustQty(amount) {
            const input = document.getElementById('quantity-input');
            let current = parseInt(input.value) || 1;
            current += amount;
            if (current < 1) current = 1;

            // Giới hạn không vượt quá số lượng tồn kho thực tế nếu có
            const maxStock = <?= (int) $product['stock'] ?>;
            if (current > maxStock) current = maxStock;

            input.value = current;
        }
    </script>
</body>

</html>