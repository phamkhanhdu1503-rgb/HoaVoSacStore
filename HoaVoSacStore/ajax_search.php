<?php
require 'config/database.php';

if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = trim($_GET['keyword']);
    $search_term = "%" . $keyword . "%";

    // SQL sử dụng LIKE sẽ tự động không phân biệt chữ hoa/chữ thường (Case-insensitive)
    $stmt = $db->prepare("SELECT id, name FROM products WHERE name LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="suggestion-item" data-name="' . htmlspecialchars($row['name']) . '">' 
                 . htmlspecialchars($row['name']) . 
                 '</div>';
        }
    } else {
        echo '<div class="suggestion-item text-muted">Không tìm thấy hoa phù hợp...</div>';
    }
}
?>