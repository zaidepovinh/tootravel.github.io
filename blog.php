<?php
require_once 'config/database.php';
$db = new Database();

// Lấy tất cả bài viết đã xuất bản cùng thông tin tác giả, danh mục, số bình luận
$sql = "
    SELECT p.id, p.title, p.excerpt, p.thumbnail,
           COALESCE(p.thumbnail, 'images/Default-thumbnail.jpg') as image,
           p.published_at, p.views,
           u.username AS author_name, u.avatar AS author_avatar,
           c.name AS category_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND is_visible = 1) AS comment_count
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'published'
    ORDER BY p.published_at DESC
";
$db->query($sql);
$posts = $db->getAll();
require_once 'header.php';
?>

<!-- Blog Hero Section -->
<section class="blog-hero">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <span class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">BLOG DU LỊCH</span>
            <h1 data-aos="fade-up" data-aos-delay="200">Những Câu Chuyện Về Vẻ Đẹp Việt Nam</h1>
            <p data-aos="fade-up" data-aos-delay="300">Chia sẻ trải nghiệm, cảm xúc và những điều thú vị về du lịch<br>Việt Nam</p>
            <div class="hero-stats" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <span class="stat-number">100+</span>
                    <span class="stat-text">Bài Viết</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">30+</span>
                    <span class="stat-text">Tác Giả</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <span class="stat-text">Bình Luận</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Filter Section -->
<section class="blog-filter">
    <div class="container">
        <div class="filter-wrapper">
            <div class="search-box" data-aos="fade-right">
                <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm bài viết...">
                <button type="button" id="searchButton"><i class="bi bi-search"></i></button>
            </div>
            <div class="filter-buttons" data-aos="fade-up">
                <button class="filter-btn active" data-filter="all">Tất cả</button>
                <?php
                // Lấy danh sách danh mục từ database
                $sql = "SELECT name, slug FROM categories ORDER BY name";
                $db->query($sql);
                $categories = $db->getAll();

                foreach ($categories as $category): ?>
                    <button class="filter-btn" data-filter="<?= $category['slug'] ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="sort-box" data-aos="fade-left">
                <select class="form-select sort-select">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="most_viewed">Xem nhiều nhất</option>
                    <option value="least_viewed">Xem ít nhất</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Blog Grid -->
