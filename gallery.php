<?php
require_once 'config/database.php';
require_once 'header.php';
?>

<!-- Gallery Hero Section -->
<section class="gallery-hero">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <span class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">Qua Ống Kính</span>
            <h1 data-aos="fade-up" data-aos-delay="200">Khám Phá Vẻ Đẹp Việt Nam</h1>
            <p data-aos="fade-up" data-aos-delay="300">Bộ sưu tập những khoảnh khắc tuyệt đẹp về thiên nhiên, văn hóa và con người Việt Nam</p>
            <div class="hero-stats" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <span class="stat-text">Bức Ảnh</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">63</span>
                    <span class="stat-text">Tỉnh Thành</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4</span>
                    <span class="stat-text">Chủ Đề</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Filter -->
<section class="gallery-filter">
    <div class="container">
        <div class="filter-buttons text-center">
            <button class="filter-btn active" data-filter="all">Tất cả</button>
            <button class="filter-btn" data-filter="landscape">Phong cảnh</button>
            <button class="filter-btn" data-filter="culture">Văn hóa</button>
            <button class="filter-btn" data-filter="food">Ẩm thực</button>
            <button class="filter-btn" data-filter="people">Con người</button>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="gallery-main">
    <div class="container">
        <div class="grid-container">
            <!-- Landscape Images -->
            <div class="gallery-item landscape" data-aos="fade-up">
                <img src="images/landscape1.jpg" alt="Phong cảnh Hà Giang">
                <div class="gallery-overlay">
                    <h3>Hà Giang</h3>
                    <p>Cao nguyên đá kỳ vĩ</p>
                </div>
            </div>
            <div class="gallery-item landscape" data-aos="fade-up" data-aos-delay="100">
                <img src="images/landscape2.jpg" alt="Phong cảnh Ninh Bình">
                <div class="gallery-overlay">
                    <h3>Ninh Bình</h3>
                    <p>Vịnh Hạ Long trên cạn</p>
                </div>
            </div>
            <div class="gallery-item landscape" data-aos="fade-up" data-aos-delay="200">
                <img src="images/landscape3.jpg" alt="Phong cảnh Hạ Long">
                <div class="gallery-overlay">
                    <h3>Hạ Long</h3>
                    <p>Di sản thiên nhiên thế giới</p>
                </div>
            </div>

            <!-- Culture Images -->
            <div class="gallery-item culture" data-aos="fade-up">
                <img src="images/culture1.jpg" alt="Phố cổ Hội An">
                <div class="gallery-overlay">
                    <h3>Phố cổ Hội An</h3>
                    <p>Di sản văn hóa thế giới</p>
                </div>
            </div>
            <div class="gallery-item culture" data-aos="fade-up" data-aos-delay="100">
                <img src="images/culture2.jpg" alt="Tết cổ truyền Việt Nam">
                <div class="gallery-overlay">
                    <h3>Tết cổ truyền</h3>
                    <p>Ngày lễ lớn của dân tộc</p>
                </div>
            </div>
            <div class="gallery-item culture" data-aos="fade-up" data-aos-delay="200">
                <img src="images/culture3.jpg" alt="Múa rối nước Việt Nam">
                <div class="gallery-overlay">
                    <h3>Múa rối nước</h3>
                    <p>Nghệ thuật dân gian độc đáo</p>
                </div>
            </div>

            <!-- Food Images -->
            <div class="gallery-item food" data-aos="fade-up">
                <img src="images/food1.jpg" alt="Phở Hà Nội">
                <div class="gallery-overlay">
                    <h3>Phở Hà Nội</h3>
                    <p>Tinh hoa ẩm thực Việt</p>
                </div>
            </div>
            <div class="gallery-item food" data-aos="fade-up" data-aos-delay="100">
                <img src="images/food2.jpg" alt="Bánh mì Sài Gòn">
                <div class="gallery-overlay">
                    <h3>Bánh mì Sài Gòn</h3>
                    <p>Món ăn đường phố trứ danh</p>
                </div>
            </div>
            <div class="gallery-item food" data-aos="fade-up" data-aos-delay="200">
                <img src="images/food3.jpg" alt="Nem rán Việt Nam">
                <div class="gallery-overlay">
                    <h3>Nem rán</h3>
                    <p>Đặc sản truyền thống</p>
                </div>
            </div>

            <!-- People Images -->
            <div class="gallery-item people" data-aos="fade-up">
                <img src="images/people1.jpg" alt="Người dân tộc Việt Nam">
                <div class="gallery-overlay">
                    <h3>Người dân tộc</h3>
                    <p>Đậm đà bản sắc</p>
                </div>
            </div>
            <div class="gallery-item people" data-aos="fade-up" data-aos-delay="100">
                <img src="images/people2.jpg" alt="Trẻ em vùng cao Việt Nam">
                <div class="gallery-overlay">
                    <h3>Trẻ em vùng cao</h3>
                    <p>Nụ cười hồn nhiên</p>
                </div>
            </div>
            <div class="gallery-item people" data-aos="fade-up" data-aos-delay="200">
                <img src="images/people3.jpg" alt="Ngư dân miền biển Việt Nam">
                <div class="gallery-overlay">
                    <h3>Ngư dân miền biển</h3>
                    <p>Cuộc sống lênh đênh sóng nước</p>
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
<button id="backToTop" title="Về đầu trang">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="js/main.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true
    });

    // Gallery Filter
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            button.classList.add('active');

            const filter = button.getAttribute('data-filter');

            galleryItems.forEach(item => {
                if (filter === 'all' || item.classList.contains(filter)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Back to Top Button
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 100) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
</body>

</html>