function displayPosts(postsToShow = posts) {
    const postsContainer = document.getElementById('posts-container');
    if (!postsContainer) return;

    postsContainer.innerHTML = '';

    postsToShow.forEach(post => {
        const postElement = document.createElement('div');
        postElement.className = 'col-lg-4 col-md-6';
        postElement.innerHTML = `
            <article class="post-card" data-category="${post.category}">
                <div class="post-card__image">
                    <img src="${post.image}" alt="${post.title}">
                    <span class="post-card__category">${getCategoryName(post.category)}</span>
                </div>
                <div class="post-card__content">
                    <div class="post-card__meta">
                        <span><i class="bi bi-calendar"></i>${formatDate(post.publishedAt)}</span>
                        <span><i class="bi bi-eye"></i>${post.views} lượt xem</span>
                        <span><i class="bi bi-chat"></i>${post.comments} bình luận</span>
                    </div>
                    <h3 class="post-card__title">
                        <a href="blog-detail.php?id=${post.id}">${post.title}</a>
                    </h3>
                    <p class="post-card__excerpt">${post.excerpt}</p>
                    <div class="post-card__footer">
                        <div class="post-card__author">
                            <div class="post-card__author-info">
                                <div class="post-card__author-name">${post.author.name}</div>
                            </div>
                        </div>
                        <a href="blog-detail.php?id=${post.id}" class="read-more">Đọc thêm <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </article>
        `;
        postsContainer.appendChild(postElement);
    });
}

function checkLogin(username, password) {
    return users.find(user => user.username === username && user.password === password);
}

function registerUser(username, password) {
    if (users.some(user => user.username === username)) {
        return { success: false, message: 'Tên người dùng đã tồn tại' };
    }

    const newUser = {
        id: users.length + 1,
        username,
        password,
        role: 'user'
    };

    users.push(newUser);
    return { success: true, message: 'Đăng ký thành công' };
}

function addPost(title, content) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const newPost = {
        id: posts.length + 1,
        title,
        content,
        authorId: currentUser.id,
        publishedAt: new Date().toISOString().split('T')[0],
        comments: []
    };

    posts.push(newPost);
    return true;
}

function editPost(id, title, content) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const post = posts.find(p => p.id === id);
    if (!post) return false;

    post.title = title;
    post.content = content;
    return true;
}

function deletePost(id) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const index = posts.findIndex(p => p.id === id);
    if (index === -1) return false;

    posts.splice(index, 1);
    return true;
}

function toggleCommentVisibility(id) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const comment = comments.find(c => c.id === id);
    if (!comment) return false;

    comment.isVisible = !comment.isVisible;
    return true;
}

function deleteComment(id) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const index = comments.findIndex(c => c.id === id);
    if (index === -1) return false;

    comments.splice(index, 1);
    return true;
}

function editUser(id, role) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const user = users.find(u => u.id === id);
    if (!user) return false;

    user.role = role;
    return true;
}

function deleteUser(id) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser || currentUser.role !== 'admin') return false;

    const index = users.findIndex(u => u.id === id);
    if (index === -1) return false;

    users.splice(index, 1);
    return true;
}

function getCategoryName(category) {
    const categories = {
        'nature': 'Phong cảnh',
        'food': 'Ẩm thực',
        'culture': 'Văn hóa',
        'festival': 'Lễ hội'
    };
    return categories[category] || category;
}

