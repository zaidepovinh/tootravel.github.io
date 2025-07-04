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
    $userId = (int)($_POST['user_id'] ?? 0);

    switch ($action) {
        case 'delete':
            // Không cho phép xóa tài khoản admin
            $db->query("SELECT role FROM users WHERE id = ?");
            $db->bind(1, $userId);
            $user = $db->getOne();

            if ($user && $user['role'] === 'admin') {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản admin']);
                exit;
            }

            $db->query("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $db->bind(1, $userId);
            echo json_encode(['success' => $db->execute()]);
            exit;

        case 'update_status':
            $status = $_POST['status'] === 'active' ? 'active' : 'blocked';
            $db->query("UPDATE users SET status = ? WHERE id = ? AND role != 'admin'");
            $db->bind(1, $status);
            $db->bind(2, $userId);
            echo json_encode(['success' => $db->execute()]);
            exit;

        case 'update':
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'user';

            if (empty($username) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit;
            }

            $db->query("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
            $db->bind(1, $username);
            $db->bind(2, $email);
            $db->bind(3, $role);
            $db->bind(4, $userId);
            echo json_encode(['success' => $db->execute()]);
            exit;

        case 'add':
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $password = password_hash('123456', PASSWORD_DEFAULT); // Mật khẩu mặc định

            if (empty($username) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit;
            }

            // Kiểm tra username/email đã tồn tại
            $db->query("SELECT id FROM users WHERE username = ? OR email = ?");
            $db->bind(1, $username);
            $db->bind(2, $email);
            if ($db->getOne()) {
                echo json_encode(['success' => false, 'message' => 'Tên người dùng hoặc email đã tồn tại']);
                exit;
            }

            $db->query("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
            $db->bind(1, $username);
            $db->bind(2, $email);
            $db->bind(3, $password);
            $db->bind(4, $role);
            echo json_encode(['success' => $db->execute()]);
            exit;
    }
}

// Lấy danh sách users
$db->query("
    SELECT id, username, email, role, status, created_at, avatar 
    FROM users 
    ORDER BY created_at DESC
");
$users = $db->getAll();

// Lấy thông tin admin đang đăng nhập
$db->query("SELECT * FROM users WHERE id = ?");
$db->bind(1, $_SESSION['user_id']);
$currentUser = $db->getOne();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Admin</title>
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
                    <li>
                        <a href="comments.php">
                            <i class="bi bi-chat-dots"></i>
                            Quản lý bình luận
                        </a>
                    </li>
                    <li class="active">
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
                    <h2>Quản lý người dùng</h2>
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

            <!-- Users Management Content -->
            <div class="admin-content">
                <div class="admin-card">
                    <div class="admin-card-header d-flex justify-content-between align-items-center">
                        <h5>Danh sách người dùng</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                            <i class="bi bi-plus-circle"></i> Thêm người dùng
                        </button>
                    </div>
                    <div class="admin-card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên người dùng</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                                                    <?= $user['role'] === 'admin' ? 'Admin' : 'User' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= $user['status'] === 'active' ? 'Hoạt động' : 'Đã khóa' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <?php if ($user['role'] !== 'admin'): ?>
                                                    <button class="btn btn-sm btn-primary edit-user"
                                                        data-id="<?= $user['id'] ?>"
                                                        data-username="<?= htmlspecialchars($user['username']) ?>"
                                                        data-email="<?= htmlspecialchars($user['email']) ?>"
                                                        data-role="<?= $user['role'] ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm <?= $user['status'] === 'active' ? 'btn-warning' : 'btn-success' ?> toggle-status"
                                                        data-id="<?= $user['id'] ?>"
                                                        data-status="<?= $user['status'] ?>">
                                                        <i class="bi <?= $user['status'] === 'active' ? 'bi-lock' : 'bi-unlock' ?>"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-user" data-id="<?= $user['id'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm/Sửa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId" name="user_id">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên người dùng</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveUser">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userModal = new bootstrap.Modal(document.getElementById('userModal'));

            // Xử lý sửa user
            document.querySelectorAll('.edit-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.id;
                    const username = this.dataset.username;
                    const email = this.dataset.email;
                    const role = this.dataset.role;

                    document.getElementById('userId').value = userId;
                    document.getElementById('username').value = username;
                    document.getElementById('email').value = email;
                    document.getElementById('role').value = role;

                    userModal.show();
                });
            });

            // Xử lý lưu user
            document.getElementById('saveUser').addEventListener('click', function() {
                const form = document.getElementById('userForm');
                const formData = new FormData(form);
                const userId = document.getElementById('userId').value;

                // Nếu không có userId thì là thêm mới
                formData.append('action', userId ? 'update' : 'add');

                fetch('users.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Có lỗi xảy ra');
                        }
                    });
            });

            // Xử lý khóa/mở khóa user
            document.querySelectorAll('.toggle-status').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.id;
                    const currentStatus = this.dataset.status;
                    const newStatus = currentStatus === 'active' ? 'blocked' : 'active';

                    fetch('users.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=update_status&user_id=${userId}&status=${newStatus}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                });
            });

            // Xử lý xóa user
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
                        const userId = this.dataset.id;

                        fetch('users.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `action=delete&user_id=${userId}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert(data.message || 'Có lỗi xảy ra');
                                }
                            });
                    }
                });
            });

            // Thêm xử lý khi click nút "Thêm người dùng"
            document.querySelector('[data-bs-target="#userModal"]').addEventListener('click', function() {
                // Reset form khi thêm mới
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
            });
        });
    </script>
</body>

</html>