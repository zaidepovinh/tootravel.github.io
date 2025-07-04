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

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Xử lý upload avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
            $fileName = uniqid('avatar_') . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $targetPath = $uploadDir . $fileName; 
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                // Xóa avatar cũ nếu có
                if (isset($_SESSION['avatar']) && file_exists($_SESSION['avatar'])) {
                    unlink($_SESSION['avatar']);
                }
                
                $db->query("UPDATE users SET avatar = ? WHERE id = ?");
                $db->bind(1, $targetPath); // Lưu đường dẫn tương đối
                $db->bind(2, $_SESSION['user_id']);
                if ($db->execute()) {
                    $_SESSION['avatar'] = $targetPath;
                }
            }
        }
    }

    // Cập nhật thông tin cơ bản
    if (!empty($username) && !empty($email)) {
        $db->query("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
        $db->bind(1, $username);
        $db->bind(2, $email);
        $db->bind(3, $bio);
        $db->bind(4, $_SESSION['user_id']);

        if ($db->execute()) {
            $_SESSION['username'] = $username;
            $success = "Cập nhật thông tin thành công!";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật thông tin!";
        }
    }
}

// Lấy thông tin user
$db->query("SELECT * FROM users WHERE id = ?");
$db->bind(1, $_SESSION['user_id']);
$user = $db->getOne();

require_once 'header.php';
?>

<div class="container main-container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?= htmlspecialchars($user['avatar'] ?? 'images/default-avatar.jpg') ?>"
                        alt="Avatar"
                        class="rounded-circle img-fluid mb-3 profile-avatar">
                    <h5 class="card-title"><?= htmlspecialchars($user['username']) ?></h5>
                    <p class="text-muted"><?= $user['role'] === 'admin' ? 'Quản trị viên' : 'Thành viên' ?></p>
                    <p class="card-text"><?= htmlspecialchars($user['bio'] ?? '') ?></p>

                    <div class="d-flex justify-content-around text-center mt-4">
                        <div>
                            <h6>Bài viết</h6>
                            <?php
                            $db->query("SELECT COUNT(*) as count FROM posts WHERE author_id = ?");
                            $db->bind(1, $_SESSION['user_id']);
                            $postCount = $db->getOne()['count'];
                            ?>
                            <p class="mb-0 text-muted"><?= $postCount ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Chỉnh sửa thông tin</h5>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control" name="avatar" accept="image/*">
                            <small class="form-text text-muted">Chọn ảnh định dạng JPG, PNG hoặc GIF</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên người dùng</label>
                            <input type="text" class="form-control" name="username"
                                value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giới thiệu bản thân</label>
                            <textarea class="form-control" name="bio" rows="4"
                                placeholder="Viết vài điều về bản thân..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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