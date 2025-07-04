INSERT INTO users (username, password, email, role, avatar, status, bio) VALUES
('admin1', 'hashed_password1', 'admin1@example.com', 'admin', 'images/admin1.jpg', 'active', 'Quản trị viên hệ thống, yêu thích du lịch và khám phá văn hóa.'),
('user1', 'hashed_password2', 'user1@example.com', 'user', 'images/user1.jpg', 'active', 'Yêu thích du lịch và ẩm thực Việt Nam.'),
('user2', 'hashed_password3', 'user2@example.com', 'user', 'images/user2.jpg', 'active', 'Người đam mê nhiếp ảnh và khám phá thiên nhiên.'),
('user3', 'hashed_password4', 'user3@example.com', 'user', 'images/user3.jpg', 'inactive', 'Thích viết lách và chia sẻ kinh nghiệm du lịch.'),
('user4', 'hashed_password5', 'user4@example.com', 'user', 'images/user4.jpg', 'active', 'Nhà báo tự do, yêu thích khám phá các điểm đến mới.');

INSERT INTO categories (name, slug) VALUES
('Du lịch', 'du-lich'),
('Ẩm thực', 'am-thuc'),
('Văn hóa', 'van-hoa'),
('Thiên nhiên', 'thien-nhien'),
('Lịch sử', 'lich-su');

INSERT INTO posts (title, excerpt, content, category_id, image, author_id, views, status, thumbnail) VALUES
('Khám phá Đà Lạt mộng mơ', 'Hành trình khám phá thành phố ngàn hoa với khí hậu mát mẻ và cảnh quan tuyệt đẹp.', 'Đà Lạt, thành phố ngàn hoa, là điểm đến lý tưởng cho những ai yêu thích thiên nhiên và sự yên bình. Nằm trên cao nguyên Lâm Viên, Đà Lạt nổi tiếng với khí hậu mát mẻ quanh năm, những đồi thông xanh mướt và các hồ nước thơ mộng như hồ Xuân Hương, hồ Tuyền Lâm. Du khách có thể ghé thăm Thung Lũng Tình Yêu, nơi lãng mạn với những con đường hoa rực rỡ, hay đồi chè Cầu Đất xanh ngút ngàn. Chợ đêm Đà Lạt là nơi tuyệt vời để thưởng thức các món ăn đường phố như bánh tráng nướng, sữa đậu nành nóng. Những quán cà phê view đồi núi như Horizon Coffee hay The Wilder-nest mang đến không gian thư giãn, lý tưởng để ngắm hoàng hôn. Đừng quên khám phá các làng hoa Vạn Thành, nơi bạn có thể chiêm ngưỡng muôn vàn loài hoa khoe sắc. Đà Lạt còn có các công trình kiến trúc độc đáo như nhà ga Đà Lạt, mang đậm dấu ấn Pháp. Một chuyến đi Đà Lạt sẽ để lại trong bạn những kỷ niệm khó quên về một thành phố mộng mơ.', 1, 'images/dalat.jpg', 1, 150, 'published', 'images/dalat.jpg'),

('Đà Nẵng – Thành phố đáng sống', 'Khám phá vẻ đẹp hiện đại và thiên nhiên hòa quyện của Đà Nẵng.', 'Đà Nẵng, thành phố biển năng động, là điểm đến hoàn hảo với sự kết hợp giữa thiên nhiên và hiện đại. Bãi biển Mỹ Khê, một trong những bãi biển đẹp nhất hành tinh, thu hút du khách với cát trắng mịn và làn nước trong xanh. Cầu Rồng, biểu tượng của thành phố, nổi bật với thiết kế độc đáo và khả năng phun lửa, nước vào cuối tuần. Ngũ Hành Sơn, với những hang động kỳ bí và chùa chiền cổ kính, là nơi lý tưởng để khám phá văn hóa và tâm linh. Bà Nà Hills, “Đà Lạt thu nhỏ”, mang đến không gian châu Âu cổ kính với cáp treo dài nhất thế giới. Du khách có thể thưởng thức ẩm thực Đà Nẵng với các món đặc sản như mì Quảng, bánh tráng cuốn thịt heo. Chợ Cồn và chợ Hàn là nơi tuyệt vời để trải nghiệm văn hóa địa phương. Với sự phát triển mạnh mẽ nhưng vẫn giữ được nét quyến rũ tự nhiên, Đà Nẵng xứng đáng là điểm đến không thể bỏ qua cho mọi du khách.', 1, 'images/danang.jpg', 2, 200, 'published', 'images/danang.jpg'),

