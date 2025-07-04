<?php
require_once 'config/database.php';
$db = new Database();

// Lấy ID bài viết từ URL
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Xử lý gửi bình luận
$commentError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment-content'])) {
    $name = trim($_POST['comment-name']);
    $email = trim($_POST['comment-email']);
    $content = trim($_POST['comment-content']);
    $userId = null;
    // Nếu có đăng nhập, lấy userId từ session/localStorage (tùy hệ thống)
    // $userId = $_SESSION['user_id'] ?? null;

    if (!$name || !$email || !$content) {
        $commentError = 'Vui lòng nhập đầy đủ thông tin bình luận!';
    } else {
        $db->query("INSERT INTO comments (post_id, user_id, guest_name, guest_email, content, is_visible, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $db->bind(1, $postId, 'i');
        $db->bind(2, $userId, 'i');
        $db->bind(3, $name, 's');
        $db->bind(4, $email, 's');
        $db->bind(5, $content, 's');
        if ($db->execute()) {
            header("Location: blog-detail.php?id=" . $postId . "#comments-list");
            exit;
        } else {
            $commentError = 'Có lỗi xảy ra, vui lòng thử lại!';
        }
    }
}

// Lấy thông tin bài viết, tác giả, danh mục
$sql = "
    SELECT p.*, u.username as author_name, u.avatar as author_avatar,
           c.name as category_name
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.status = 'published'
    LIMIT 1
";
$db->query($sql);
$db->bind(1, $postId, 'i');
$post = $db->getOne();

if (!$post) {
    header("Location: blog.php");
    exit;
}

// Tăng lượt xem
$db->query("UPDATE posts SET views = views + 1 WHERE id = ?");
$db->bind(1, $postId, 'i');
$db->execute();

// Lấy bình luận cho bài viết (bao gồm cả guest)
$sqlComments = "
    SELECT c.*, u.username, u.avatar
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ? AND c.is_visible = 1
    ORDER BY c.created_at DESC
";
$db->query($sqlComments);
$db->bind(1, $postId, 'i');
$comments = $db->getAll();
$commentCount = count($comments);

// Lấy 3 bài viết phổ biến (trừ bài hiện tại)
$sqlPopular = "
    SELECT id, title, image, published_at, views
    FROM posts
    WHERE status = 'published' AND id != ?
    ORDER BY views DESC
    LIMIT 3
";
$db->query($sqlPopular);
$db->bind(1, $postId, 'i');
$popularPosts = $db->getAll();

// Lấy 3 bài viết liên quan cùng danh mục (trừ bài hiện tại)
$sqlRelated = "
    SELECT id, title, image, published_at
    FROM posts
    WHERE status = 'published' AND category_id = ? AND id != ?
    ORDER BY published_at DESC
    LIMIT 3
";
$db->query($sqlRelated);
$db->bind(1, $post['category_id'], 'i');
$db->bind(2, $postId, 'i');
$relatedPosts = $db->getAll();

require_once 'header.php';
?>

<!-- Blog Hero Section -->
<section class="post-hero">
    <div class="container">
        <div class="post-hero__content">
            <span class="post-hero__label">Chi tiết bài viết</span>
            <h1 id="blog-title"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="post-meta">
                <span class="post-author" id="blog-author"><?= htmlspecialchars($post['author_name']) ?></span>
                <span class="post-date" id="blog-date"><?= date('d/m/Y', strtotime($post['published_at'])) ?></span>
                <span class="post-views" id="blog-views"><?= number_format($post['views'] + 1) ?> lượt xem</span>
                <span class="post-comments" id="blog-comments"><?= $commentCount ?> bình luận</span>
            </div>
        </div>
    </div>
</section>

