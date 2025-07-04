<?php
require_once 'config/database.php';
$db = new Database();
require_once 'header.php';
// Lấy 3 bài viết mới nhất cùng thông tin tác giả và danh mục
$sql = "
    SELECT p.id, p.title, p.excerpt, 
           COALESCE(NULLIF(p.thumbnail, ''), 'images/Default-thumbnail.jpg') as image,
           p.published_at, p.views,
           u.username AS author_name, u.avatar AS author_avatar,
           c.name AS category_name,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND is_visible = 1) as comment_count
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'published'
    ORDER BY p.published_at DESC
    LIMIT 3
";
$db->query($sql);
$posts = $db->getAll();

?>

<!-- Hero Section -->
<section class="hero">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/halong.jpg');">
                <div class="hero-content">
                    <span class="hero-subtitle">Khám phá Việt Nam</span>
                    <h1>Vịnh Hạ Long</h1>
                    <p>Kỳ quan thiên nhiên thế giới với hàng nghìn hòn đảo đá vôi hùng vĩ</p>
                    <div class="hero-buttons">
                        <a href="gallery.php" class="btn btn-primary">Khám phá ngay</a>
                        <a href="blog.php" class="btn btn-outline-light">Tìm hiểu thêm</a>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/hoian.jpg');">
                <div class="hero-content">
                    <span class="hero-subtitle">Khám phá Việt Nam</span>
                    <h1>Phố Cổ Hội An</h1>
                    <p>Thành phố cổ kính với kiến trúc độc đáo và văn hóa truyền thống</p>
                    <div class="hero-buttons">
                        <a href="gallery.php" class="btn btn-primary">Khám phá ngay</a>
                        <a href="blog.php" class="btn btn-outline-light">Tìm hiểu thêm</a>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/sapa.jpg');">
                <div class="hero-content">
                    <span class="hero-subtitle">Khám phá Việt Nam</span>
                    <h1>Sapa Hùng Vĩ</h1>
                    <p>Vùng đất của những thửa ruộng bậc thang và văn hóa dân tộc độc đáo</p>
                    <div class="hero-buttons">
                        <a href="gallery.php" class="btn btn-primary">Khám phá ngay</a>
                        <a href="blog.php" class="btn btn-outline-light">Tìm hiểu thêm</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<!-- Chức năng tìm kiếm bài viết -->
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
                    <option value="most_commented">Nhiều bình luận nhất</option>
                    <option value="least_commented">Ít bình luận nhất</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Hiển thị kết quả tìm kiếm -->
<section class="search-results" style="display: none;">
    <div class="container">
        <div class="row" id="searchResults"></div>
    </div>
</section>

<!-- Service Features -->
<section class="service-features">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/planning.png" alt="Planning Icon">
                    </div>
                    <h3>Kế Hoạch Du Lịch</h3>
                    <p>TooTravel: Cổng thông tin du lịch toàn diện giúp bạn lên kế hoạch chuyến đi dễ dàng. Khám phá Việt Nam với sự hỗ trợ của chúng tôi, từ chỗ ở đến các hoạt động, đảm bảo một trải nghiệm trọn vẹn và không phiền phức.</p>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/map.png" alt="Experience Icon">
                    </div>
                    <h3>Trải nghiệm phù hợp</h3>
                    <p>Tạo nên những trải nghiệm độc đáo dành riêng cho bạn. Khám phá Việt Nam theo cách chưa từng có với các hành trình được cá nhân hóa và sự hướng dẫn chuyên nghiệp, đảm bảo mỗi khoảnh khắc trong chuyến đi đều hoàn hảo.</p>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/trust.png" alt="Trust Icon">
                    </div>
                    <h3>Đáng Tin Cậy</h3>
                    <p>TooTravel: Người bạn đồng hành đáng tin cậy trong mọi chuyến đi. Với cam kết về chất lượng dịch vụ và sự chuyên nghiệp, hãy an tâm khám phá Việt Nam cùng chúng tôi, nơi mọi chi tiết đều được chăm chút tỉ mỉ.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Offerings -->
