<?php
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
    $category = isset($_GET['category']) ? $_GET['category'] : 'all';
    
    $sql = "
        SELECT p.id, p.title, p.excerpt, 
               COALESCE(p.thumbnail, 'images/Default-thumbnail.jpg') as image,
               p.published_at, p.views,
               u.username AS author_name, u.avatar AS author_avatar,
               c.name AS category_name,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND is_visible = 1) as comment_count
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'published'
    ";
    
    // Thêm điều kiện lọc theo danh mục
    if ($category !== 'all') {
        $sql .= " AND c.slug = :category";
    }
    
    // Thêm điều kiện sắp xếp
    switch ($sort) {
        case 'newest':
            $sql .= " ORDER BY p.published_at DESC";
            break;
        case 'oldest':
            $sql .= " ORDER BY p.published_at ASC";
            break;
        case 'most_viewed':
            $sql .= " ORDER BY p.views DESC";
            break;
        case 'least_viewed':
            $sql .= " ORDER BY p.views ASC";
            break;
        case 'most_commented':
            $sql .= " ORDER BY comment_count DESC";
            break;
        case 'least_commented':
            $sql .= " ORDER BY comment_count ASC";
            break;
        default:
            $sql .= " ORDER BY p.published_at DESC";
    }
    
    $db->query($sql);
    
    if ($category !== 'all') {
        $db->bind(':category', $category);
    }
    
    $results = $db->getAll();
    
    echo json_encode([
        'success' => true,
        'data' => $results
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Đã xảy ra lỗi khi sắp xếp bài viết: ' . $e->getMessage()
    ]);
}