function formatDate(dateString) {
    const options = { day: 'numeric', month: 'numeric', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('vi-VN', options);
}

function setupFilter() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.post-card, .gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            items.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else if (item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

function setupSearch() {
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

        // Hiển thị loading
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
                console.log('Kết quả tìm kiếm:', result);

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
                                <img src="${post.image}" alt="${post.title}">
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

    // Xử lý tìm kiếm khi nhấn nút
    searchButton.addEventListener('click', performSearch);

    // Xử lý tìm kiếm khi nhấn Enter
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Tìm kiếm tự động sau khi người dùng ngừng gõ
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    console.log('Đã khởi tạo chức năng tìm kiếm');
}

// Đảm bảo hàm được gọi khi trang tải xong
document.addEventListener('DOMContentLoaded', function() {
    initSearch();
});

function setupSort() {
    const sortSelect = document.querySelector('.sort-select');
    if (!sortSelect) return;

    sortSelect.addEventListener('change', (e) => {
        const sortBy = e.target.value;
        const sortedPosts = [...posts].sort((a, b) => {
            if (sortBy === 'newest') {
                return new Date(b.publishedAt) - new Date(a.publishedAt);
            } else if (sortBy === 'popular') {
                return b.views - a.views;
            }
            return 0;
        });
        displayPosts(sortedPosts);
    });
}

function initBlog() {
    displayPosts();
    setupFilter();
    setupSearch();
    setupSort();
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        heroContent.style.opacity = '1';
        heroContent.style.transform = 'translateY(0)';
    }
}

function initBlogDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = parseInt(urlParams.get('id'));

    const post = posts.find(p => p.id === postId);
    
    if (!post) {
        window.location.href = 'blog.php';
        return;
    }

    document.title = `${post.title} - Travel Blog`;
    document.getElementById('blog-title').textContent = post.title;
    document.getElementById('blog-author').textContent = post.author.name;
    document.getElementById('blog-date').textContent = formatDate(post.publishedAt);
    document.getElementById('blog-views').textContent = `${post.views} lượt xem`;
    document.getElementById('blog-comments').textContent = `${post.comments} bình luận`;
    document.getElementById('blog-image').src = post.image;
    document.getElementById('blog-image').alt = post.title;
    document.getElementById('blog-content').innerHTML = `<p>${post.content}</p>`;
    document.getElementById('author-avatar').src = post.author.avatar;
    document.getElementById('author-name').textContent = post.author.name;

    displayComments(postId);
    displayRelatedPosts(post.category, postId);
    displayPopularPosts(postId);
}

function displayComments(postId) {
    const postComments = comments.filter(c => c.postId === postId && c.isVisible);
    const commentsContainer = document.getElementById('comments-list');
    const commentsCount = document.getElementById('comments-count');

    if (commentsContainer && commentsCount) {
        commentsContainer.innerHTML = `
            <div class="text-center">
                <div class="loading-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <p class="mt-2">Đang tải bình luận...</p>
            </div>
        `;

        setTimeout(() => {
            commentsCount.textContent = postComments.length;
            
            if (postComments.length === 0) {
                commentsContainer.innerHTML = `
                    <div class="text-center">
                        <i class="bi bi-chat-dots" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="mt-3">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                    </div>
                `;
                return;
            }

            commentsContainer.innerHTML = postComments.map((comment, index) => `
                <div class="comment" style="animation-delay: ${index * 0.1}s">
                    <div class="comment__content">
                        <div class="comment__author">
                            ${users.find(u => u.id === comment.userId)?.username || 'Người dùng ẩn danh'}
                        </div>
                        <div class="comment__date">
                            <i class="bi bi-clock"></i> ${formatDate(comment.createdAt)}
                        </div>
                        <div class="comment__text">${comment.content}</div>
                    </div>
                </div>
            `).join('');

            const commentForm = document.getElementById('comment-form');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = document.getElementById('comment-name').value;
                    const email = document.getElementById('comment-email').value;
                    const content = document.getElementById('comment-content').value;

                    if (name && email && content) {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = `
                            <div class="loading-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        `;
                        submitBtn.disabled = true;

                        setTimeout(() => {
                            const newComment = {
                                id: comments.length + 1,
                                postId: postId,
                                userId: 1,
                                content: content,
                                createdAt: new Date().toISOString(),
                                isVisible: true
                            };
                            comments.push(newComment);
                            displayComments(postId);
                            commentForm.reset();
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;

                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success mt-3';
                            alert.innerHTML = 'Bình luận của bạn đã được gửi thành công!';
                            commentForm.appendChild(alert);
                            setTimeout(() => alert.remove(), 3000);
                        }, 1500);
                    }
                });
            }
        }, 1000);
    }
}