('Vịnh Hạ Long – Kỳ quan thiên nhiên', 'Khám phá vẻ đẹp hùng vĩ của Vịnh Hạ Long, di sản thế giới UNESCO.', 'Vịnh Hạ Long, di sản thiên nhiên thế giới, là một kiệt tác của tạo hóa với hàng nghìn đảo đá vôi độc đáo nhô lên từ mặt biển xanh ngọc. Du thuyền trên vịnh, bạn sẽ bị mê hoặc bởi các hang động kỳ bí như hang Sửng Sốt, động Thiên Cung, hay đảo Titop với bãi biển tuyệt đẹp. Hạ Long không chỉ có cảnh quan thiên nhiên mà còn mang giá trị văn hóa, lịch sử với những câu chuyện truyền thuyết về rồng mẹ, rồng con. Du khách có thể tham gia chèo kayak để khám phá các góc khuất của vịnh hoặc ngắm toàn cảnh từ thủy phi cơ. Ẩm thực Hạ Long cũng là điểm nhấn với các món hải sản tươi ngon như tôm, cua, mực. Đêm đến, ngủ trên du thuyền dưới bầu trời đầy sao là trải nghiệm khó quên. Vịnh Hạ Long không chỉ là điểm đến du lịch mà còn là nơi khơi nguồn cảm hứng cho những ai yêu thiên nhiên và khám phá.', 4, 'images/halong.jpg', 3, 180, 'published', 'images/halong.jpg'),

('Hà Nội – Thủ đô ngàn năm văn hiến', 'Khám phá Hà Nội với bề dày lịch sử và văn hóa đặc sắc.', 'Hà Nội, trái tim của Việt Nam, là nơi giao thoa giữa truyền thống và hiện đại. Hồ Hoàn Kiếm với cầu Thê Húc đỏ rực dẫn vào đền Ngọc Sơn là biểu tượng của thủ đô. Văn Miếu – Quốc Tử Giám, trường đại học đầu tiên của Việt Nam, lưu giữ giá trị lịch sử và văn hóa lâu đời. Phố cổ Hà Nội, với những con phố nhỏ nhộn nhịp, là nơi bạn có thể cảm nhận rõ nét hồn xưa của thành phố. Ẩm thực Hà Nội là điểm nhấn với phở, bún chả, bánh cuốn, và cà phê trứng nổi tiếng. Lăng Chủ tịch Hồ Chí Minh và Quảng trường Ba Đình là nơi không thể bỏ qua khi tìm hiểu về lịch sử Việt Nam. Hà Nội còn có những khu chợ đêm sôi động, những quán cà phê mang phong cách hoài cổ. Mỗi góc phố, mỗi con đường ở Hà Nội đều kể những câu chuyện riêng, khiến du khách không khỏi lưu luyến khi rời xa.', 3, 'images/hanoi.jpg', 1, 220, 'published', 'images/hanoi.jpg'),

('Hội An – Phố cổ quyến rũ', 'Khám phá vẻ đẹp cổ kính và bình yên của phố cổ Hội An.', 'Hội An, di sản văn hóa thế giới, là bức tranh sống động về một thương cảng sầm uất từ thế kỷ 16. Những con phố nhỏ với nhà cổ vàng rực, đèn lồng lung linh tạo nên một không gian thơ mộng. Chùa Cầu, biểu tượng của Hội An, là nơi giao thoa văn hóa Việt – Nhật – Hoa. Du khách có thể thả hoa đăng trên sông Hoài, ngắm nhìn phố cổ về đêm. Hội An còn nổi tiếng với làng nghề truyền thống như gốm Thanh Hà, làng mộc Kim Bồng. Ẩm thực Hội An là điểm nhấn với cao lầu, cơm gà, bánh mì Phượng trứ danh. Đạp xe quanh những con đường làng quê yên bình hoặc tham quan Cù Lao Chàm là trải nghiệm tuyệt vời. Hội An không chỉ là điểm đến du lịch mà còn là nơi để cảm nhận sự giao thoa văn hóa và sự tĩnh lặng của thời gian.', 3, 'images/hoian.jpg', 4, 170, 'published', 'images/hoian.jpg'),