<section class="blog-grid">
    <div class="container">
        <div class="row g-4" id="posts-container">
            <?php foreach ($posts as $post): ?>
                <div class="col-lg-4 col-md-6">
                    <article class="post-card">
                        <div class="post-card__image">
                            <img src="<?php
                                        if (!empty($post['thumbnail'])) {
                                            $thumbnailPath = $post['thumbnail'];
                                            // Thêm đường dẫn gốc nếu là đường dẫn tương đối
                                            if (!str_starts_with($thumbnailPath, '/')) {
                                                $thumbnailPath = __DIR__ . '/' . $thumbnailPath;
                                            }
                                            echo file_exists($thumbnailPath) ?
                                                htmlspecialchars($post['thumbnail']) :
                                                'images/Default-thumbnail.jpg';
                                        } else {
                                            echo 'images/Default-thumbnail.jpg';
                                        }
                                        ?> 
                                 alt=" <?= htmlspecialchars($post['title']) ?>"
                                class="post-card__img"
                                onerror="this.src='images/Default-thumbnail.jpg'">
                            <span class="post-card__category"><?= htmlspecialchars($post['category_name']) ?></span>
                        </div>
                        <div class="post-card__content">
                            <div class="post-card__meta">
                                <span><i class="bi bi-calendar"></i><?= date('d/m/Y', strtotime($post['published_at'])) ?></span>
                                <span><i class="bi bi-eye"></i><?= number_format($post['views']) ?> lượt xem</span>
                                <span><i class="bi bi-chat"></i><?= $post['comment_count'] ?> bình luận</span>
                            </div>
                            <h3 class="post-card__title">
                                <a href="blog-detail.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                            </h3>
                            <p class="post-card__excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                            <div class="post-card__footer">
                                <div class="post-card__author">
                                    <div class="post-card__author-info">
                                        <div class="post-card__author-name"><?= htmlspecialchars($post['author_name']) ?></div>
                                    </div>
                                </div>
                                <a href="blog-detail.php?id=<?= $post['id'] ?>" class="read-more">Đọc thêm <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
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
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Định nghĩa hàm initSearch trước khi sử dụng
    function initSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');
        const postsContainer = document.getElementById('posts-container');

        if (!searchInput || !searchButton || !postsContainer) {
            console.error('Không tìm thấy các phần tử cần thiết cho chức năng tìm kiếm');
            return;
        }

        let searchTimeout;

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        function performSearch() {
            const query = searchInput.value.trim();
            if (query.length < 2) return;

            console.log('Thực hiện tìm kiếm với từ khóa:', query);

            postsContainer.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tìm kiếm...</span>
                        </div>
                        <p class="mt-2">Đang tìm kiếm...</p>
                    </div>
                `;

            fetch(`search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.error || 'Lỗi tìm kiếm');
                    }

                    const posts = result.data;
                    if (posts.length === 0) {
                        postsContainer.innerHTML = `
                                <div class="col-12 text-center">
                                    <div class="no-results">
                                        <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mt-3">Không tìm thấy kết quả nào cho "${query}"</p>
                                    </div>
                                </div>
                            `;
                        return;
                    }

                    postsContainer.innerHTML = posts.map(post => `
                            <div class="col-lg-4 col-md-6">
                                <article class="post-card">
                                    <div class="post-card__image">
                                        <img src="${post.image || 'images/Default-thumbnail.jpg'}" 
                                             alt="${post.title}"
                                             class="post-card__img"
                                             onerror="this.src='images/Default-thumbnail.jpg'">
                                        <span class="post-card__category">${post.category_name}</span>
                                    </div>
                                    <div class="post-card__content">
                                        <div class="post-card__meta">
                                            <span><i class="bi bi-calendar"></i>${formatDate(post.published_at)}</span>
                                            <span><i class="bi bi-eye"></i>${post.views} lượt xem</span>
                                            <span><i class="bi bi-chat"></i>${post.comment_count} bình luận</span>
                                        </div>
                                        <h3 class="post-card__title">
                                            <a href="blog-detail.php?id=${post.id}">${post.title}</a>
                                        </h3>
                                        <p class="post-card__excerpt">${post.excerpt}</p>
                                        <div class="post-card__footer">
                                            <div class="post-card__author">
                                                <div class="post-card__author-info">
                                                    <div class="post-card__author-name">${post.author_name}</div>
                                                </div>
                                            </div>
                                            <a href="blog-detail.php?id=${post.id}" class="read-more">Đọc thêm <i class="bi bi-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        `).join('');
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    postsContainer.innerHTML = `
                            <div class="col-12 text-center">
                                <div class="error-message">
                                    <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #dc3545;"></i>
                                    <p class="mt-3">Đã có lỗi xảy ra: ${error.message}</p>
                                </div>
                            </div>
                        `;
                });
        }

        searchButton.addEventListener('click', performSearch);

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    }

    // Khởi tạo khi trang đã tải xong
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init();
        initSearch();
        initFilter();
        initSort(); // Thêm dòng này
    });

    function initFilter() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const postsContainer = document.getElementById('posts-container');

        if (!filterButtons || !postsContainer) {
            console.error('Không tìm thấy các phần tử cần thiết cho chức năng lọc');
            return;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        function filterPosts(category) {
            // Hiển thị loading
            postsContainer.innerHTML = `
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải bài viết...</p>
            </div>
        `;

            fetch(`filter.php?category=${category}`)
                .then(response => response.json())
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.error || 'Lỗi lọc bài viết');
                    }

                    const posts = result.data;
                    if (posts.length === 0) {
                        postsContainer.innerHTML = `
                        <div class="col-12 text-center">
                            <div class="no-results">
                                <i class="bi bi-journal-x" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3">Không có bài viết nào trong danh mục này</p>
                            </div>
                        </div>
                    `;
                        return;
                    }

                    postsContainer.innerHTML = posts.map(post => `
                    <div class="col-lg-4 col-md-6">
                        <article class="post-card">
                            <div class="post-card__image">
                                <img src="${post.image || 'images/Default-thumbnail.jpg'}" 
                                     alt="${post.title}"
                                     class="post-card__img"
                                     onerror="this.src='images/Default-thumbnail.jpg'">
                                <span class="post-card__category">${post.category_name}</span>
                            </div>
                            <div class="post-card__content">
                                <div class="post-card__meta">
                                    <span><i class="bi bi-calendar"></i>${formatDate(post.published_at)}</span>
                                    <span><i class="bi bi-eye"></i>${post.views} lượt xem</span>
                                    <span><i class="bi bi-chat"></i>${post.comment_count} bình luận</span>
                                </div>
                                <h3 class="post-card__title">
                                    <a href="blog-detail.php?id=${post.id}">${post.title}</a>
                                </h3>
                                <p class="post-card__excerpt">${post.excerpt}</p>
                                <div class="post-card__footer">
                                    <div class="post-card__author">
                                        <div class="post-card__author-info">
                                            <div class="post-card__author-name">${post.author_name}</div>
                                        </div>
                                    </div>
                                    <a href="blog-detail.php?id=${post.id}" class="read-more">Đọc thêm <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </article>
                    </div>
                `).join('');
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    postsContainer.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #dc3545;"></i>
                            <p class="mt-3">Đã có lỗi xảy ra: ${error.message}</p>
                        </div>
                    </div>
                `;
                });
        }

        // Xử lý sự kiện click cho các nút lọc
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Gỡ bỏ class active từ tất cả các nút
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Thêm class active cho nút được click
                button.classList.add('active');
                // Lọc bài viết theo danh mục
                filterPosts(button.dataset.filter);
            });
        });
    }

    function initSort() {
        const sortSelect = document.querySelector('.sort-select');
        const postsContainer = document.getElementById('posts-container');
        let currentCategory = 'all';

        if (!sortSelect || !postsContainer) {
            console.error('Không tìm thấy các phần tử cần thiết cho chức năng sắp xếp');
            return;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        function sortPosts(sortBy) {
            // Hiển thị loading
            postsContainer.innerHTML = `
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải bài viết...</p>
            </div>
        `;

            // Thêm timestamp để tránh cache
            const timestamp = new Date().getTime();
            fetch(`sort.php?sort=${sortBy}&category=${currentCategory}&_=${timestamp}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.error || 'Lỗi sắp xếp bài viết');
                    }

                    const posts = result.data;
                    if (posts.length === 0) {
                        postsContainer.innerHTML = `
                        <div class="col-12 text-center">
                            <div class="no-results">
                                <i class="bi bi-journal-x" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3">Không có bài viết nào</p>
                            </div>
                        </div>
                    `;
                        return;
                    }

                    postsContainer.innerHTML = posts.map(post => `
                    <div class="col-lg-4 col-md-6">
                        <article class="post-card">
                            <div class="post-card__image">
                                <img src="${post.image || 'images/Default-thumbnail.jpg'}" 
                                     alt="${post.title}"
                                     class="post-card__img"
                                     onerror="this.src='images/Default-thumbnail.jpg'">
                                <span class="post-card__category">${post.category_name}</span>
                            </div>
                            <div class="post-card__content">
                                <div class="post-card__meta">
                                    <span><i class="bi bi-calendar"></i>${formatDate(post.published_at)}</span>
                                    <span><i class="bi bi-eye"></i>${post.views} lượt xem</span>
                                    <span><i class="bi bi-chat"></i>${post.comment_count} bình luận</span>
                                </div>
                                <h3 class="post-card__title">
                                    <a href="blog-detail.php?id=${post.id}">${post.title}</a>
                                </h3>
                                <p class="post-card__excerpt">${post.excerpt}</p>
                                <div class="post-card__footer">
                                    <div class="post-card__author">
                                        <div class="post-card__author-info">
                                            <div class="post-card__author-name">${post.author_name}</div>
                                        </div>
                                    </div>
                                    <a href="blog-detail.php?id=${post.id}" class="read-more">Đọc thêm <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </article>
                    </div>
                `).join('');
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    postsContainer.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #dc3545;"></i>
                            <p class="mt-3">Đã có lỗi xảy ra: ${error.message}</p>
                        </div>
                    </div>
                `;
                });
        }

        // Xử lý sự kiện thay đổi select
        sortSelect.addEventListener('change', () => {
            sortPosts(sortSelect.value);
        });

        // Cập nhật currentCategory khi lọc
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                currentCategory = button.dataset.filter;
                // Khi chọn danh mục mới, giữ nguyên kiểu sắp xếp hiện tại
                sortPosts(sortSelect.value);
            });
        });

        // Khởi tạo ban đầu với giá trị mặc định
        sortPosts(sortSelect.value);
    }
</script>
</body>

</html>