function displayRelatedPosts(category, currentPostId) {
    const relatedPosts = posts
        .filter(p => p.category === category && p.id !== currentPostId)
        .slice(0, 3);

    const relatedContainer = document.getElementById('related-posts');
    if (relatedContainer) {
        relatedContainer.innerHTML = relatedPosts.map(post => `
            <div class="post-related__item">
                <div class="post-related__image">
                    <a href="blog-detail.php?id=${post.id}">
                        <img src="${post.image}" alt="${post.title}">
                    </a>
                </div>
                <div class="post-related__content">
                    <h4><a href="blog-detail.php?id=${post.id}">${post.title}</a></h4>
                    <div class="post-related__meta">
                        <span><i class="bi bi-calendar"></i>${formatDate(post.publishedAt)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

function displayPopularPosts(currentPostId) {
    const popularPosts = posts
        .filter(p => p.id !== currentPostId)
        .sort((a, b) => b.views - a.views)
        .slice(0, 3);

    const popularContainer = document.getElementById('popular-posts');
    if (popularContainer) {
        popularContainer.innerHTML = popularPosts.map(post => `
            <div class="popular-post">
                <div class="popular-post__image">
                    <a href="blog-detail.php?id=${post.id}">
                        <img src="${post.image}" alt="${post.title}">
                    </a>
                </div>
                <div class="popular-post__content">
                    <h4><a href="blog-detail.php?id=${post.id}">${post.title}</a></h4>
                    <div class="popular-post__meta">
                        <span><i class="bi bi-calendar"></i>${formatDate(post.publishedAt)}</span>
                        <span><i class="bi bi-eye"></i>${post.views} lượt xem</span>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

function setupGalleryFilter() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            galleryItems.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else if (item.classList.contains(filterValue)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

let lastScrollTop = 0;

function getCurrentPage() {
    return window.location.pathname.split('/').pop();
}

function initAboutPage() {
    console.log('Initializing About Page...');
    
    // Khởi tạo AOS
    AOS.init({
        duration: 1000,
        once: true
    });

    // Khởi tạo counter animation
    const counters = document.querySelectorAll('.stat-counter');
    
    const startCounting = (counter) => {
        const target = parseInt(counter.getAttribute('data-target'));
        let count = 0;
        const speed = target / 200; // Tốc độ đếm

        const updateCount = () => {
            if (count < target) {
                count += speed;
                counter.innerText = Math.ceil(count);
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target;
            }
        };

        updateCount();
    };

    // Sử dụng Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounting(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    // Observe tất cả các counter
    counters.forEach(counter => observer.observe(counter));
}

// Thêm vào cuối file
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname;
    if (currentPage.includes('about.php')) {
        console.log('About page detected, initializing...');
        initAboutPage();
    }
});

function initCommonFeatures() {
    // Khởi tạo loading overlay
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 500);
    }

    // Khởi tạo AOS
    AOS.init({
        duration: 1000,
        once: true
    });

    // Khởi tạo back to top button
    initBackToTop();

    // Khởi tạo smooth scroll
    initSmoothScroll();

    // Khởi tạo navbar scroll effect
    initNavbarScroll();
}

function checkLoginStatus() {
    console.log('Checking login status...');
    const user = JSON.parse(localStorage.getItem('currentUser'));
    console.log('Current user:', user);
    
    if (user) {
        console.log('User is logged in, updating UI...');
        updateUIForLoggedInUser(user);
        return true;
    } else {
        console.log('No user logged in, updating UI for logged out state...');
        updateUIForLoggedOutUser();
        return false;
    }
}