-- Các bài viết còn lại (rút gọn nội dung để ngắn gọn)
('Ẩm thực Hội An – Hương vị độc đáo', 'Thưởng thức các món ăn đặc sắc của phố cổ Hội An.', 'Hội An không chỉ nổi tiếng với vẻ đẹp cổ kính mà còn là thiên đường ẩm thực. Cao lầu, món ăn đặc trưng với sợi mì dai giòn, thịt heo và rau sống, mang hương vị độc đáo. Cơm gà Hội An, với gà xé mềm và cơm dẻo thơm, là món không thể bỏ qua. Bánh mì Phượng, nổi tiếng thế giới, thu hút thực khách bởi lớp vỏ giòn và nhân đậm đà. Ngoài ra, chè bắp, hoành thánh, và bánh bao – bánh vạc cũng là những món ăn đáng thử. Các quán ăn ven sông Hoài mang đến không gian thưởng thức ẩm thực đầy thơ mộng. Hội An là nơi bạn không chỉ ăn bằng vị giác mà còn cảm nhận bằng trái tim.', 2, 'images/hoianfood.jpg', 2, 190, 'published', 'images/hoianfood.jpg'),
('Huế – Vẻ đẹp cố đô', 'Khám phá nét trầm mặc và lịch sử của cố đô Huế.', 'Huế, cố đô Việt Nam, mang vẻ đẹp trầm mặc với những di tích lịch sử như Kinh thành Huế, lăng Tự Đức, lăng Khải Định. Sông Hương thơ mộng và cầu Trường Tiền là biểu tượng của thành phố. Ẩm thực Huế với bún bò, bánh bèo, bánh nậm là điểm nhấn khó quên. Du khách có thể chèo thuyền nghe ca Huế trên sông Hương, tận hưởng không gian văn hóa đặc sắc.', 3, 'images/hue.jpg', 3, 160, 'published', 'images/hue.jpg'),
('Phú Quốc – Thiên đường biển đảo', 'Khám phá vẻ đẹp hoang sơ của đảo ngọc Phú Quốc.', 'Phú Quốc, hòn đảo ngọc, nổi tiếng với những bãi biển cát trắng như Bãi Sao, Bãi Dài. Du khách có thể lặn ngắm san hô, khám phá làng chài Hàm Ninh, hay thưởng thức nước mắm nổi tiếng. Sunset Sanato mang đến trải nghiệm ngắm hoàng hôn tuyệt đẹp.', 1, 'images/phuquoc.jpg', 1, 210, 'published', 'images/phuquoc.jpg'),
('Sài Gòn – Thành phố không ngủ', 'Khám phá nhịp sống sôi động của TP. Hồ Chí Minh.', 'Sài Gòn, trung tâm kinh tế sôi động, là nơi giao thoa văn hóa và hiện đại. Nhà thờ Đức Bà, Dinh Độc Lập, và chợ Bến Thành là những điểm đến nổi bật. Ẩm thực đường phố như bánh xèo, hủ tiếu, phá lấu làm say lòng du khách.', 1, 'images/saigon.jpg', 4, 230, 'published', 'images/saigon.jpg'),
('Sapa – Vùng đất sương mù', 'Khám phá vẻ đẹp núi non hùng vĩ của Sapa.', 'Sapa, thị trấn sương mù, nổi tiếng với khí hậu mát mẻ và cảnh quan núi non hùng vĩ. Đỉnh Fansipan, bản Cát Cát, và chợ đêm Sapa là những điểm đến hấp dẫn. Ẩm thực Sapa với thắng cố, thịt nướng làm say lòng du khách.', 4, 'images/sapa.jpg', 2, 140, 'published', 'images/sapa.jpg'),
('Ninh Bình – Vẻ đẹp non nước', 'Khám phá Ninh Bình với cảnh quan thiên nhiên tuyệt đẹp.', 'Ninh Bình, vùng đất “Hạ Long trên cạn”, nổi tiếng với Tràng An, Tam Cốc – Bích Động. Hang Múa và chùa Bái Đính là điểm đến không thể bỏ qua. Ẩm thực với thịt dê và cơm cháy là đặc sản.', 4, 'images/ninhbinh.jpg', 3, 175, 'published', 'images/ninhbinh.jpg'),
('Nha Trang – Viên ngọc biển', 'Khám phá vẻ đẹp biển cả của Nha Trang.', 'Nha Trang, thành phố biển, nổi tiếng với vịnh biển đẹp và các đảo như Hòn Mun, Hòn Tằm. Vinpearl Land và tháp Bà Ponagar là điểm nhấn. Hải sản tươi ngon là điều không thể bỏ qua.', 1, 'images/nhatrang.jpg', 1, 195, 'published', 'images/nhatrang.jpg'),
('Cần Thơ – Miền Tây sông nước', 'Khám phá vẻ đẹp sông nước của Cần Thơ.', 'Cần Thơ, thủ phủ miền Tây, nổi tiếng với chợ nổi Cái Răng và bến Ninh Kiều. Du khách có thể khám phá vườn cò Bằng Lăng và thưởng thức bánh xèo miền Tây.', 1, 'images/cantho.jpg', 4, 165, 'published', 'images/cantho.jpg'),
('An Giang – Vùng đất tâm linh', 'Khám phá vẻ đẹp độc đáo của An Giang.', 'An Giang nổi tiếng với rừng tràm Trà Sư, chợ nổi Long Xuyên, và miếu Bà Chúa Xứ. Văn hóa Chăm và cảnh quan sông nước là điểm nhấn đặc biệt.', 1, 'images/angiang.jpg', 2, 155, 'published', 'images/angiang.jpg'),
('Tiền Giang – Vùng đất trái ngọt', 'Khám phá miền Tây với Tiền Giang.', 'Tiền Giang nổi tiếng với chợ nổi Cái Bè và các vườn trái cây ngọt ngào. Chùa Vĩnh Nghiêm và lò gốm Tân Phước là điểm đến thú vị.', 1, 'images/tiengiang.jpg', 3, 145, 'published', 'images/tiengiang.jpg'),
('Nghệ An – Quê hương Bác Hồ', 'Khám phá vùng đất lịch sử Nghệ An.', 'Nghệ An, quê hương Chủ tịch Hồ Chí Minh, nổi tiếng với làng Sen, làng Kim Liên. Biển Cửa Lò và các món ăn như cháo lươn, bánh mướt là điểm nhấn.', 3, 'images/nghean.jpg', 1, 160, 'published', 'images/nghean.jpg'),
('Mộc Châu – Cao nguyên xanh', 'Khám phá vẻ đẹp thiên nhiên của Mộc Châu.', 'Mộc Châu nổi tiếng với đồi chè trái tim, thác Dải Yếm, và rừng thông Bản Áng. Mùa hoa mận, hoa cải trắng làm say lòng du khách.', 4, 'images/mocchau.jpg', 4, 150, 'published', 'images/mocchau.jpg'),
('Thái Nguyên – Vùng đất chè', 'Khám phá Thái Nguyên với văn hóa chè nổi tiếng.', 'Thái Nguyên, thủ phủ chè Việt Nam, nổi tiếng với đồi chè Tân Cương. Hồ Núi Cốc và bảo tàng Văn hóa các dân tộc là điểm đến hấp dẫn.', 1, 'images/thainguyen.jpg', 2, 140, 'published', 'images/thainguyen.jpg'),
('Bắc Cạn – Vẻ đẹp hoang sơ', 'Khám phá thiên nhiên hoang sơ của Bắc Cạn.', 'Bắc Cạn nổi tiếng với hồ Ba Bể, một trong những hồ nước ngọt đẹp nhất Việt Nam. Thác Bạc và động Nàng Tiên là điểm đến lý tưởng cho yêu thiên nhiên.', 4, 'images/baccan.jpg', 3, 130, 'published', 'images/baccan.jpg'),
('Thái Bình – Vùng đất lúa', 'Khám phá văn hóa đồng bằng ở Thái Bình.', 'Thái Bình, vùng đất lúa, nổi tiếng với chùa Keo và làng nghề truyền thống. Ẩm thực với bánh cáy và bún bung là điểm nhấn đặc sắc.', 3, 'images/thaibinh.jpg', 1, 120, 'published', 'images/thaibinh.jpg');

