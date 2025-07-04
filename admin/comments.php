<?php
require_once '../config/database.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = new Database();

// Xử lý thay đổi trạng thái bình luận
if (isset($_POST['action']) && isset($_POST['comment_id'])) {
    $commentId = (int)$_POST['comment_id'];
    $action = $_POST['action'];
    
    if ($action === 'toggle_visibility') {
        $db->query("UPDATE comments SET is_visible = NOT is_visible WHERE id = ?");
        $db->bind(1, $commentId);
        if ($db->execute()) {
            echo json_encode(['success' => true]);
            exit;
        }
    } else if ($action === 'delete') {
        $db->query("DELETE FROM comments WHERE id = ?");
        $db->bind(1, $commentId);
        if ($db->execute()) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    echo json_encode(['success' => false]);
    exit;
}

// Lấy danh sách bình luận
$sql = "
    SELECT c.*, u.username, p.title as post_title 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
";
$db->query($sql);
$comments = $db->getAll();

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
    <title>Quản lý bình luận - Admin</title>
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
                    <li>
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
                    <li class="active">
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
                    <h2>Quản lý bình luận</h2>
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

            <!-- Comment Management Content -->
            <div class="admin-content">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h5>Danh sách bình luận</h5>
                    </div>
                    <div class="admin-card-body">
                        <table class="table table-striped admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nội dung</th>
                                    <th>Người dùng</th>
                                    <th>Bài viết</th>
                                    <th>Ngày đăng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comments as $comment): ?>
                                <tr>
                                    <td><?= $comment['id'] ?></td>
                                    <td><?= htmlspecialchars(substr($comment['content'], 0, 100)) ?>...</td>
                                    <td><?= htmlspecialchars($comment['username'] ?? 'Ẩn danh') ?></td>
                                    <td><?= htmlspecialchars($comment['post_title']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></td>
                                    <td>
                                        <span class="badge <?= $comment['is_visible'] ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $comment['is_visible'] ? 'Hiển thị' : 'Đã ẩn' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm <?= $comment['is_visible'] ? 'btn-warning' : 'btn-success' ?> toggle-visibility" 
                                                data-id="<?= $comment['id'] ?>">
                                            <i class="bi <?= $comment['is_visible'] ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-comment" data-id="<?= $comment['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý ẩn/hiện bình luận
        document.querySelectorAll('.toggle-visibility').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.id;
                fetch('comments.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=toggle_visibility&comment_id=${commentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        });

        // Xử lý xóa bình luận
        document.querySelectorAll('.delete-comment').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                    const commentId = this.dataset.id;
                    fetch('comments.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&comment_id=${commentId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
                }
            });
        });
    });
    </script>
</body>
</html>