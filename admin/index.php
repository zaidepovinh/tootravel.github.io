<?php
require_once '../config/database.php';
// session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = new Database();

// Lấy thống kê tổng quan
$stats = [
    'posts' => 0,
    'comments' => 0,
    'users' => 0,
    'views' => 0
];

// Đếm tổng số bài viết
$db->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
$result = $db->getOne();
$stats['posts'] = $result['total'];

// Đếm tổng số bình luận
$db->query("SELECT COUNT(*) as total FROM comments WHERE is_visible = 1");
$result = $db->getOne();
$stats['comments'] = $result['total'];

// Đếm tổng số người dùng
$db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$result = $db->getOne();
$stats['users'] = $result['total'];

// Tổng lượt xem
$db->query("SELECT SUM(views) as total FROM posts");
$result = $db->getOne();
$stats['views'] = $result['total'] ?? 0;

// Lấy 5 bài viết mới nhất
$db->query("
    SELECT p.*, u.username as author_name 
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.status = 'published'
    ORDER BY p.published_at DESC 
    LIMIT 5
");
$recentPosts = $db->getAll();

// Lấy 5 bình luận mới nhất
$db->query("
    SELECT c.*, u.username, p.title as post_title
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
    LIMIT 5
");
$recentComments = $db->getAll();

// Lấy thông tin user đăng nhập
$db->query("SELECT * FROM users WHERE id = ?");
$db->bind(1, $_SESSION['user_id']);
$currentUser = $db->getOne();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel Blog VIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h3>Travel Blog VIP</h3>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="index.php">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="posts.php">
                            <i class="bi bi-file-earmark-text"></i>
                            Quản lý bài viết
                        </a>
                    </li>
                    <li>
                        <a href="comments.php">
                            <i class="bi bi-chat-dots"></i>
                            Quản lý bình luận
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <i class="bi bi-people"></i>
                            Quản lý người dùng
                        </a>
                    </li>
                    <li>
                        <a href="../index.php" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>
                            Xem trang web
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <div class="admin-topbar">
                <div class="topbar-left">
                    <h2>Dashboard</h2>
                </div>
                <div class="topbar-right">
                    <div class="admin-user-menu">
                        <img src="<?= htmlspecialchars($currentUser['avatar'] ? '../' . $currentUser['avatar'] : '../images/default-avatar.jpg') ?>" 
                             alt="<?= htmlspecialchars($currentUser['username']) ?>" 
                             class="admin-avatar">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" id="userMenuButton" data-bs-toggle="dropdown">
                                <span class="admin-username"><?= htmlspecialchars($currentUser['username']) ?></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../profile.php"><i class="bi bi-person"></i> Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="../myposts.php"><i class="bi bi-gear"></i> Bài viết của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="../logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="admin-card stats-card">
                            <div class="admin-card-body">
                                <div class="stats-icon bg-primary">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <div class="stats-details">
                                    <h3><?= number_format($stats['posts']) ?></h3>
                                    <p>Bài viết</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card stats-card">
                            <div class="admin-card-body">
                                <div class="stats-icon bg-success">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <div class="stats-details">
                                    <h3><?= number_format($stats['comments']) ?></h3>
                                    <p>Bình luận</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card stats-card">
                            <div class="admin-card-body">
                                <div class="stats-icon bg-warning">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stats-details">
                                    <h3><?= number_format($stats['users']) ?></h3>
                                    <p>Người dùng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card stats-card">
                            <div class="admin-card-body">
                                <div class="stats-icon bg-info">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <div class="stats-details">
                                    <h3><?= number_format($stats['views']) ?></h3>
                                    <p>Lượt xem</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Posts and Comments -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <h5>Bài viết mới nhất</h5>
                                <a href="posts.php" class="btn btn-sm btn-primary">
                                    Xem tất cả
                                </a>
                            </div>
                            <div class="admin-card-body">
                                <div class="recent-list">
                                    <?php foreach ($recentPosts as $post): ?>
                                        <div class="recent-item">
                                            <div class="recent-content">
                                                <h6><?= htmlspecialchars($post['title']) ?></h6>
                                                <div class="recent-meta">
                                                    <span><i class="bi bi-person"></i> <?= htmlspecialchars($post['author_name']) ?></span>
                                                    <span><i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($post['published_at'])) ?></span>
                                                    <span><i class="bi bi-eye"></i> <?= number_format($post['views']) ?></span>
                                                </div>
                                            </div>
                                            <a href="posts.php?action=edit&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <h5>Bình luận mới nhất</h5>
                                <a href="comments.php" class="btn btn-sm btn-primary">
                                    Xem tất cả
                                </a>
                            </div>
                            <div class="admin-card-body">
                                <div class="recent-list">
                                    <?php foreach ($recentComments as $comment): ?>
                                        <div class="recent-item">
                                            <div class="recent-content">
                                                <p class="comment-text"><?= htmlspecialchars(substr($comment['content'], 0, 100)) ?>...</p>
                                                <div class="recent-meta">
                                                    <span><i class="bi bi-person"></i> <?= htmlspecialchars($comment['username'] ?? 'Ẩn danh') ?></span>
                                                    <span><i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($comment['created_at'])) ?></span>
                                                    <span><i class="bi bi-file-text"></i> <?= htmlspecialchars($comment['post_title']) ?></span>
                                                </div>
                                            </div>
                                            <div class="comment-actions">
                                                <?php if ($comment['is_visible']): ?>
                                                    <button class="btn btn-sm btn-warning">
                                                        <i class="bi bi-eye-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>