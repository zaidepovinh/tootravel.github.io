<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (isset($_GET['q'])) {
    try {
        $db = new Database();
        $search = '%' . $_GET['q'] . '%';
        
        $sql = "
            SELECT p.id, p.title, p.excerpt, p.image, p.published_at, p.views,
                   u.username AS author_name, u.avatar AS author_avatar,
                   c.name AS category_name,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND is_visible = 1) AS comment_count
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published' 
            AND (p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)
            ORDER BY p.published_at DESC
        ";
        
        $db->query($sql);
        $db->bind(1, $search);
        $db->bind(2, $search);
        $db->bind(3, $search);
        
        $results = $db->getAll();
        
        echo json_encode(['success' => true, 'data' => $results]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Đã xảy ra lỗi khi tìm kiếm']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Thiếu tham số tìm kiếm']);
?>