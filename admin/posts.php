<?php
require_once '../config/database.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = new Database();

// Xử lý các action từ AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $postId = (int)($_POST['post_id'] ?? 0);
    
    switch ($action) {
        case 'delete':
            $db->query("DELETE FROM posts WHERE id = ?");
            $db->bind(1, $postId);
            echo json_encode(['success' => $db->execute()]);
            exit;
            
        case 'update_status':
            $status = $_POST['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'published', 'hidden'])) {
                $status = 'draft';
            }
            
            $db->query("UPDATE posts SET status = ? WHERE id = ?");
            $db->bind(1, $status);
            $db->bind(2, $postId);
            echo json_encode(['success' => $db->execute()]);
            exit;
            
        case 'save':
            try {
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $category_id = (int)($_POST['category'] ?? 0);
                $status = $_POST['status'] ?? 'draft';
                $postId = (int)($_POST['post_id'] ?? 0);
                
                if (empty($title) || empty($content)) {
                    throw new Exception('Vui lòng điền đầy đủ thông tin');
                }

                // Upload thumbnail nếu có
                $imagePath = '';
                if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../uploads/thumbnails/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $filename = uniqid() . '_' . $_FILES['thumbnail']['name'];
                    $uploadFile = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadFile)) {
                        $imagePath = 'uploads/thumbnails/' . $filename;
                    }
                }
                
                if ($postId > 0) {
                    // Update existing post
                    $sql = "UPDATE posts SET 
                            title = ?, 
                            content = ?, 
                            excerpt = ?, 
                            category_id = ?, 
                            status = ?";
            
                    $params = [$title, $content, $excerpt, $category_id, $status];
            
                    if ($imagePath) {
                        $sql .= ", thumbnail = ?, image = ?";
                        $params[] = $imagePath;
                        $params[] = $imagePath; // Thêm cùng đường dẫn cho image
                    }
            
                    $sql .= " WHERE id = ?";
                    $params[] = $postId;
            
                } else {
                    // Create new post
                    $sql = "INSERT INTO posts (title, content, excerpt, category_id, status, author_id, thumbnail, image) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $params = [
                        $title, 
                        $content, 
                        $excerpt, 
                        $category_id, 
                        $status, 
                        $_SESSION['user_id'], 
                        $imagePath ?: 'images/Default-thumbnail.jpg',
                        $imagePath ?: 'images/Default-thumbnail.jpg' // Sử dụng cùng đường dẫn cho image
                    ];
                }
                
                $db->query($sql);
                foreach ($params as $index => $param) {
                    $db->bind($index + 1, $param);
                }
                
                $success = $db->execute();
                echo json_encode(['success' => $success]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
    }
}

// Cấu hình phân trang
$posts_per_page = 6; // Số bài viết trên mỗi trang
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Đếm tổng số bài viết
$db->query("SELECT COUNT(*) as total FROM posts");
$total_posts = $db->getOne()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Kiểm tra trang hiện tại hợp lệ
if ($current_page > $total_pages) {
    $current_page = $total_pages;
} elseif ($current_page < 1) {
    $current_page = 1;
}

