<?php
require __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = trim($_GET['keyword']);
    $search_term = "%" . $keyword . "%";

    mysqli_set_charset($db, 'utf8mb4');

    $stmt = $db->prepare("SELECT id, name FROM products WHERE name LIKE ? LIMIT 5");
    if ($stmt) {
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="suggestion-item" data-name="' . htmlspecialchars($row['name']) . '">'
                    . htmlspecialchars($row['name'])
                    . '</div>';
            }
        } else {
            echo '<div class="suggestion-item text-muted">Không tìm thấy hoa phù hợp...</div>';
        }
    } else {
        echo '<div class="suggestion-item text-muted">Không thể tải gợi ý lúc này.</div>';
    }
}
?>