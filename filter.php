<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (isset($_GET['category'])) {
    try {
        $db = new Database();
        $category = $_GET['category'];
        
        $sql = "
            SELECT p.id, p.title, p.excerpt, p.image, p.published_at, p.views,
                   u.username AS author_name, u.avatar AS author_avatar,
                   c.name AS category_name,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND is_visible = 1) AS comment_count
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published'
        ";
        
        if ($category !== 'all') {
            $sql .= " AND c.slug = ?";
            $db->query($sql);
            $db->bind(1, $category);
        } else {
            $db->query($sql);
        }
        
        $results = $db->getAll();
        
        echo json_encode(['success' => true, 'data' => $results]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Đã xảy ra lỗi khi lọc bài viết']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Thiếu tham số lọc']);