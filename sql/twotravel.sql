    -- USERS TABLE
    CREATE TABLE users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        avatar VARCHAR(255) DEFAULT 'images/default-avatar.jpg',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- CATEGORIES TABLE
    CREATE TABLE categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL
    );

    -- POSTS TABLE
    CREATE TABLE posts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        excerpt TEXT,
        content TEXT NOT NULL,
        category_id INT NOT NULL,
        image VARCHAR(255) NOT NULL,
        author_id INT,
        views INT DEFAULT 0,
        published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('draft', 'published', 'hidden') DEFAULT 'published',
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );

    -- TAGS TABLE
    CREATE TABLE tags (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) UNIQUE NOT NULL
    );

    -- POST_TAGS TABLE
    CREATE TABLE post_tags (
        post_id INT NOT NULL,
        tag_id INT NOT NULL,
        PRIMARY KEY (post_id, tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    );

    -- COMMENTS TABLE
    CREATE TABLE comments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        post_id INT NOT NULL,
        user_id INT,
        guest_name VARCHAR(100) NULL,
        guest_email VARCHAR(100) NULL,
        content TEXT NOT NULL,
        is_visible BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );

    -- NEWSLETTERS TABLE
    CREATE TABLE newsletters (
        id INT PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(100) UNIQUE NOT NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- INBOX TABLE
    CREATE TABLE inbox (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- ANALYTICS TABLE
    CREATE TABLE analytics (
        id INT PRIMARY KEY AUTO_INCREMENT,
        post_id INT,
        views INT DEFAULT 0,
        date DATE NOT NULL,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    );

    ALTER TABLE posts
    ADD COLUMN thumbnail VARCHAR(255) DEFAULT NULL;


    ALTER TABLE users
    ADD COLUMN bio TEXT DEFAULT NULL;

    -- ALTER TABLE posts 
    -- ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

    ALTER TABLE posts
    ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
    -- SAMPLE DATA

-- Categories
INSERT INTO categories (name, slug) VALUES
('Phong cảnh', 'nature'),
('Ẩm thực', 'food'),
('Văn hóa', 'culture'),
('Lễ hội', 'festival'),
('Du lịch tâm linh', 'spiritual-travel'),
('Nghỉ dưỡng', 'resort');

-- Users
INSERT INTO users (username, password, email, role, avatar, status) VALUES
('admin', '$2y$10$abcdefghijklmnopqrstuv', 'admin@example.com', 'admin', 'images/obito.jpg', 'active'),
('john_doe', '$2y$10$12345678901234567890', 'john@example.com', 'user', 'images/wrxdie.jpg', 'active'),
('jane_smith', '$2y$10$09876543210987654321', 'jane@example.com', 'user', 'images/mck.jpg', 'active'),
('alice_wander', '$2y$10$abcabcabcabcabcabcabc1', 'alice@example.com', 'user', 'images/alice.jpg', 'active'),
('bob_travel', '$2y$10$abcabcabcabcabcabcabc2', 'bob@example.com', 'user', 'images/bob.jpg', 'active'),
('charlie_view', '$2y$10$abcabcabcabcabcabcabc3', 'charlie@example.com', 'user', 'images/charlie.jpg', 'inactive');

-- Posts
INSERT INTO posts (title, excerpt, content, category_id, image, author_id, views, published_at, status) VALUES
('Khám Phá Đà Lạt11111 - Thành Phố Ngàn Hoa',
 'Đà Lạt nổi tiếng với khí hậu mát mẻ, những rừng thông bạt ngàn và những khu vườn hoa tuyệt đẹp.',
 'Đà Lạt - thành phố ngàn hoa với khí hậu mát mẻ quanh năm là điểm đến lý tưởng cho những ai yêu thích thiên nhiên...',
 1, 'images/dalat.jpg', 1, 1500, '2024-04-01', 'published'),

('Ẩm Thực Đường Phố Hội 1222222An',
 'Khám phá nền ẩm thực đa dạng và phong phú của phố cổ Hội An, từ mì Quảng đến cao lầu.',
 'Hội An không chỉ nổi tiếng với kiến trúc cổ kính mà còn là thiên đường ẩm thực đường phố...',
 2, 'images/hoianfood.jpg', 2, 2000, '2024-03-28', 'published'),

('Lễ Hội Áo D2ài Huế 211111024',
 'Trải nghiệm vẻ đẹp của áo dài truyền thống Việt Nam tại Festival Huế 2024.',
 'Festival Huế 2024 mang đến cho du khách cơ hội chiêm ngưỡng vẻ đẹp của áo dài Việt Nam qua các buổi trình diễn đặc sắc...',
 4, 'images/hue.jpg', 3, 1800, '2024-03-25', 'published'),

('Thiền Viện Trúc Lâm - Hành trình an yên',
 'Một trong những điểm đến tâm linh nổi bật ở Đà Lạt.',
 'Thiền viện Trúc Lâm là nơi lý tưởng cho những ai muốn tìm kiếm sự bình yên...',
 5, 'images/truclam.jpg', 4, 850, '2024-04-05', 'published'),

('Khu Nghỉ Dưỡng Vinpearl Phú Quốc - Thiên đường nghỉ dưỡng',
 'Trải nghiệm dịch vụ cao cấp giữa thiên nhiên tuyệt đẹp.',
 'Vinpearl Phú Quốc mang đến trải nghiệm đẳng cấp, hòa mình vào không gian thiên nhiên...',
 6, 'images/vinpearl.jpg', 5, 1200, '2024-04-04', 'published');

-- Comments
INSERT INTO comments (post_id, user_id, guest_name, guest_email, content, is_visible, created_at) VALUES
(1, 2, NULL, NULL, 'Đà Lạt qu222ả thực rất đẹp! Tôi đặc biệt thích những vườn hoa và khí hậu mát mẻ ở đây.', 1, '2024-04-02'),
(2, 3, NULL, NULL, 'Mì Quảng và Cao Lầ222u ở Hội An đúng là tuyệt phẩm! Tôi đã thử hết các quán được recommend trong bài.', 1, '2024-03-29'),
(3, 1, NULL, NULL, 'Festival Huế năm nay222 thật hoành tráng! Tôi rất ấn tượng với bộ sưu tập "Huế - Kinh đô Áo dài".', 1, '2024-03-26'),
(4, NULL, 'Linh Nguyễn', 'linhnguyen@gmail.com', 'Thiền viện rất yên bình, không khí trong lành, tuyệt vời để tĩnh tâm.', 1, '2024-04-06'),
(5, 2, NULL, NULL, 'Dịch vụ tốt, phòng ốc sạch sẽ và hiện đại. Rất đáng tiền!', 1, '2024-04-07');

-- Tags
INSERT INTO tags (name, slug) VALUES
('Du lịch', 'travel'),
('Ẩm thực', 'food'),
('Văn hóa', 'culture'),
('Lễ hội', 'festival'),
('Nghỉ dưỡng', 'resort'),
('Thiền', 'zen'),
('Tâm linh', 'spiritual');

-- Post Tags
INSERT INTO post_tags (post_id, tag_id) VALUES
(1, 1), (1, 3),
(2, 2), (2, 1),
(3, 4), (3, 3),
(4, 3), (4, 7), (4, 6),
(5, 1), (5, 5);

-- Newsletters
INSERT INTO newsletters (email, subscribed_at) VALUES
('user1@example.com', '2024-04-01 10:00:00'),
('user2@example.com', '2024-04-02 11:00:00'),
('wanderer@example.com', '2024-04-05 09:30:00'),
('phuotthu@example.com', '2024-04-06 10:15:00');

-- Inbox
INSERT INTO inbox (user_id, subject, message, is_read, sent_at) VALUES
(2, 'Hỏi về tour Đà Lạt', 'Tôi muốn biết thêm thông tin về tour Đà Lạt.', 0, '2024-04-03 09:00:00'),
(4, 'Tư vấn về nghỉ dưỡng Phú Quốc', 'Cho tôi hỏi chi tiết các dịch vụ tại Vinpearl Phú Quốc?', 0, '2024-04-07 14:00:00'),
(5, 'Thiền viện Trúc Lâm mở cửa khi nào?', 'Tôi muốn biết giờ mở cửa và đóng cửa.', 0, '2024-04-07 15:30:00');

-- Analytics
INSERT INTO analytics (post_id, views, date) VALUES
(1, 100, '2024-04-01'),
(2, 120, '2024-04-01'),
(3, 90, '2024-04-01'),
(4, 75, '2024-04-05'),
(5, 95, '2024-04-05');

-- Thêm dữ liệu mẫu vào bảng posts (bài viết dài hơn)
INSERT INTO posts (title, excerpt, content, category_id, image, author_id, views, published_at, status) VALUES
-- Bài viết 4
('Trải Nghiệm Vịnh Hạ Long - Kỳ Quan Thiên Nhiên Thế Giới',
 'Vịnh Hạ Long với hàng nghìn đảo đá vôi kỳ vĩ, nước biển trong xanh và các hang động tuyệt đẹp luôn làm say lòng du khách.',
 'Vịnh Hạ Long, được UNESCO công nhận là Di sản Thiên nhiên Thế giới, là điểm đến không thể bỏ lỡ tại Việt Nam. Khi đến đây, bạn sẽ có cơ hội khám phá các đảo đá vôi hùng vĩ như Đảo Titop, Hang Sửng Sốt, và tận hưởng khung cảnh hoàng hôn trên boong tàu. Ngoài ra, các hoạt động như chèo kayak, tắm biển hay thưởng thức hải sản tươi sống trên tàu sẽ mang lại trải nghiệm khó quên. Đặc biệt, du khách còn có thể tham quan làng chài nổi Cửa Vạn để hiểu thêm về đời sống ngư dân nơi đây.',
 1, 'images/halong.jpg', 1, 2200, '2024-04-05', 'published'),

-- Bài viết 5
('Ẩm Thực Huế - Tinh Hoa Đất Cố Đô',
 'Ẩm thực Huế không chỉ nổi tiếng với sự cầu kỳ mà còn thể hiện rõ nét văn hóa cung đình và dân gian.',
 'Huế được biết đến là cái nôi của nền ẩm thực cung đình Việt Nam với hơn 1700 món ăn khác nhau. Từ những món ăn cung đình như nem công chả phượng, bánh ít lá gai, đến các món dân dã như bún bò Huế, bánh bèo, bánh nậm... mỗi món đều mang một hương vị rất riêng. Du khách có thể ghé thăm các quán ăn ven sông Hương, chợ Đông Ba hay các nhà hàng truyền thống để trải nghiệm hương vị ẩm thực Huế. Hương vị đậm đà, trình bày tinh tế và sự hiếu khách của người Huế chắc chắn sẽ làm hài lòng bất kỳ thực khách nào.',
 2, 'images/huefood.jpg', 2, 3100, '2024-04-06', 'published'),

-- Bài viết 6
('Tết Nguyên Đán - Bản Sắc Văn Hóa Người Việt',
 'Tết Nguyên Đán là dịp lễ lớn nhất trong năm, phản ánh sâu sắc giá trị truyền thống và tinh thần đoàn viên của người Việt.',
 'Tết Nguyên Đán là thời điểm chuyển giao giữa năm cũ và năm mới theo âm lịch, mang theo nhiều ý nghĩa thiêng liêng trong văn hóa Việt. Trước Tết, mọi gia đình đều tất bật dọn dẹp, trang trí nhà cửa, mua sắm và chuẩn bị mâm cỗ cúng ông Công ông Táo. Trong những ngày Tết, người Việt có truyền thống đi chúc Tết họ hàng, bạn bè, mừng tuổi cho trẻ em và tham gia các lễ hội đầu năm. Mỗi vùng miền đều có nét đặc trưng riêng như bánh chưng ở miền Bắc, bánh tét ở miền Nam, hay các trò chơi dân gian, múa lân sư rồng. Dù ở đâu, Tết vẫn là dịp để mỗi người Việt hướng về cội nguồn, gìn giữ truyền thống và cầu mong một năm mới bình an, thịnh vượng.',
 3, 'images/tet.jpg', 3, 2900, '2024-04-07', 'published');
