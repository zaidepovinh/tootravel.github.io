<?php
require_once 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$success = '';
$error = '';

// Xử lý xóa bài viết
if (isset($_POST['delete_post'])) {
    $postId = (int)$_POST['post_id'];
    
    // Kiểm tra quyền sở hữu bài viết
    $db->query("SELECT id FROM posts WHERE id = ? AND author_id = ?");
    $db->bind(1, $postId);
    $db->bind(2, $_SESSION['user_id']);
    $post = $db->getOne();
    
    if ($post) {
        $db->query("DELETE FROM posts WHERE id = ?");
        $db->bind(1, $postId);
        if ($db->execute()) {
            $success = "Đã xóa bài viết thành công!";
        } else {
            $error = "Có lỗi xảy ra khi xóa bài viết!";
        }
    }
}

// Lấy danh sách bài viết của user
$sql = "
    SELECT p.*, c.name as category_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.author_id = ?
    ORDER BY p.created_at DESC
";
$db->query($sql);
$db->bind(1, $_SESSION['user_id']);
$posts = $db->getAll();

require_once 'header.php';
?>

<div class="container main-container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý bài viết của tôi</h2>
        <a href="post-editor.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Viết bài mới
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #ccc;"></i>
            </div>
            <h4>Bạn chưa có bài viết nào</h4>
            <p class="text-muted">Hãy bắt đầu chia sẻ trải nghiệm của bạn!</p>
            <a href="post-editor.php" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle"></i> Viết bài mới
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 post-card">
                        <?php if (!empty($post['thumbnail']) && file_exists($post['thumbnail'])): ?>
                            <img src="<?= htmlspecialchars($post['thumbnail']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($post['title']) ?>"
                                 style="height: 200px; object-fit: cover;"
                                 onerror="this.src='images/Default-thumbnail.jpg'">
                        <?php else: ?>
                            <img src="images/Default-thumbnail.jpg" 
                                 class="card-img-top"
                                 alt="Default thumbnail"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary"><?= htmlspecialchars($post['category_name']) ?></span>
                                <span class="badge bg-<?= $post['status'] === 'published' ? 'success' : 'warning' ?>">
                                    <?= $post['status'] === 'published' ? 'Đã đăng' : 'Bản nháp' ?>
                                </span>
                            </div>
                            
                            <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                            
                            <div class="text-muted small mb-3">
                                <i class="bi bi-calendar3"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                <i class="bi bi-eye ms-3"></i> <?= number_format($post['views']) ?> lượt xem
                                <i class="bi bi-chat-dots ms-3"></i> <?= $post['comment_count'] ?> bình luận
                            </div>
                            
                            <p class="card-text"><?= htmlspecialchars(substr($post['excerpt'] ?? '', 0, 150)) ?>...</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="btn-group">
                                    <a href="post-editor.php?id=<?= $post['id'] ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button type="submit" name="delete_post" 
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                                <a href="blog-detail.php?id=<?= $post['id'] ?>" 
                                   class="btn btn-link btn-sm">
                                    Xem bài viết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h4>Về TooTravel</h4>
                <p>Chúng tôi là nền tảng chia sẻ thông tin du lịch hàng đầu Việt Nam, mang đến cho bạn những trải nghiệm tuyệt vời.</p>
                <div class="social-links">
                    <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="youtube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                <h4>Khám Phá</h4>
                <ul class="footer-links">
                    <li><a href="gallery.php">Điểm đến</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="about.php">Về Chúng Tôi</a></li>
                    <li><a href="contact.php">Liên Hệ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                <h4>Liên Kết</h4>
                <ul class="footer-links">
                    <li><a href="about.php">Về chúng tôi</a></li>
                    <li><a href="about.php">Điều khoản</a></li>
                    <li><a href="about.php">Chính sách</a></li>
                    <li><a href="about.php">FAQs</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4">
                <h4>Liên Hệ</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="bi bi-geo-alt"></i>
                        <span>76 Hồ Tùng mậu, TP Hà nội</span>
                    </li>
                    <li>
                        <i class="bi bi-envelope"></i>
                        <span>TooTravel@gmail.com</span>
                    </li>
                    <li>
                        <i class="bi bi-phone"></i>
                        <span>0911034984</span>
                    </li>
                    <li>
                        <i class="bi bi-clock"></i>
                        <span>Thứ 2 - Chủ nhật: 9:00 - 18:00</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; 2024 TooTravel. All rights reserved.</p>
        </div>
    </div>
</footer>

 <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            AOS.init({
                duration: 1000,
                once: true
            });

            // Initialize other features
            initializeFeatures();
        });
    </script>
</body>
</html>