function updateUIForLoggedInUser(user) {
    console.log('Updating UI for logged in user:', user);
    
    // Xác định đường dẫn cơ sở
    const currentPath = getCurrentPage();
    const basePath = currentPath.includes('admin/') ? '../' : '';
    
    // Avatar mặc định và theme color
    const defaultAvatar = user.avatar || `${basePath}images/wrxdie.jpg`;
    const themeColor = user.username === 'jane_smith' ? 'red' : 'green';
    
    // Cập nhật navigation buttons
    const navButtons = document.querySelector('.nav-buttons');
    console.log('Nav buttons element:', navButtons);
    
    if (navButtons) {
        const userMenuHTML = `
            <div class="user-info d-flex align-items-center" data-theme="${themeColor}">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle d-flex align-items-center gap-2" 
                            type="button" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <img src="${defaultAvatar}" alt="User Avatar" width="32" height="32" class="rounded-circle">
                        <span class="username">${user.username}</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li class="user-profile">
                            <img src="${defaultAvatar}" alt="User Avatar">
                            <div class="user-info">
                                <span class="username">${user.username}</span>
                                <span class="user-role">${user.role === 'admin' ? 'Quản trị viên' : 'Thành viên'}</span>
                            </div>
                        </li>
                        <li><a class="dropdown-item" href="${basePath}profile.php">
                            <i class="bi bi-person-circle"></i>
                            Chỉnh sửa hồ sơ
                        </a></li>
                        <li><a class="dropdown-item" href="${basePath}inbox.php">
                            <i class="bi bi-envelope"></i>
                            Hộp thư đến
                        </a></li>
                        <li><a class="dropdown-item" href="${basePath}analytics.php">
                            <i class="bi bi-graph-up"></i>
                            Thống kê
                        </a></li>
                        <li><a class="dropdown-item" href="${basePath}settings.php">
                            <i class="bi bi-gear"></i>
                            Cài đặt
                        </a></li>
                        <li><a class="dropdown-item" href="${basePath}help.php">
                            <i class="bi bi-question-circle"></i>
                            Trợ giúp & Hỗ trợ
                        </a></li>
                        ${user.role === 'admin' ? `
                        <li><a class="dropdown-item" href="${basePath}admin/index.php">
                            <i class="bi bi-speedometer2"></i>
                            Quản trị
                        </a></li>
                        ` : ''}
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            Đăng xuất
                        </button></li>
                    </ul>
                </div>
            </div>
        `;
        
        navButtons.innerHTML = userMenuHTML;
        
        // Thêm event listener cho nút đăng xuất
        const logoutBtn = navButtons.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', handleLogout);
        }
    }

    // Cập nhật hiển thị các phần tử dựa trên vai trò
    document.querySelectorAll('.admin-only').forEach(item => {
        item.style.display = user.role === 'admin' ? 'block' : 'none';
    });

    document.querySelectorAll('.user-only').forEach(item => {
        item.style.display = 'block';
    });

    document.querySelectorAll('.login-btn, .register-btn').forEach(button => {
        button.style.display = 'none';
    });
}

function updateUIForLoggedOutUser() {
    console.log('Updating UI for logged out user');
    
    const navButtons = document.querySelector('.nav-buttons');
    if (navButtons) {
        const currentPath = getCurrentPage();
        const basePath = currentPath.includes('admin/') ? '../' : '';
        
        navButtons.innerHTML = `
            <a href="${basePath}login.php" class="btn btn-outline-primary me-2 login-btn">Đăng nhập</a>
            <a href="${basePath}register.php" class="btn btn-primary register-btn">Đăng ký</a>
        `;
    }

    // Ẩn các phần tử yêu cầu đăng nhập
    document.querySelectorAll('.admin-only, .user-only').forEach(item => {
        item.style.display = 'none';
    });
}

function handleLogout() {
    console.log('Handling logout...');
    localStorage.removeItem('currentUser');
    
    const currentPath = getCurrentPage();
    const basePath = currentPath.includes('admin/') ? '../' : '';
    
    // Cập nhật UI trước khi chuyển hướng
    updateUIForLoggedOutUser();
    
    // Chuyển hướng về trang chủ nếu cần
    if (currentPath.includes('admin/') || currentPath === 'profile.php') {
        window.location.href = `${basePath}index.php`;
    } else {
        window.location.reload();
    }
}