// Sửa câu query để thêm LIMIT và OFFSET
$db->query("
    SELECT p.*, u.username as author_name, c.name as category_name 
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.published_at DESC
    LIMIT ? OFFSET ?
");
$db->bind(1, $posts_per_page);
$db->bind(2, $offset);
$posts = $db->getAll();

// Lấy thông tin admin đang đăng nhập
$db->query("SELECT * FROM users WHERE id = ?");
$db->bind(1, $_SESSION['user_id']);
$currentUser = $db->getOne();

// Categories for select dropdown
$db->query("SELECT id, name FROM categories ORDER BY name");
$categories = $db->getAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài viết - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <!-- Include CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
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
                    <li class="active">
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
                    <h2>Quản lý bài viết</h2>
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

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
                        <i class="bi bi-plus-circle"></i> Thêm bài viết
                    </button>
                </div>
            </div>

            <!-- Posts List -->
            <div class="admin-content">
                <div class="admin-card">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề</th>
                                    <th>Tóm tắt</th> 
                                    <th>Danh mục</th>
                                    <th>Tác giả</th>
                                    <th>Trạng thái</th>
                                    <th>Lượt xem</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?= $post['id'] ?></td>
                                    <td><?= htmlspecialchars($post['title']) ?></td>
                                    <td><?= htmlspecialchars(substr($post['excerpt'] ?? '', 0, 100)) ?>...</td> <!-- Thêm dòng này -->
                                    <td><?= htmlspecialchars($post['category_name'] ?? 'Không có danh mục') ?></td>
                                    <td><?= htmlspecialchars($post['author_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $post['status'] === 'published' ? 'success' : 
                                            ($post['status'] === 'draft' ? 'warning' : 'secondary') 
                                        ?>">
                                            <?= ucfirst($post['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($post['views']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($post['published_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-post" 
                                                data-id="<?= $post['id'] ?>"
                                                data-title="<?= htmlspecialchars($post['title']) ?>"
                                                data-content="<?= htmlspecialchars($post['content']) ?>"
                                                data-excerpt="<?= htmlspecialchars($post['excerpt']) ?>" 
                                                data-category="<?= $post['category_id'] ?>"
                                                data-status="<?= $post['status'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-post" data-id="<?= $post['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Posts pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        // Hiển thị tối đa 5 trang
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        if ($start_page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                            if ($start_page > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor;

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                        }
                        ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </main>
    </div>

    <!-- Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm/Sửa bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="postForm" enctype="multipart/form-data" onsubmit="return false;">
                        <input type="hidden" id="postId" name="post_id">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Tóm tắt bài viết</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                      placeholder="Nhập tóm tắt ngắn gọn về bài viết..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung</label>
                            <textarea id="content" name="content"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Ảnh thumbnail</label>
                            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft">Bản nháp</option>
                                <option value="published">Xuất bản</option>
                                <option value="hidden">Ẩn</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="savePost">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let editor;
        const postModal = new bootstrap.Modal(document.getElementById('postModal'));
        
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#content'))
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });
        
        // Edit post handler
        document.querySelectorAll('.edit-post').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.id;
                const title = this.dataset.title;
                const content = this.dataset.content;
                const excerpt = this.dataset.excerpt;  
                const category = this.dataset.category;
                const status = this.dataset.status;
                
                document.getElementById('postId').value = postId;
                document.getElementById('title').value = title;
                document.getElementById('excerpt').value = excerpt; 
                editor.setData(content);
                document.getElementById('category').value = category;
                document.getElementById('status').value = status;
                
                postModal.show();
            });
        });
        
        // Save post handler
        document.getElementById('savePost').addEventListener('click', function() {
            const form = document.getElementById('postForm');
            const formData = new FormData(form);
            
            // Kiểm tra dữ liệu
            if (!formData.get('title')) {
                alert('Vui lòng nhập tiêu đề');
                return;
            }
            
            // Lấy nội dung từ CKEditor và thêm vào formData
            const content = editor.getData();
            if (!content) {
                alert('Vui lòng nhập nội dung');
                return;
            }
            
            // Xóa content cũ và thêm content mới từ CKEditor
            formData.delete('content');
            formData.append('content', content);
            formData.append('action', 'save');

            // Thêm status nếu chưa có
            if (!formData.has('status')) {
                formData.append('status', document.getElementById('status').value);
            }
            
            // Thêm loading state
            const saveButton = document.getElementById('savePost');
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';
            
            fetch('posts.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Giữ nguyên trang hiện tại khi reload
                    const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                    location.href = `?page=${currentPage}`;
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Lưu';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi lưu bài viết');
                saveButton.disabled = false;
                saveButton.innerHTML = 'Lưu';
            });
        });
        
        // Delete post handler
        document.querySelectorAll('.delete-post').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Bạn có chắc chắn muốn xóa bài viết này?')) {
                    const postId = this.dataset.id;
                    
                    fetch('posts.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&post_id=${postId}`
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