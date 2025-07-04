<?php
require_once 'config/database.php';
// Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy tên file hiện tại
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TooTravel - Khám phá vẻ đẹp Việt Nam qua những điểm đến tuyệt vời, trải nghiệm văn hóa độc đáo và ẩm thực phong phú">
    <meta name="keywords" content="du lịch Việt Nam, điểm đến Việt Nam, văn hóa Việt Nam, ẩm thực Việt Nam">
    <meta name="author" content="TooTravel">
    <meta property="og:title" content="TooTravel - Khám Phá Việt Nam">
    <meta property="og:description" content="Khám phá vẻ đẹp Việt Nam qua những điểm đến tuyệt vời">
    <meta property="og:image" content="images/logo.png">
    <meta property="og:url" content="https://TooTravel.com">
    <title>TooTravel - Khám Phá Việt Nam</title>
    
    <!-- Preload Fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" as="style">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg fixed-top bg-white">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    TooTravel
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                                Trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>" href="about.php">
                                Về chúng tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'blog.php' ? 'active' : '' ?>" href="blog.php">
                                Blog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'gallery.php' ? 'active' : '' ?>" href="gallery.php">
                                Thư Viện
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="contact.php">
                                Liên hệ
                            </a>
                        </li>
                    </ul>
                    
                    <div class="nav-buttons">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <div class="user-info dropdown">
                                <button class="btn btn-link dropdown-toggle d-flex align-items-center gap-2"  
                                        type="button" 
                                        id="userDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <img src="<?= isset($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'images/default-avatar.jpg' ?>" 
                                         alt="Avatar" 
                                         class="rounded-circle"
                                         width="32" 
                                         height="32">
                                    <strong><span class="username"><?= htmlspecialchars($_SESSION['username']) ?></span></strong>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li class="dropdown-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= isset($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'images/default-avatar.jpg' ?>" 
                                                 alt="Avatar"
                                                 class="rounded-circle"
                                                 width="48" 
                                                 height="48">
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></div>
                                                <div class="small text-muted">
                                                    <?= $_SESSION['role'] === 'admin' ? 'Quản trị viên' : 'Thành viên' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="profile.php">
                                            <i class="bi bi-person"></i> Hồ sơ
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="myposts.php">
                                            <i class="bi bi-file-earmark-text"></i> Quản lý bài viết 
                                        </a>
                                    </li>
                                    <?php if($_SESSION['role'] === 'admin'): ?>
                                        <li>
                                            <a class="dropdown-item" href="admin/index.php">
                                                <i class="bi bi-speedometer2"></i> Quản trị
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="logout.php">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                            <a href="register.php" class="btn btn-primary">Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>