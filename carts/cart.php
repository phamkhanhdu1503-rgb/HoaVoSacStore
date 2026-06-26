<?php
require '../config/auth.php';
require '../config/database.php';
session_start();

// ============================
// CHECK LOGIN
// ============================
if (!isset($_SESSION['user_id'])) {
    die("Vui lòng đăng nhập!");
}

$user_id = $_SESSION['user_id'];

// ============================
// LẤY GIỎ HÀNG
// ============================
$sql = "
SELECT
    c.id AS cart_id,
    c.quantity,
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
$total = 0;

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

    <style>
        /* ===== FONT & NỀN ĐỒNG BỘ ===== */
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            background: #fff8f9;
            font-family: 'Quicksand', sans-serif;
        }

        /* Thẻ Card lớn bọc ngoài bảng giỏ hàng */
        .table-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.04);
            padding: 30px;
            overflow: hidden;
        }

        /* Tinh chỉnh bảng Table phẳng, bo góc mềm mại */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            border: none;
        }
        .table-custom thead th {
            background-color: #fde8ef !important; 
            color: #c0405a !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 16px;
            border: none;
        }
        .table-custom tbody td {
            padding: 16px;
            vertical-align: middle;
            color: #4a3040;
            font-size: 14px;
            border-bottom: 1px solid #fde8ef;
        }
        .table-custom tbody tr.total-row td {
            background-color: #fffbfb;
            border-bottom: 1px solid #fbd0dd;
        }
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .table-custom tbody tr:hover td {
            background-color: #fff5f7 !important;
        }

        /* Bo góc cho phần đầu bảng */
        .table-custom thead tr th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .table-custom thead tr th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        /* Các nút tăng/giảm số lượng vòng tròn tinh tế */
        .btn-qty-adj {
            width: 28px;
            height: 28px;
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
            background-color: #c0405a;
            border-color: #c0405a;
            color: #ffffff;
        }

        /* Nút hành động viên thuốc mềm mại */
        .btn-checkout-now {
            background-color: #c0405a;
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 10px 24px;
            box-shadow: 0 4px 12px rgba(192, 64, 90, 0.2);
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .btn-checkout-now:hover {
            background-color: #a81c39;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(192, 64, 90, 0.3);
        }

        .btn-continue-shop {
            background-color: #ffffff;
            color: #4a3040;
            font-weight: 600;
            border: 1px solid #fbd0dd;
            border-radius: 50px;
            padding: 10px 24px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-continue-shop:hover {
            background-color: #fde8ef;
            color: #c0405a;
        }

        .btn-action-delete {
            background-color: #fff0f2;
            color: #dc3545;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-action-delete:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        /* Khung thông báo trống pastel */
        .alert-custom-empty {
            background-color: #ffffff;
            border: 1px dashed #fbd0dd;
            color: #4a3040;
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 10px 30px rgba(255, 105, 135, 0.03);
        }

        .price-text {
            font-weight: 700;
            color: #a81c39;
        }

        /* ===== ĐỀ XUẤT SẢN PHẨM STYLE HOA VÔ SẮC ===== */
        .section-recommend-title {
            font-weight: 700;
            color: #4a3040;
            position: relative;
            margin-top: 60px;
            margin-bottom: 25px;
        }
        .section-recommend-title span {
            color: #c0405a;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #fde8ef;
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
    </style>
</head>

<body>

    <div class="container py-5">

        <div class="mb-5">
            <h2 class="fw-bold text-dark m-0">🛒 Giỏ Hàng Của Tôi</h2>
            <p class="text-muted small m-0 mt-1">Quản lý danh sách các gói hoa tươi chuẩn bị tiến hành đặt hàng</p>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-custom table-hover m-0">

                    <thead>
                        <tr>
                            <th>Sản phẩm hoa</th>
                            <th style="width: 150px;">Giá bán</th>
                            <th style="width: 160px;" class="text-center">Số lượng</th>
                            <th style="width: 180px;">Thành tiền</th>
                            <th style="width: 120px;" class="text-center">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (mysqli_num_rows($result) > 0) { ?>

                            <?php while ($row = $result->fetch_assoc()) :
                                $sub = $row['price'] * $row['quantity'];
                                $total += $sub;
                                $img = !empty($row['image']) ? $row['image'] : 'default.png';
                            ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <img src="../uploads/<?= htmlspecialchars($img) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;" class="me-2">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td class="fw-semibold text-secondary">
                                        <?= number_format($row['price']) ?>₫
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="update_cart.php?id=<?= $row['cart_id'] ?>&type=dec" class="btn btn-qty-adj">
                                                <i class="bi bi-dash"></i>
                                            </a>
                                            <span class="fw-bold text-dark px-2" style="min-width: 30px;">
                                                <?= $row['quantity'] ?>
                                            </span>
                                            <a href="update_cart.php?id=<?= $row['cart_id'] ?>&type=inc" class="btn btn-qty-adj">
                                                <i class="bi bi-plus"></i>
                                            </a>
                                        </div>
                                    </td>

                                    <td class="price-text">
                                        <?= number_format($sub) ?>₫
                                    </td>

                                    <td class="text-center">
                                        <a href="remove_cart.php?id=<?= $row['cart_id'] ?>" 
                                           class="btn btn-action-delete d-inline-flex align-items-center gap-1"
                                           onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                            <i class="bi bi-trash3-fill"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <tr class="total-row">
                                <td colspan="3" class="text-end fw-bold text-dark fs-5 py-3">
                                    Tổng giá trị thanh toán:
                                </td>
                                <td colspan="2" class="price-text fs-4 py-3" style="color: #c0405a !important;">
                                    <?= number_format($total) ?>₫
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5" class="text-end pt-4 border-0">
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="../index.php" class="btn btn-continue-shop d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-arrow-left"></i> Tiếp tục mua hàng
                                        </a>

                                        <a href="checkout.php" class="btn btn-checkout-now d-inline-flex align-items-center gap-2"
                                           onclick="return confirm('Xác nhận tiến hành đặt hàng đơn này?')">
                                            <i class="bi bi-credit-card-2-front-fill"></i> Tiến Hành Đặt Hàng
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php } else { ?>

                            <tr>
                                <td colspan="5" class="border-0">
                                    <div class="alert alert-custom-empty text-center m-0" role="alert">
                                        <i class="bi bi-cart-x fs-1 d-block mb-3" style="color: #ff758f;"></i>
                                        <span class="fw-bold d-block mb-1 fs-5 text-dark">Giỏ hàng của bạn đang trống</span>
                                        <p class="small text-muted m-0 mb-4">Hãy ghé thăm cửa hàng hoa và chọn cho mình những sản phẩm tươi thắm nhất nhé!</p>
                                        <a href="../index.php" class="btn btn-continue-shop rounded-pill px-4 py-2" style="border-color: #fbd0dd; color: #c0405a;">
                                            <i class="bi bi-arrow-left"></i> Khám phá cửa hàng ngay
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="section-recommend-title">
            🌸 Thêm vào giỏ <span>những bông hoa tươi thắm...</span>
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
                <div class="col-12 text-center text-muted small">Đang tìm những mẫu hoa phù hợp với bạn...</div>
            <?php } ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>