window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

const backToTop = document.getElementById('backToTop');
if (backToTop) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
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
}

const heroSwiper = document.querySelector('.hero-swiper');
if (heroSwiper) {
    new Swiper('.hero-swiper', {
        loop: true,
        effect: 'fade',
        speed: 1000,
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
}

window.addEventListener('load', () => {
    AOS.refresh();
});

function initContact() {
    // Xác thực biểu mẫu và hành động
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đã Gửi Thành Công';
            button.classList.add('btn-success');
            setTimeout(() => {
                button.innerHTML = '<i class="bi bi-send me-2"></i>Gửi Tin Nhắn';
                button.classList.remove('btn-success');
            }, 3000);
        });
    }
}

// Auth Functions
function initAuth() {
    console.log('Initializing auth...');
    
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        console.log('Login form found');
        loginForm.addEventListener('submit', handleLogin);
        checkLoginStatus(); // Kiểm tra trạng thái đăng nhập khi tải trang
    } else {
        console.log('Login form not found');
    }

    if (registerForm) {
        console.log('Register form found');
        registerForm.addEventListener('submit', handleRegister);
    } else {
        console.log('Register form not found');
    }

    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
}

function handleLogin(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    console.log('Attempting login with:', { username, password });
    console.log('Available users:', users);
    
    // Tìm kiếm người dùng trong mảng users
    const user = users.find(u => u.username === username && u.password === password);
    
    console.log('Found user:', user);
    
    if (user) {
        // Lưu thông tin người dùng vào localStorage
        localStorage.setItem('currentUser', JSON.stringify(user));
        
        // Hiển thị thông báo thành công
        const submitBtn = document.querySelector('#loginForm button[type="submit"]');
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đăng nhập thành công';
        submitBtn.classList.add('btn-success');
        
        // Chuyển hướng sau 1 giây
        setTimeout(() => {
            console.log('Redirecting user with role:', user.role);
            if (user.role === 'admin') {
                window.location.href = 'admin/index.php';
            } else {
                window.location.href = 'index.php';
            }
        }, 1000);
    } else {
        // Hiển thị thông báo lỗi
        alert('Tên đăng nhập hoặc mật khẩu không đúng!');
    }
}

function handleRegister(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        alert('Mật khẩu xác nhận không khớp!');
        return;
    }
    
    const result = registerUser(username, password);
    
    if (result.success) {
        alert(result.message);
        window.location.href = 'login.php';
    } else {
        alert(result.message);
    }
}

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

        // Hiển thị loading
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
                console.log('Kết quả tìm kiếm:', result);

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
                                <img src="${post.image}" alt="${post.title}">
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

    // Xử lý tìm kiếm khi nhấn nút
    searchButton.addEventListener('click', performSearch);

    // Xử lý tìm kiếm khi nhấn Enter
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Tìm kiếm tự động sau khi người dùng ngừng gõ
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    console.log('Đã khởi tạo chức năng tìm kiếm');
}

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý lỗi ảnh
    const images = document.querySelectorAll('.post-card__image img, .post-image img');
    images.forEach(img => {
        img.onerror = function() {
            this.src = 'images/default-thumbnail.jpg';
            this.classList.add('error');
        };
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý lỗi ảnh
    const images = document.querySelectorAll('.card-img-top');
    images.forEach(img => {
        img.onerror = function() {
            this.src = 'images/default-thumbnail.jpg';
            this.classList.add('error');
        };
    });
});

// Thêm các hàm xử lý tìm kiếm và lọc
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

function sortPosts(sortBy) {
    const searchResults = document.getElementById('searchResults');
    const searchResultsSection = document.querySelector('.search-results');

    fetch(`sort.php?sort=${encodeURIComponent(sortBy)}`)
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
        sortPosts(this.value);
    });
});