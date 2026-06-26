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
    <link rel="stylesheet" href="../style/cart.css">
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

                            <?php while ($row = $result->fetch_assoc()):
                                $sub = $row['price'] * $row['quantity'];
                                $total += $sub;
                                $img = !empty($row['image']) ? $row['image'] : 'default.png';
                                ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <img src="../uploads/<?= htmlspecialchars($img) ?>" alt=""
                                            style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;"
                                            class="me-2">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td class="fw-semibold text-secondary">
                                        <?= number_format($row['price']) ?>₫
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="update_cart.php?id=<?= $row['cart_id'] ?>&type=dec"
                                                class="btn btn-qty-adj">
                                                <i class="bi bi-dash"></i>
                                            </a>
                                            <span class="fw-bold text-dark px-2" style="min-width: 30px;">
                                                <?= $row['quantity'] ?>
                                            </span>
                                            <a href="update_cart.php?id=<?= $row['cart_id'] ?>&type=inc"
                                                class="btn btn-qty-adj">
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
                                <td colspan="5" class="pt-4 border-0">

                                    <form method="POST" action="checkout.php">

                                        <!-- PHẦN CHỌN PHƯƠNG THỨC MỚI: MƯỢT MÀ & ĐỒNG BỘ -->
                                        <div class="payment-section d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 text-start">
                                            <div>
                                                <label class="form-label fw-bold text-dark m-0 d-block mb-1">
                                                    <i class="bi bi-shield-check text-success me-1"></i> Phương thức thanh toán
                                                </label>
                                                <small class="text-muted">Vui lòng chọn hình thức giao dịch phù hợp với bạn</small>
                                            </div>

                                            <!-- Thẻ ẩn để lưu giá trị gửi lên file checkout.php -->
                                            <input type="hidden" name="payment_method" id="payment_method_input" value="cod">

                                            <!-- Cụm Dropdown Custom của Bootstrap 5 -->
                                            <div class="dropdown">
                                                <button class="btn select-payment-custom dropdown-toggle d-flex align-items-center gap-2" 
                                                        type="button" 
                                                        id="paymentDropdown" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span id="selected-payment-text">💵 Thanh toán khi nhận hàng (COD)</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-payment-menu dropdown-menu-end" aria-labelledby="paymentDropdown">
                                                    <li>
                                                        <a class="dropdown-item active" href="#" data-value="cod" onclick="selectPayment(this)">
                                                            💵 Thanh toán khi nhận hàng (COD)
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-value="bank" onclick="selectPayment(this)">
                                                            🏦 Chuyển khoản ngân hàng
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end gap-3">

                                            <a href="../index.php"
                                                class="btn btn-continue-shop d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-arrow-left"></i> Tiếp tục mua hàng
                                            </a>

                                            <button type="submit"
                                                class="btn btn-checkout-now d-inline-flex align-items-center gap-2"
                                                onclick="return confirm('Xác nhận đặt hàng?')">
                                                <i class="bi bi-credit-card-2-front-fill"></i>
                                                Tiến Hành Đặt Hàng
                                            </button>

                                        </div>

                                    </form>

                                </td>
                            </tr>

                        <?php } else { ?>

                            <tr>
                                <td colspan="5" class="border-0">
                                    <div class="alert alert-custom-empty text-center m-0" role="alert">
                                        <i class="bi bi-cart-x fs-1 d-block mb-3" style="color: #ff758f;"></i>
                                        <span class="fw-bold d-block mb-1 fs-5 text-dark">Giỏ hàng của bạn đang trống</span>
                                        <p class="small text-muted m-0 mb-4">Hãy ghé thăm cửa hàng hoa và chọn cho mình
                                            những sản phẩm tươi thắm nhất nhé!</p>
                                        <a href="../index.php" class="btn btn-continue-shop rounded-pill px-4 py-2"
                                            style="border-color: #fbd0dd; color: #c0405a;">
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
                                <img src="../uploads/<?= htmlspecialchars($img_rec) ?>"
                                    alt="<?= htmlspecialchars($prod['name']) ?>">
                            </div>

                            <div class="card-body-hoa">
                                <a href="../product_detail.php?id=<?= $prod['id'] ?>" class="product-title">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>

                                <div class="product-price">
                                    <?= number_format($prod['price']) ?>₫
                                </div>

                                <div class="action-row">
                                    <a href="add_to_cart.php?id=<?= $prod['id'] ?>" class="btn-circle-action"
                                        title="Thêm nhanh vào giỏ">
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
    
    <!-- SCRIPT ĐỔI GIÁ TRỊ VÀ CLASS ACTIVE CHO DROPDOWN CUSTOM -->
    <script>
    function selectPayment(element) {
        event.preventDefault();
        
        const val = element.getAttribute('data-value');
        const text = element.innerText;
        
        document.getElementById('payment_method_input').value = val;
        document.getElementById('selected-payment-text').innerText = text;
        
        document.querySelectorAll('.dropdown-payment-menu .dropdown-item').forEach(item => {
            item.classList.remove('active');
        });
        element.classList.add('active');
    }
    </script>
</body>

</html>