<section class="offerings">
    <div class="container">
        <div class="offering-grid">
            <div class="offering-item" data-aos="fade-right">
                <div class="offering-number">01</div>
                <div class="offering-content">
                    <h3>Khám Phá Văn Hóa</h3>
                    <p>Trải nghiệm nét văn hóa độc đáo của Việt Nam thông qua các tour du lịch văn hóa, lễ hội truyền thống và giao lưu với người dân địa phương. Khám phá những di sản văn hóa phi vật thể và vật thể được UNESCO công nhận.</p>
                </div>
            </div>
            <div class="offering-item" data-aos="fade-left">
                <div class="offering-image">
                    <img src="images/culture.jpg" alt="Văn hóa Việt Nam">
                </div>
            </div>
            <div class="offering-item" data-aos="fade-right">
                <div class="offering-image">
                    <img src="images/nature.jpg" alt="Thiên nhiên Việt Nam">
                </div>
            </div>
            <div class="offering-item" data-aos="fade-left">
                <div class="offering-number">02</div>
                <div class="offering-content">
                    <h3>Khám Phá Thiên Nhiên</h3>
                    <p>Chiêm ngưỡng vẻ đẹp tuyệt vời của thiên nhiên Việt Nam từ những vịnh biển trong xanh đến những dãy núi hùng vĩ. Tham gia các hoạt động ngoài trời và khám phá hệ sinh thái đa dạng của đất nước.</p>
                </div>
            </div>
            <div class="offering-item" data-aos="fade-right">
                <div class="offering-number">03</div>
                <div class="offering-content">
                    <h3>Ẩm Thực Đặc Sắc</h3>
                    <p>Khám phá nền ẩm thực phong phú của Việt Nam với những món ăn đặc trưng từng vùng miền. Trải nghiệm nghệ thuật nấu nướng truyền thống và thưởng thức hương vị độc đáo của ẩm thực đường phố.</p>
                </div>
            </div>
            <div class="offering-item" data-aos="fade-left">
                <div class="offering-image">
                    <img src="images/food.jpg" alt="Ẩm thực Việt Nam">
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Latest Blog Posts -->
<section class="latest-posts">
    <div class="container">
        <div class="blog-title" data-aos="fade-up">
            <span class="subtitle">Blog du lịch</span>
            <h2>Bài Viết Mới Nhất</h2>
            <p>Cập nhật những trải nghiệm và câu chuyện du lịch thú vị</p>
        </div>
        <div class="row g-4">
            <?php foreach ($posts as $index => $post): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" <?php if ($index > 0) echo 'data-aos-delay="' . ($index * 100) . '"'; ?>>
                    <article class="post-card">
                        <div class="post-card__image">
                            <?php
                            $imagePath = !empty($post['image']) ? $post['image'] : 'images/Default-thumbnail.jpg';
                            if ($imagePath !== 'images/Default-thumbnail.jpg') {
                                // Kiểm tra file có tồn tại không
                                $fullPath = __DIR__ . '/' . $imagePath;
                                if (!file_exists($fullPath)) {
                                    $imagePath = 'images/Default-thumbnail.jpg';
                                }
                            }
                            ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>"
                                alt="<?= htmlspecialchars($post['title']) ?>"
                                class="post-card__img"
                                onerror="this.src='images/Default-thumbnail.jpg'">
                            <span class="post-card__category"><?= htmlspecialchars($post['category_name']) ?></span>
                        </div>
                        <div class="post-card__content">
                            <div class="post-card__meta">
                                <span><i class="bi bi-calendar"></i><?= date('d M', strtotime($post['published_at'])) ?></span>
                                <span><i class="bi bi-eye"></i><?= number_format($post['views']) ?> lượt xem</span>
                                <span><i class="bi bi-chat"></i><?= number_format($post['comment_count']) ?> bình luận</span>
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

