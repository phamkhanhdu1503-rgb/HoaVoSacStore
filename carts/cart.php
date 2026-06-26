<?php
require '../config/flash.php';
require '../config/auth.php';
require '../config/database.php';

$user_id = $_SESSION['user_id'];

// ============================
// LẤY GIỎ HÀNG
// ============================
$sql = "
SELECT
    c.id AS cart_id,
    c.quantity,
    p.id AS product_id,
    p.name,
    p.price,
    p.image
FROM carts c
JOIN products p ON c.product_id = p.id
WHERE c.user_id = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
$total_qty = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
    $total_qty += $row['quantity'];
}

// ============================
// LẤY SẢN PHẨM ĐỀ XUẤT (Ngẫu nhiên 4 sản phẩm)
// ============================
$recommend_stmt = $db->prepare("
    SELECT * FROM products 
    ORDER BY RAND() 
    LIMIT 4
");
$recommend_stmt->execute();
$recommended_products = $recommend_stmt->get_result();
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #fff8f9;
            font-family: 'Quicksand', sans-serif;
            color: #4a3040;
        }

        /* Thẻ bao quanh từng sản phẩm trong giỏ (Thay cho Table) */
        .cart-item-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(255, 179, 193, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .cart-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 117, 143, 0.1);
        }

        .cart-item-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid #ffeef1;
        }

        /* Cột bên phải - Bảng tóm tắt hóa đơn chốt giá */
        .summary-card {
            background: #ffffff;
            border: none;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(255, 179, 193, 0.06);
            padding: 30px;
            position: sticky;
            top: 30px;
        }

        /* Tinh chỉnh nút tăng giảm số lượng hình tròn */
        .btn-qty-adj {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
            border: 1px solid #fbd0dd;
            color: #ff758f;
            background-color: #ffffff;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-qty-adj:hover {
            background-color: #ff758f;
            border-color: #ff758f;
            color: #ffffff;
        }

        /* Nút Tiến hành đặt hàng dạng dải màu đồng bộ */
        .btn-checkout-now {
            background: linear-gradient(135deg, #ff758f 0%, #ff4d6d 100%);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            padding: 14px 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(255, 117, 143, 0.2);
            text-decoration: none;
            transition: all 0.25s ease;
            width: 100%;
        }
        .btn-checkout-now:hover {
            background: linear-gradient(135deg, #ff4d6d 0%, #c9184a 100%);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(255, 117, 143, 0.3);
        }

        .btn-continue-shop {
            background-color: #fffbfb;
            color: #8a3a4b;
            font-weight: 600;
            border: 1px solid #f8e9ec;
            border-radius: 50px;
            padding: 12px 24px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
        }
        .btn-continue-shop:hover {
            background-color: #fff0f2;
            color: #ff758f;
            border-color: #ffb3c1;
        }

        .btn-action-delete {
            background-color: #fff0f2;
            color: #dc3545;
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-action-delete:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        .alert-custom-empty {
            background-color: #ffffff;
            border: 1px dashed #fbd0dd;
            color: #4a3040;
            border-radius: 24px;
            padding: 60px 30px;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.02);
        }

        .price-text {
            font-weight: 700;
            color: #a81c39;
        }

        .badge-cart-count {
            background-color: #ffccd5;
            color: #8a3a4b;
            font-size: 14px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            vertical-align: middle;
        }

        /* ===== DANH SÁCH ĐỀ XUẤT SẢN PHẨM ===== */
        .section-recommend-title {
            font-weight: 700;
            color: #4a3040;
            margin-top: 65px;
            margin-bottom: 25px;
        }
        .section-recommend-title span {
            color: #ff758f;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #fde8ef;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 117, 143, 0.12);
        }
        .product-card .img-container {
            width: 100%;
            height: 240px;
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
            transform: scale(1.05);
        }
        .product-card .card-body-hoa {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: center;
        }
        .product-card .product-title {
            font-size: 14px;
            font-weight: 600;
            color: #4a3040;
            margin-bottom: 8px;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.4rem;
            line-height: 1.2rem;
        }
        .product-card .product-title:hover {
            color: #ff758f;
        }
        .product-card .product-price {
            font-size: 16px;
            font-weight: 700;
            color: #a81c39;
            margin-bottom: 15px;
        }
        .product-card .action-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
        }
        .product-card .btn-circle-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #ffeef1;
            background: #fffbfb;
            color: #ff758f;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
        }
        .product-card .btn-circle-action:hover {
            background: #ff758f;
            color: #fff;
        }
        .product-card .btn-outline-hoa {
            flex: 1;
            height: 36px;
            border: 1px solid #ffccd5;
            background: #fff;
            color: #8a3a4b;
            font-size: 13px;
            font-weight: 600;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        .product-card .btn-outline-hoa:hover {
            background: #fff0f2;
            color: #ff758f;
            border-color: #ffb3c1;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <div class="mb-5 d-flex align-items-center gap-3">
            <div>
                <h2 class="fw-bold text-dark m-0">
                    🛒 Giỏ Hàng Của Tôi 
                    <?php if ($total_qty > 0): ?>
                        <span class="badge-cart-count ms-1"><?= $total_qty ?></span>
                    <?php endif; ?>
                </h2>
                <p class="text-muted small m-0 mt-1">Xem lại danh sách những bông hoa bạn đã chọn trước khi thanh toán</p>
            </div>
        </div>

        <?php if (!empty($cart_items)) { ?>
            
            <div class="row g-4">
                
                <div class="col-xl-8 col-lg-7">
                    <div class="d-flex flex-column gap-3">
                        
                        <?php foreach ($cart_items as $item): 
                            $sub = $item['price'] * $item['quantity'];
                            $img = !empty($item['image']) ? $item['image'] : 'default.png';
                        ?>
                            <div class="card cart-item-card p-3">
                                <div class="row g-3 align-items-center">
                                    
                                    <div class="col-auto">
                                        <img src="../uploads/<?= htmlspecialchars($img) ?>" class="cart-item-img" alt="">
                                    </div>
                                    
                                    <div class="col">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 250px;">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </h6>
                                        <span class="text-muted small"><?= number_format($item['price']) ?>₫ / sản phẩm</span>
                                    </div>
                                    
                                    <div class="col-auto px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="update_cart.php?id=<?= $item['cart_id'] ?>&type=dec" class="btn btn-qty-adj">
                                                <i class="bi bi-dash"></i>
                                            </a>
                                            <span class="fw-bold text-dark text-center" style="min-width: 24px;">
                                                <?= $item['quantity'] ?>
                                            </span>
                                            <a href="update_cart.php?id=<?= $item['cart_id'] ?>&type=inc" class="btn btn-qty-adj">
                                                <i class="bi bi-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="col-auto text-end px-3" style="min-width: 110px;">
                                        <span class="price-text fs-6"><?= number_format($sub) ?>₫</span>
                                    </div>
                                    
                                    <div class="col-auto text-center">
                                        <a href="remove_cart.php?id=<?= $item['cart_id'] ?>" 
                                           class="btn btn-action-delete"
                                           onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card summary-card">
                        <h5 class="fw-bold text-dark mb-4">Tóm tắt đơn hàng</h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px dashed #f8e9ec;">
                            <span class="text-secondary small">Tổng số lượng:</span>
                            <span class="fw-bold text-dark"><?= $total_qty ?> gói hoa</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-secondary small">Tổng tiền thanh toán:</span>
                            <span class="price-text fs-4"><?= number_format($total) ?>₫</span>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <a href="checkout.php" class="btn btn-checkout-now"
                               onclick="return confirm('Xác nhận tiến hành đặt hàng đơn này?')">
                                <i class="bi bi-credit-card-2-front-fill fs-5"></i> Tiến Hành Đặt Hàng
                            </a>
                            
                            <a href="../index.php" class="btn btn-continue-shop">
                                <i class="bi bi-arrow-left"></i> Tiếp tục mua hoa
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        <?php } else { ?>
            
            <div class="alert alert-custom-empty text-center" role="alert">
                <i class="bi bi-basket3 fs-1 d-block mb-3" style="color: #ff758f;"></i>
                <span class="fw-bold d-block mb-2 fs-5 text-dark">Giỏ hàng của bạn đang trống</span>
                <p class="small text-muted m-0 mb-4 mx-auto" style="max-width: 400px;">Hãy ghé thăm không gian cửa hàng và lựa chọn cho mình những bó hoa tươi tắn, ngát hương nhất nhé!</p>
                <a href="../index.php" class="btn btn-checkout-now rounded-pill px-5 d-inline-flex w-auto">
                    Khám phá sản phẩm ngay <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

        <?php } ?>

        <h3 class="section-recommend-title">
            🌸 Có thể bạn sẽ thích <span>những bông hoa này...</span>
        </h3>

        <div class="row g-4">
            <?php if ($recommended_products && $recommended_products->num_rows > 0) { ?>
                <?php while ($prod = $recommended_products->fetch_assoc()) { 
                    $img_rec = !empty($prod['image']) ? $prod['image'] : 'default.png';
                ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <div class="img-container">
                                <img src="../uploads/<?= htmlspecialchars($img_rec) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                            </div>

                            <div class="card-body-hoa">
                                <a href="../product_detail.php?id=<?= $prod['id'] ?>" class="product-title">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>

                                <div class="product-price">
                                    <?= number_format($prod['price']) ?>₫
                                </div>

                                <div class="action-row">
                                    <a href="add_to_cart.php?id=<?= $prod['id'] ?>" class="btn-circle-action" title="Thêm nhanh vào giỏ">
                                        <i class="bi bi-cart-plus"></i>
                                    </a>
                                    <a href="../product_detail.php?id=<?= $prod['id'] ?>" class="btn-outline-hoa">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12 text-center text-muted small py-4">Đang tìm những mẫu hoa phù hợp với bạn...</div>
            <?php } ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>