INSERT INTO comments (post_id, user_id, guest_name, guest_email, content, is_visible, created_at) VALUES
(1, 2, NULL, NULL, 'Đà Lạt thật sự là nơi tuyệt vời để thư giãn!', TRUE, '2025-06-01 10:00:00'),
(1, NULL, 'Nguyen Van A', 'nguyenvana@example.com', 'Bánh tráng nướng ở chợ đêm ngon tuyệt!', TRUE, '2025-06-01 11:00:00'),
(2, 3, NULL, NULL, 'Cầu Rồng phun lửa đẹp mê hồn!', TRUE, '2025-06-02 12:00:00'),
(2, NULL, 'Tran Thi B', 'tranthib@example.com', 'Mì Quảng ở Đà Nẵng là nhất!', TRUE, '2025-06-02 13:00:00'),
(3, 4, NULL, NULL, 'Vịnh Hạ Long đẹp như tranh vẽ!', TRUE, '2025-06-03 14:00:00'),
(3, NULL, 'Le Van C', 'levanc@example.com', 'Chèo kayak ở Hạ Long rất thú vị.', TRUE, '2025-06-03 15:00:00'),
(4, 1, NULL, NULL, 'Hà Nội mùa thu thật lãng mạn!', TRUE, '2025-06-04 16:00:00'),
(4, NULL, 'Pham Thi D', 'phamthid@example.com', 'Phở Hà Nội đúng là đặc sản.', TRUE, '2025-06-04 17:00:00'),
(5, 2, NULL, NULL, 'Hội An về đêm đẹp như cổ tích!', TRUE, '2025-06-05 18:00:00'),
(5, NULL, 'Hoang Van E', 'hoangvane@example.com', 'Thả đèn hoa đăng ở Hội An rất thú vị.', TRUE, '2025-06-05 19:00:00'),
(6, 3, NULL, NULL, 'Cao lầu Hội An ngon tuyệt!', TRUE, '2025-06-06 20:00:00'),
(6, NULL, 'Nguyen Thi F', 'nguyenthif@example.com', 'Bánh mì Phượng đáng thử lắm!', TRUE, '2025-06-06 21:00:00'),
(7, 4, NULL, NULL, 'Huế mộng mơ và đầy chất thơ.', TRUE, '2025-06-07 22:00:00'),
(7, NULL, 'Tran Van G', 'tranvang@example.com', 'Bún bò Huế cay nồng khó quên.', TRUE, '2025-06-07 23:00:00'),
(8, 1, NULL, NULL, 'Phú Quốc đẹp như thiên đường!', TRUE, '2025-06-08 09:00:00'),
(8, NULL, 'Le Thi H', 'lethih@example.com', 'Bãi Sao cát trắng mịn tuyệt vời.', TRUE, '2025-06-08 10:00:00'),
(9, 2, NULL, NULL, 'Sài Gòn nhộn nhịp và đầy sức sống!', TRUE, '2025-06-09 11:00:00'),
(9, NULL, 'Pham Van I', 'phamvani@example.com', 'Chợ Bến Thành đông vui quá!', TRUE, '2025-06-09 12:00:00'),
(10, 3, NULL, NULL, 'Sapa mùa đông lạnh nhưng đẹp!', TRUE, '2025-06-10 13:00:00'),
(10, NULL, 'Hoang Thi K', 'hoangthik@example.com', 'Thắng cố Sapa rất đặc biệt.', TRUE, '2025-06-10 14:00:00');

