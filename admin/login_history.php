<?php
session_start();
require "../config/database.php";

// Lấy danh sách lịch sử đăng nhập từ database
$sql = "SELECT * FROM login_log ORDER BY login_at DESC";
$result = $db->query($sql);
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lịch Sử Đăng Nhập - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="../style/login_history.css">
</head>

<body>

<?php include '../sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid p-0">

        <div class="mb-5">
            <h2 class="fw-bold text-dark m-0">Nhật Ký & Lịch Sử Đăng Nhập</h2>
            <p class="text-muted small m-0 mt-1">
                Giám sát các phiên truy cập hệ thống Hoa Vô Sắc
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tài khoản</th>
                        <th>IP</th>
                        <th>User Agent</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $stt = 1; ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $stt++ ?></td>

                            <td class="fw-bold">
                                <?= htmlspecialchars($row['username']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['ip_address'] ?? '-') ?>
                            </td>

                            <td style="max-width: 300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <?= htmlspecialchars($row['user_agent'] ?? '-') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['login_at']) ?>
                            </td>

                            <td>
                                <?php if ($row['status'] === 'success'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Success
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle-fill me-1"></i> Failed
                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Chưa có lịch sử đăng nhập nào
                        </td>
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