<?php
// filepath: c:\xampp\htdocs\hehe\register.php
require_once 'config/database.php';
$db = new Database();

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Kiểm tra username/email đã tồn tại
        $db->query("SELECT id FROM users WHERE username = ? OR email = ?");
        $db->bind(1, $username, 's');
        $db->bind(2, $email, 's');
        $exist = $db->getOne();
        if ($exist) {
            $error = 'Tên đăng nhập hoặc email đã tồn tại!';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db->query("INSERT INTO users (username, password, email, role, status) VALUES (?, ?, ?, 'user', 'active')");
            $db->bind(1, $username, 's');
            $db->bind(2, $hash, 's');
            $db->bind(3, $email, 's');
            if ($db->execute()) {
                $success = 'Đăng ký thành công! Bạn có thể <a href="login.php">đăng nhập</a> ngay.';
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại!';
            }
        }
    }
}

require_once 'header.php';
?>

    <div class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Đăng Ký</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form class="auth-form" method="post">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm">Xác nhận mật khẩu</label>
                    <input type="password" id="confirm" name="confirm" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Đăng Ký</button>
                </div>
                <div class="auth-links">
                    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>