INSERT INTO tags (name, slug) VALUES
('Du lịch biển', 'du-lich-bien'),
('Ẩm thực địa phương', 'am-thuc-dia-phuong'),
('Văn hóa truyền thống', 'van-hoa-truyen-thong'),
('Thiên nhiên', 'thien-nhien'),
('Lịch sử', 'lich-su'),
('Khám phá', 'kham-pha'),
('Núi rừng', 'nui-rung'),
('Ẩm thực đường phố', 'am-thuc-duong-pho'),
('Đảo', 'dao'),
('Chợ nổi', 'cho-noi');

INSERT INTO post_tags (post_id, tag_id) VALUES
(1, 4), (1, 6), -- Đà Lạt: Thiên nhiên, Khám phá
(2, 1), (2, 8), -- Đà Nẵng: Du lịch biển, Ẩm thực đường phố
(3, 1), (3, 4), (3, 6), -- Vịnh Hạ Long: Du lịch biển, Thiên nhiên, Khám phá
(4, 3), (4, 5), -- Hà Nội: Văn hóa truyền thống, Lịch sử
(5, 3), (5, 6), -- Hội An: Văn hóa truyền thống, Khám phá
(6, 2), (6, 8), -- Ẩm thực Hội An: Ẩm thực địa phương, Ẩm thực đường phố
(7, 3), (7, 5), -- Huế: Văn hóa truyền thống, Lịch sử
(8, 1), (8, 9), -- Phú Quốc: Du lịch biển, Đảo
(9, 3), (9, 8), -- Sài Gòn: Văn hóa truyền thống, Ẩm thực đường phố
(10, 4), (10, 7), -- Sapa: Thiên nhiên, Núi rừng
(11, 4), (11, 6), -- Ninh Bình: Thiên nhiên, Khám phá
(12, 1), (12, 9), -- Nha Trang: Du lịch biển, Đảo
(13, 10), (13, 6), -- Cần Thơ: Chợ nổi, Khám phá
(14, 3), (14, 6), -- An Giang: Văn hóa truyền thống, Khám phá
(15, 10), (15, 6), -- Tiền Giang: Chợ nổi, Khám phá
(16, 5), (16, 3), -- Nghệ An: Lịch sử, Văn hóa truyền thống
(17, 4), (17, 7), -- Mộc Châu: Thiên nhiên, Núi rừng
(18, 2), (18, 6), -- Thái Nguyên: Ẩm thực địa phương, Khám phá
(19, 4), (19, 6), -- Bắc Cạn: Thiên nhiên, Khám phá
(20, 3), (20, 2); -- Thái Bình: Văn hóa truyền thống, Ẩm thực địa phương

