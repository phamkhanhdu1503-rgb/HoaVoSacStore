<?php
require __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

mysqli_set_charset($db, "utf8mb4");

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {

    $keyword = trim($_GET['keyword']);
    $search = "%" . $keyword . "%";

    $stmt = $db->prepare("
        SELECT id, name, image, price
        FROM products
        WHERE name LIKE ?
        ORDER BY name
        LIMIT 5
    ");

    if ($stmt) {

        $stmt->bind_param("s", $search);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                $image = !empty($row['image'])
                    ? "uploads/" . $row['image']
                    : "uploads/default.png";

                ?>

                <a href="product_detail.php?id=<?= $row['id'] ?>" class="suggestion-item">

                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="suggestion-img">

                    <div class="suggestion-info">

                        <div class="suggestion-name">
                            <?= htmlspecialchars($row['name']) ?>
                        </div>

                        <div class="suggestion-price">
                            <?= number_format($row['price']) ?>₫
                        </div>

                    </div>

                </a>

                <?php
            }

        } else {

            echo '
            <div class="suggestion-item-empty">
                Không tìm thấy sản phẩm phù hợp.
            </div>';

        }

        $stmt->close();

    } else {

        echo '
        <div class="suggestion-item-empty">
            Không thể tải dữ liệu.
        </div>';

    }

}

$db->close();