<!-- Blog Detail Section -->
<section class="post-detail py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Reading Progress Bar -->
                <div class="reading-progress">
                    <div class="reading-progress-bar"></div>
                </div>

                <!-- Blog Content -->
                <article class="post-content scroll-reveal">
                    <div class="post-image">
                        <?php if (!empty($post['thumbnail'])): ?>
                            <img src="<?= htmlspecialchars($post['thumbnail']) ?>"
                                alt="<?= htmlspecialchars($post['title']) ?>"
                                class="img-fluid"
                                onerror="this.src='images/default-thumbnail.jpg'">
                        <?php else: ?>
                            <img src="images/default-thumbnail.jpg"
                                alt="Default thumbnail"
                                class="img-fluid">
                        <?php endif; ?>
                    </div>
                    <div class="post-body">
                        <!-- Nội dung bài viết -->
                        <?= $post ? $post['content'] : '' ?>
                    </div>
                </article>

                <!-- Author Info -->
                <div class="author-box scroll-reveal">
                    <div class="author-box__inner">
                        <div class="author-avatar">
                            <img id="author-avatar" src="<?= htmlspecialchars($post['author_avatar']) ?>" alt="">
                        </div>
                        <div class="author-info">
                            <h3 class="author-name" id="author-name"><?= htmlspecialchars($post['author_name']) ?></h3>
                            <p class="author-bio">Nhà báo chuyên về văn hóa và du lịch, với hơn 10 năm kinh nghiệm viết về di sản văn hóa Việt Nam.</p>
                            <div class="author-social">
                                <a href="#" class="social-link facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="social-link twitter">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="#" class="social-link instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="post-comments-section scroll-reveal">
                    <div class="comments-header">
                        <h3 class="section-title">Bình luận (<span id="comments-count"><?= $commentCount ?></span>)</h3>
                        <p class="comments-subtitle">Hãy chia sẻ ý kiến của bạn về bài viết</p>
                    </div>
                    <div class="comments-list" id="comments-list">
                        <?php if ($commentCount == 0): ?>
                            <div class="text-center">
                                <i class="bi bi-chat-dots" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comments as $c): ?>
                                <div class="comment">
                                    <div class="comment__content">
                                        <div class="comment__author">
                                            <?= htmlspecialchars($c['username'] ?: ($c['guest_name'] ?: 'Ẩn danh')) ?>
                                        </div>
                                        <div class="comment__date">
                                            <i class="bi bi-clock"></i> <?= date('d/m/Y', strtotime($c['created_at'])) ?>
                                        </div>
                                        <div class="comment__text"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <!-- Form bình luận -->
                    <div class="comment-form-wrapper">
                        <h4>Viết bình luận</h4>
                        <?php if (!empty($commentError)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($commentError) ?></div>
                        <?php endif; ?>
                        <form id="comment-form" class="comment-form" method="post" action="#comments-list">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="comment-name" class="form-label">Tên của bạn</label>
                                        <input type="text" class="form-control" id="comment-name" name="comment-name" placeholder="Nhập tên của bạn" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="comment-email" class="form-label">Email của bạn</label>
                                        <input type="email" class="form-control" id="comment-email" name="comment-email" placeholder="Nhập email của bạn" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="comment-content" class="form-label">Nội dung bình luận</label>
                                <textarea class="form-control" id="comment-content" name="comment-content" rows="5" placeholder="Chia sẻ suy nghĩ của bạn..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i>
                                Gửi bình luận
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="post-sidebar">
                    <!-- Categories Widget -->
                    <div class="post-sidebar__widget">
                        <h3 class="post-sidebar__title">Danh mục</h3>
                        <ul class="category-list">
                            <li><a href="#"><span>Du lịch</span><span class="count">15</span></a></li>
                            <li><a href="#"><span>Ẩm thực</span><span class="count">12</span></a></li>
                            <li><a href="#"><span>Văn hóa</span><span class="count">8</span></a></li>
                            <li><a href="#"><span>Lễ hội</span><span class="count">6</span></a></li>
                            <li><a href="#"><span>Phong cảnh</span><span class="count">10</span></a></li>
                        </ul>
                    </div>

                    <!-- Popular Posts Widget -->
                    <div class="post-sidebar__widget">
                        <h3 class="post-sidebar__title">Bài viết phổ biến</h3>
                        <div class="popular-posts" id="popular-posts">
                            <?php foreach ($popularPosts as $p): ?>
                                <div class="popular-post">
                                    <div class="popular-post__image">
                                        <a href="blog-detail.php?id=<?= $p['id'] ?>">
                                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                                        </a>
                                    </div>
                                    <div class="popular-post__content">
                                        <h4><a href="blog-detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></h4>
                                        <div class="popular-post__meta">
                                            <span><i class="bi bi-calendar"></i><?= date('d/m/Y', strtotime($p['published_at'])) ?></span>
                                            <span><i class="bi bi-eye"></i><?= number_format($p['views']) ?> lượt xem</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tags Widget -->
                    <div class="post-sidebar__widget">
                        <h3 class="post-sidebar__title">Tags</h3>
                        <div class="tag-cloud">
                            <a href="#" class="tag">Du lịch</a>
                            <a href="#" class="tag">Ẩm thực</a>
                            <a href="#" class="tag">Văn hóa</a>
                            <a href="#" class="tag">Lễ hội</a>
                            <a href="#" class="tag">Phong cảnh</a>
                            <a href="#" class="tag">Biển</a>
                            <a href="#" class="tag">Núi</a>
                            <a href="#" class="tag">Đảo</a>
                        </div>
                    </div>

                    <!-- Related Posts Widget -->
                    <div class="post-sidebar__widget">
                        <h3 class="post-sidebar__title">Bài viết liên quan</h3>
                        <div class="post-related" id="related-posts">
                            <?php foreach ($relatedPosts as $p): ?>
                                <div class="post-related__item">
                                    <div class="post-related__image">
                                        <a href="blog-detail.php?id=<?= $p['id'] ?>">
                                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                                        </a>
                                    </div>
                                    <div class="post-related__content">
                                        <h4><a href="blog-detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></h4>
                                        <div class="post-related__meta">
                                            <span><i class="bi bi-calendar"></i><?= date('d/m/Y', strtotime($p['published_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter">
    <div class="container">
        <div class="newsletter-wrapper text-center">
            <span class="subtitle">Đăng ký nhận tin</span>
            <h2>Nhận Thông Tin Du Lịch Mới Nhất</h2>
            <p>Đăng ký để nhận những bài viết hay và ưu đãi du lịch hấp dẫn</p>
            <form class="newsletter-form">
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="Nhập email của bạn">
                    <button class="btn btn-primary">Đăng ký</button>
                </div>
            </form>
        </div>
    </div>
</section>

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

<!-- Back to Top Button -->
<button id="backToTop" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>

</html>