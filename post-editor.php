<?php
require_once 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$error = '';
$success = '';

// Lấy danh sách danh mục
$db->query("SELECT * FROM categories ORDER BY name");
$categories = $db->getAll();

// Lấy thông tin bài viết nếu là chỉnh sửa
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($postId > 0) {
    // Kiểm tra quyền sở hữu bài viết
    $db->query("SELECT * FROM posts WHERE id = ? AND author_id = ?");
    $db->bind(1, $postId);
    $db->bind(2, $_SESSION['user_id']);
    $post = $db->getOne();

    if (!$post) {
        header('Location: myposts.php');
        exit;
    }
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $excerpt = trim($_POST['excerpt']);
    $categoryId = (int)$_POST['category_id'];
    $status = $_POST['status'];

    if (empty($title) || empty($content)) {
        $error = 'Vui lòng điền đầy đủ tiêu đề và nội dung bài viết';
    } else {
        // Xử lý upload thumbnail
        $thumbnail = $post ? $post['thumbnail'] : '';
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/posts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (in_array($fileExtension, $allowedExtensions) && $_FILES['thumbnail']['size'] <= $maxFileSize) {
                $fileName = uniqid('post_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
                    // Xóa ảnh cũ nếu tồn tại
                    if ($post && $post['thumbnail'] && file_exists($post['thumbnail'])) {
                        unlink($post['thumbnail']);
                    }
                    $thumbnail = $targetPath;
                } else {
                    $thumbnail = 'images/Default-thumbnail.jpg';
                }
            } else {
                $thumbnail = 'images/Default-thumbnail.jpg';
            }
        } else {
            // Nếu không có ảnh upload hoặc có lỗi, sử dụng ảnh mặc định
            if (empty($thumbnail) || !file_exists($thumbnail)) {
                $thumbnail = 'images/Default-thumbnail.jpg';
            }
        }

        if ($postId > 0) {
            // Cập nhật bài viết
            $db->query("
                UPDATE posts 
                SET title = ?, content = ?, excerpt = ?, category_id = ?, 
                    thumbnail = ?, image = ?, status = ?, updated_at = NOW() 
                WHERE id = ? AND author_id = ?
            ");
            $db->bind(1, $title);
            $db->bind(2, $content);
            $db->bind(3, $excerpt);
            $db->bind(4, $categoryId);
            $db->bind(5, $thumbnail); // Dùng cùng giá trị cho cả thumbnail
            $db->bind(6, $thumbnail); // và image
            $db->bind(7, $status);
            $db->bind(8, $postId);
            $db->bind(9, $_SESSION['user_id']);
        } else {
            // Thêm bài viết mới
            $db->query("
                INSERT INTO posts (title, content, excerpt, category_id, thumbnail, image,
                                 status, author_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $db->bind(1, $title);
            $db->bind(2, $content);
            $db->bind(3, $excerpt);
            $db->bind(4, $categoryId);
            $db->bind(5, $thumbnail); // Dùng cùng giá trị cho cả thumbnail
            $db->bind(6, $thumbnail); // và image
            $db->bind(7, $status);
            $db->bind(8, $_SESSION['user_id']);
        }

        if ($db->execute()) {
            $success = 'Đã lưu bài viết thành công!';
            if (!$postId) {
                header('Location: myposts.php');
                exit;
            }
        } else {
            $error = 'Có lỗi xảy ra, vui lòng thử lại!';
        }
    }
}

require_once 'header.php';
?>

<div class="container main-container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="card-title">
                            <?= $postId ? 'Chỉnh sửa bài viết' : 'Tạo bài viết mới' ?>
                        </h2>
                        <a href="myposts.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?= $post ? htmlspecialchars($post['title']) : '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tóm tắt</label>
                            <textarea class="form-control" name="excerpt" rows="3"
                                    placeholder="Tóm tắt ngắn gọn về nội dung bài viết..."
                            ><?= $post ? htmlspecialchars($post['excerpt']) : '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="content" id="editor" rows="10"
                            ><?= $post ? htmlspecialchars($post['content']) : '' ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select class="form-select" name="category_id">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" 
                                                <?= ($post && $post['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="draft" <?= ($post && $post['status'] == 'draft') ? 'selected' : '' ?>>
                                            Bản nháp
                                        </option>
                                        <option value="published" <?= ($post && $post['status'] == 'published') ? 'selected' : '' ?>>
                                            Xuất bản
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh thumbnail</label>
                            <?php if ($post && $post['thumbnail']): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($post['thumbnail']) ?>" 
                                         alt="Current thumbnail" 
                                         class="img-thumbnail" 
                                         style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                            <div class="form-text">Chọn ảnh định dạng JPG, PNG hoặc WebP</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Lưu bài viết
                            </button>
                            <a href="myposts.php" class="btn btn-light">
                                <i class="bi bi-x"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'blockQuote', 'insertTable', '|',
            'undo', 'redo'
        ]
    })
    .catch(error => {
        console.error(error);
    });
</script>
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