INSERT INTO newsletters (email, subscribed_at) VALUES
('subscriber1@example.com', '2025-06-01 08:00:00'),
('subscriber2@example.com', '2025-06-02 09:30:00'),
('subscriber3@example.com', '2025-06-03 10:15:00'),
('subscriber4@example.com', '2025-06-04 11:45:00'),
('subscriber5@example.com', '2025-06-04 14:20:00');

INSERT INTO inbox (user_id, subject, message, is_read, sent_at) VALUES
(1, 'Phản hồi về bài viết Đà Lạt', 'Bài viết về Đà Lạt rất hay, mong có thêm thông tin về các homestay!', FALSE, '2025-06-01 09:00:00'),
(1, 'Yêu cầu chỉnh sửa bài viết', 'Vui lòng kiểm tra lại nội dung bài viết về Hội An, có một số thông tin cần cập nhật.', TRUE, '2025-06-02 10:30:00'),
(2, 'Góp ý về giao diện', 'Giao diện trang web hơi khó sử dụng, có thể cải thiện phần tìm kiếm không?', FALSE, '2025-06-03 11:00:00'),
(3, 'Hỏi về tour du lịch Sapa', 'Tôi muốn hỏi thêm về các tour du lịch Sapa, bạn có thể tư vấn không?', FALSE, '2025-06-04 12:15:00'),
(4, 'Cảm ơn về bài viết Phú Quốc', 'Bài viết về Phú Quốc rất chi tiết, cảm ơn đội ngũ đã cung cấp thông tin hữu ích!', TRUE, '2025-06-04 13:45:00');

INSERT INTO analytics (post_id, views, date) VALUES
(1, 50, '2025-06-01'),
(1, 100, '2025-06-02'),
(2, 80, '2025-06-01'),
(2, 120, '2025-06-02'),
(3, 60, '2025-06-01'),
(3, 90, '2025-06-03'),
(4, 70, '2025-06-02'),
(4, 150, '2025-06-03'),
(5, 65, '2025-06-02'),
(5, 105, '2025-06-04'),
(6, 55, '2025-06-03'),
(6, 85, '2025-06-04'),
(7, 45, '2025-06-03'),
(8, 75, '2025-06-04'),
(9, 95, '2025-06-04'),
(10, 60, '2025-06-04'),
(11, 50, '2025-06-04'),
(12, 70, '2025-06-04'),
(13, 40, '2025-06-04'),
(14, 55, '2025-06-04');