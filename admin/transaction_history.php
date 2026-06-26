<?php
session_start();
require "../config/database.php";
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lịch Sử Giao Dịch - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="../style/transaction_history.css">
    <style>
        /* CSS bổ sung để hiển thị danh sách ảnh sản phẩm dạng hàng ngang mini */
        .product-item-mini {
            display: inline-flex;
            align-items: center;
            background: #fff0f2;
            padding: 4px 10px;
            border-radius: 50px;
            margin: 2px;
            border: 1px solid #ffe5ec;
        }
        .product-img-mini {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 6px;
            border: 1px solid #ff758f;
        }
        .product-list-container {
            max-width: 380px;
            max-height: 80px;
            overflow-y: auto;
        }
    </style>
</head>

<body>

    <?php include '../sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="mb-5">
                <h2 class="fw-bold text-dark m-0">Quản Lý Lịch Sử Giao Dịch</h2>
                <p class="text-muted small m-0 mt-1">Theo dõi các hoạt động nạp tiền, thanh toán hóa đơn của khách hàng trên hệ thống</p>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Mã Giao Dịch</th>
                            <th>Khách Hàng</th>
                            <th>Sản Phẩm Đã Đặt</th>
                            <th>Số Tiền</th>
                            <th>Thời Gian</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Lấy danh sách đơn hàng
                        $sql = "SELECT o.id, u.fullname, o.total, o.created_at 
                                FROM orders o
                                JOIN users u ON o.user_id = u.id
                                ORDER BY o.created_at DESC";

                        $result = $db->query($sql);

                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                $order_id = $row['id'];
                        ?>
                        <tr>
                            <td class="fw-bold text-brand">#HVS<?= $order_id ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td>
                                <div class="product-list-container">
                                    <?php
                                    // Truy vấn lấy chi tiết từng sản phẩm bao gồm cả TÊN, SỐ LƯỢNG và ẢNH của đơn hàng này
                                    $sp_sql = "SELECT p.name, p.image, od.quantity 
                                               FROM order_details od
                                               JOIN products p ON od.product_id = p.id
                                               WHERE od.order_id = $order_id";
                                    $sp_result = $db->query($sp_sql);
                                    
                                    if ($sp_result && $sp_result->num_rows > 0):
                                        while ($sp = $sp_result->fetch_assoc()):
                                            // Đường dẫn ảnh sản phẩm (bạn điều chỉnh lại thư mục chứa ảnh ../uploads/ hoặc ../images/ cho đúng cấu trúc của bạn nhé)
                                            $img_path = "../uploads/" . $sp['image'];
                                            // Nếu file ảnh trống hoặc không tồn tại thì lấy ảnh mặc định tạm thời
                                            if (empty($sp['image'])) {
                                                $img_path = "../images/default-flower.png";
                                            }
                                    ?>
                                        <div class="product-item-mini" title="<?= htmlspecialchars($sp['name']) ?> (x<?= $sp['quantity'] ?>)">
                                            <img src="<?= htmlspecialchars($img_path) ?>" class="product-img-mini" alt="flower">
                                            <span class="small text-dark fw-semibold"><?= htmlspecialchars($sp['name']) ?> <span class="text-brand">(x<?= $sp['quantity'] ?>)</span></span>
                                        </div>
                                    <?php 
                                        endwhile;
                                    else:
                                        echo '<span class="text-muted small">Không rõ sản phẩm</span>';
                                    endif; 
                                    ?>
                                </div>
                            </td>
                            <td class="fw-bold text-success">+<?= number_format($row['total']) ?> đ</td>
                            <td><?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?></td>
                            <td><span class="badge badge-status status-completed">Thành công</span></td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có lịch sử giao dịch nào trong hệ thống.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $db->close(); ?>