<!-- Image Gallery -->
<section class="image-gallery">
    <div class="container">
        <div class="gallery-header">
            <div class="header-left" data-aos="fade-right" data-aos-duration="1000">
                <h2 class="gallery-title">Khám Phá Vẻ Đẹp Việt Nam</h2>
            </div>
            <div class="header-right" data-aos="fade-left" data-aos-duration="1000">
                <p>Từ những thành phố sôi động đến những vùng núi non hùng vĩ, từ những bãi biển hoang sơ đến những di sản văn hóa độc đáo - hãy để chúng tôi dẫn lối bạn khám phá Việt Nam theo cách riêng của mình.</p>
                <a href="gallery.php" class="btn-explore">
                    Khám phá thêm
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="gallery-grid">
            <div class="gallery-row top-row">
                <div class="gallery-item" data-aos="zoom-in-up" data-aos-duration="800">
                    <img src="images/gallery-1.jpg" alt="Thừa Thiên Huế">
                    <div class="gallery-overlay">
                        <h3>Thừa Thiên Huế</h3>
                    </div>
                </div>
                <div class="gallery-item" data-aos="zoom-in-up" data-aos-duration="800" data-aos-delay="200">
                    <img src="images/gallery-2.jpg" alt="Hà Nội">
                    <div class="gallery-overlay">
                        <h3>Hà Nội</h3>
                    </div>
                </div>
                <div class="gallery-item" data-aos="zoom-in-up" data-aos-duration="800" data-aos-delay="400">
                    <img src="images/gallery-3.jpg" alt="Hội An">
                    <div class="gallery-overlay">
                        <h3>Hội An</h3>
                    </div>
                </div>
            </div>

            <div class="gallery-row bottom-row">
                <div class="gallery-item wide" data-aos="fade-up" data-aos-duration="1000">
                    <img src="images/gallery-4.jpg" alt="Sapa">
                    <div class="gallery-overlay">
                        <h3>Sapa</h3>
                    </div>
                </div>
                <div class="gallery-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <img src="images/gallery-5.jpg" alt="Quảng Ninh">
                    <div class="gallery-overlay">
                        <h3>Quảng Ninh</h3>
                    </div>
                </div>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="js/main.js"></script>
