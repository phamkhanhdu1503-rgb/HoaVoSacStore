<?php
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

    <style>
        /* ===== FONT & NỀN ĐỒNG BỘ ===== */
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            background: #fff8f9;
            font-family: 'Quicksand', sans-serif;
        }

        /* Khung chứa ảnh sản phẩm */
        .img-container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.04);
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 20px;
        }

        .product-img {
            width: 100%;
            height: auto;
            max-height: 480px;
            object-fit: cover;
            border-radius: 16px;
        }

        /* Khung chứa thông tin chi tiết */
        .info-card {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.04);
            padding: 40px;
        }

        /* Giá bán nổi bật */
        .price-box {
            background: #fff5f7;
            padding: 15px 25px;
            border-radius: 12px;
            display: inline-block;
        }
        .price-text {
            font-size: 32px;
            font-weight: 800;
            color: #a81c39;
        }

        /* Badge số lượng tồn kho */
        .stock-badge {
            background-color: #fde8ef;
            color: #c0405a;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            display: inline-block;
        }

        /* Bộ tăng giảm số lượng tinh tế */
        .qty-input-group {
            max-width: 130px;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            overflow: hidden;
            background: #fff;
        }
        .qty-btn {
            background: none;
            border: none;
            width: 35px;
            height: 38px;
            color: #c0405a;
            font-weight: bold;
            transition: background 0.2s;
        }
        .qty-btn:hover {
            background: #fde8ef;
        }
        .qty-val {
            border: none;
            width: 50px;
            text-align: center;
            font-weight: 700;
            color: #4a3040;
        }
        .qty-val:focus {
            outline: none;
        }

        /* Nút hành động lớn */
        .btn-add-cart-lg {
            background-color: #c0405a;
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 12px 35px;
            box-shadow: 0 4px 12px rgba(192, 64, 90, 0.2);
            transition: all 0.25s ease;
        }
        .btn-add-cart-lg:hover {
            background-color: #a81c39;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(192, 64, 90, 0.3);
        }

        .btn-back-shop {
            background-color: #ffffff;
            color: #4a3040;
            font-weight: 600;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            padding: 12px 28px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-back-shop:hover {
            background-color: #fde8ef;
            color: #c0405a;
        }

        /* Custom Tabs thông tin */
        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 12px 20px;
            position: relative;
        }
        .nav-tabs-custom .nav-link.active {
            color: #c0405a;
            background: none;
        }
        .nav-tabs-custom .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 3px;
            background: #c0405a;
            border-radius: 10px;
        }

        /* Khối cam kết dịch vụ */
        .policy-box {
            border-top: 1px dashed #fbd0dd;
            padding-top: 25px;
            margin-top: 30px;
        }
        .policy-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4a3040;
        }
        .policy-item i {
            font-size: 20px;
            color: #ff758f;
        }

        hr {
            border-top: 1px solid #fde8ef;
            opacity: 1;
        }
    </style>
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
                <img src="uploads/<?= htmlspecialchars($img) ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
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
                        <span class="text-muted small"><i class="bi bi-shield-check text-success"></i> Đã kiểm định tươi mới</span>
                    </div>

                    <div class="price-box mb-4">
                        <span class="price-text"><?= number_format($product['price']) ?>₫</span>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom border-bottom mb-3" id="productTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-pane" type="button" role="tab">Mô tả sản phẩm</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy-pane" type="button" role="tab">Chính sách giao hoa</button>
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
                                <button type="button" class="qty-btn" onclick="adjustQty(-1)"><i class="bi bi-dash"></i></button>
                                <input type="text" name="quantity" id="quantity-input" class="qty-val" value="1" readonly>
                                <button type="button" class="qty-btn" onclick="adjustQty(1)"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>

                        <div class="d-flex gap-3 flex-wrap pt-2">
                            <button type="submit" class="btn btn-add-cart-lg d-inline-flex align-items-center gap-2">
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
        const maxStock = <?= (int)$product['stock'] ?>;
        if (current > maxStock) current = maxStock;

        input.value = current;
    }
</script>
</body>
</html>