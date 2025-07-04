<?php
require_once 'config/database.php';

// Kiểm tra xem phiên đã bắt đầu chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu người dùng đã đăng nhập, hãy chuyển hướng họ đến trang chính hoặc trang quản trị
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        $db = new Database();
        // Kiểm tra thông tin đăng nhập
        $db->query("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $db->bind(1, $username);
        $user = $db->getOne();

        if ($user && password_verify($password, $user['password'])) {
            // Lưu thông tin vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['avatar'] = $user['avatar'];

            // Chuyển hướng dựa vào role
            if ($user['role'] === 'admin') {    
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    }
}

require_once 'header.php';
?>

    <div class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Đăng Nhập</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form class="auth-form" method="post">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Đăng Nhập</button>
                </div>
                <div class="auth-links">
                    <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>