<script>
    function performSearch() {
        const query = document.getElementById('searchInput').value.trim();
        const searchResults = document.getElementById('searchResults');
        const searchResultsSection = document.querySelector('.search-results');

        if (query.length < 2) {
            searchResultsSection.style.display = 'none';
            return;
        }

        fetch(`search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayResults(result.data);
                    searchResultsSection.style.display = 'block';
                } else {
                    throw new Error(result.error);
                }
            })
            .catch(error => {
                searchResults.innerHTML = `
                <div class="col-12 text-center">
                    <div class="error-message">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>${error.message}</p>
                    </div>
                </div>
            `;
                searchResultsSection.style.display = 'block';
            });
    }

    function filterPosts(category) {
        const searchResults = document.getElementById('searchResults');
        const searchResultsSection = document.querySelector('.search-results');

        // Cập nhật trạng thái active của nút lọc
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === category) {
                btn.classList.add('active');
            }
        });

        fetch(`filter.php?category=${encodeURIComponent(category)}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayResults(result.data);
                    searchResultsSection.style.display = 'block';
                } else {
                    throw new Error(result.error);
                }
            })
            .catch(error => {
                searchResults.innerHTML = `
                <div class="col-12 text-center">
                    <div class="error-message">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>${error.message}</p>
                    </div>
                </div>
            `;
                searchResultsSection.style.display = 'block';
            });
    }

    function sortPosts(sortBy, currentCategory) {
        const searchResults = document.getElementById('searchResults');
        const searchResultsSection = document.querySelector('.search-results');

        fetch(`sort.php?sort=${encodeURIComponent(sortBy)}&category=${encodeURIComponent(currentCategory)}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayResults(result.data);
                    searchResultsSection.style.display = 'block';
                } else {
                    throw new Error(result.error);
                }
            })
            .catch(error => {
                searchResults.innerHTML = `
                <div class="col-12 text-center">
                    <div class="error-message">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>${error.message}</p>
                    </div>
                </div>
            `;
                searchResultsSection.style.display = 'block';
            });
    }

    function formatCommentCount(count) {
        if (count >= 1000) {
            return (count / 1000).toFixed(1) + 'k';
        }
        return count.toString();
    }

    function displayResults(posts) {
        const searchResults = document.getElementById('searchResults');

        if (!posts || posts.length === 0) {
            searchResults.innerHTML = `
            <div class="col-12 text-center">
                <div class="no-results">
                    <i class="bi bi-search"></i>
                    <p>Không tìm thấy kết quả nào</p>
                </div>
            </div>
        `;
            return;
        }

        const html = posts.map((post, index) => `
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="${index * 100}">
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
                        <span><i class="bi bi-calendar"></i> ${formatDate(post.published_at)}</span>
                        <span><i class="bi bi-eye"></i> ${post.views} lượt xem</span>
                        <span><i class="bi bi-chat"></i> ${formatCommentCount(post.comment_count)} bình luận</span>
                    </div>
                    <h3 class="post-card__title">
                        <a href="blog-detail.php?id=${post.id}">${post.title}</a>
                    </h3>
                    <p class="post-card__excerpt">${post.excerpt}</p>
                    <div class="post-card__footer">
                        <div class="post-card__author">
                            <div class="post-card__author-name">${post.author_name}</div>
                        </div>
                        <a href="blog-detail.php?id=${post.id}" class="read-more">Đọc thêm <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </article>
        </div>
    `).join('');

        searchResults.innerHTML = html;
        AOS.refresh();
    }

    // Thêm event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const sortSelect = document.querySelector('.sort-select');

        // Tìm kiếm
        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        });

        // Lọc
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterPosts(this.dataset.filter);
            });
        });

        // Sắp xếp
        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            const currentCategory = document.querySelector('.filter-btn.active').dataset.filter;
            
            // Hiển thị loading state
            const searchResults = document.getElementById('searchResults');
            searchResults.innerHTML = `
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang sắp xếp bài viết...</p>
                </div>
            `;
            
            sortPosts(sortBy, currentCategory);
        });
    });

    // Kiểm tra trạng thái đăng nhập và cập nhật giao diện
    function updateLoginUI() {
        const navButtons = document.getElementById('navButtons');
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));

        if (currentUser) {
            // Người dùng đã đăng nhập
            navButtons.innerHTML = `
                    <div class="user-info">
                        <span class="username">Xin chào, ${currentUser.username}</span>
                        <button onclick="handleLogout()" class="btn btn-outline-danger">Đăng xuất</button>
                    </div>
                `;
        } else {
            // Chưa đăng nhập
            navButtons.innerHTML = `
                    <a href="login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                    <a href="register.php" class="btn btn-primary">Đăng ký</a>
                `;
        }
    }

    // Hàm xử lý đăng xuất
    function handleLogout() {
        localStorage.removeItem('currentUser');
        window.location.reload(); // Tải lại trang sau khi đăng xuất
    }

    // Gọi hàm updateLoginUI khi trang được tải
    document.addEventListener('DOMContentLoaded', function() {
        updateLoginUI();

        // Initialize Swiper
        const swiper = new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
        });

        // Back to Top Button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý ảnh lỗi cho post-card
        const postImages = document.querySelectorAll('.post-card__image img');
        postImages.forEach(img => {
            img.onerror = function() {
                this.src = 'images/Default-thumbnail.jpg';
                this.classList.add('fallback-image');
            };

            // Kiểm tra ảnh ngay khi tải trang
            if (!img.complete || img.naturalWidth === 0) {
                img.src = 'images/Default-thumbnail.jpg';
                img.classList.add('fallback-image');
            }
        });
    });
</script>
</body>

</html>



