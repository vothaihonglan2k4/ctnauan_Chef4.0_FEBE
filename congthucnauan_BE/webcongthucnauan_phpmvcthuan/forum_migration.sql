-- ========================================
-- MIGRATION: Tạo bảng cho Diễn đàn chia sẻ công thức nấu ăn
-- Ngày tạo: 2025-11-13
-- ========================================

-- Bảng 1: Bài viết diễn đàn (forum_posts)
CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề bài viết',
  `content` TEXT NOT NULL COMMENT 'Nội dung bài viết',
  `image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Ảnh đính kèm',
  `video_url` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Link video YouTube',
  `recipe_id` INT(11) NULL DEFAULT NULL COMMENT 'Liên kết đến công thức (nếu có)',
  `views` INT(11) NOT NULL DEFAULT 0 COMMENT 'Số lượt xem',
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Ghim bài viết (0: không, 1: có)',
  `status` ENUM('active','hidden','deleted') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái bài viết',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_recipe_id` (`recipe_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_is_pinned` (`is_pinned`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu bài viết diễn đàn';

-- Bảng 2: Bình luận diễn đàn (forum_comments)
CREATE TABLE IF NOT EXISTS `forum_comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) NOT NULL COMMENT 'ID bài viết',
  `user_id` INT(11) NOT NULL COMMENT 'ID người comment',
  `parent_id` INT(11) NULL DEFAULT NULL COMMENT 'ID comment cha (cho reply)',
  `content` TEXT NOT NULL COMMENT 'Nội dung comment',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_parent_id` (`parent_id`),
  FOREIGN KEY (`post_id`) REFERENCES `forum_posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `forum_comments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu comment diễn đàn';

-- Bảng 3: Like bài viết (forum_likes)
CREATE TABLE IF NOT EXISTS `forum_likes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) NOT NULL COMMENT 'ID bài viết',
  `user_id` INT(11) NOT NULL COMMENT 'ID người like',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`post_id`, `user_id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`post_id`) REFERENCES `forum_posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu like bài viết';

-- Bảng 4: Tags (forum_tags)
CREATE TABLE IF NOT EXISTS `forum_tags` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL COMMENT 'Tên tag',
  `slug` VARCHAR(50) NOT NULL COMMENT 'Slug cho URL',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`),
  UNIQUE KEY `unique_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu tags';

-- Bảng 5: Liên kết bài viết với tags (forum_post_tags)
CREATE TABLE IF NOT EXISTS `forum_post_tags` (
  `post_id` INT(11) NOT NULL,
  `tag_id` INT(11) NOT NULL,
  PRIMARY KEY (`post_id`, `tag_id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_tag_id` (`tag_id`),
  FOREIGN KEY (`post_id`) REFERENCES `forum_posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `forum_tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng liên kết posts và tags';

-- Thêm dữ liệu mẫu cho tags
INSERT INTO `forum_tags` (`name`, `slug`) VALUES
('Món Việt', 'mon-viet'),
('Món Á', 'mon-a'),
('Món Âu', 'mon-au'),
('Bí quyết', 'bi-quyet'),
('Mẹo hay', 'meo-hay'),
('Thảo luận', 'thao-luan'),
('Hỏi đáp', 'hoi-dap'),
('Chia sẻ', 'chia-se')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Thêm dữ liệu mẫu cho forum_posts (3 bài viết mẫu)
INSERT INTO `forum_posts` (`user_id`, `title`, `content`, `image`, `video_url`, `recipe_id`, `views`, `is_pinned`, `status`) VALUES
(1, 'Bí quyết làm phở bò thơm ngon tại nhà', 'Xin chào mọi người! Hôm nay mình muốn chia sẻ với các bạn bí quyết làm phở bò thơm ngon tại nhà. Sau nhiều lần thử nghiệm, mình đã tìm ra được công thức phù hợp nhất.\n\nĐiều quan trọng nhất là ninh nước dùng phải đúng cách:\n1. Xương bò phải chần qua nước sôi trước\n2. Nướng gừng và hành tây cho thơm\n3. Rang gia vị (hồi, quế, đinh hương) trước khi cho vào nồi\n4. Ninh ít nhất 3-4 tiếng để nước dùng ngọt tự nhiên\n\nCác bạn có bí quyết gì khác không? Chia sẻ với mình nhé!', NULL, 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 1, 125, 1, 'active'),
(2, 'Hỏi: Làm sao để sushi không bị nát khi cuộn?', 'Chào các bạn! Mình mới tập làm sushi nhưng cứ cuộn là nát. Có bạn nào biết mẹo không?\n\nMình đã thử:\n- Dùng chiếu tre\n- Để cơm nguội\n- Cuộn chặt tay\n\nNhưng vẫn không được. Có phải do tay còn non hay do nguyên liệu không đúng nhỉ? 😅', NULL, NULL, 2, 87, 0, 'active'),
(3, 'Chia sẻ: Cách làm bánh tiramisu không cần lò nướng', 'Mình vừa thử làm tiramisu theo công thức mới và thành công rực rỡ! Muốn chia sẻ với mọi người luôn.\n\nĐặc biệt là công thức này không cần lò nướng, chỉ cần tủ lạnh là được. Rất phù hợp cho những bạn không có lò.\n\nNguyên liệu:\n- Phô mai mascarpone 500g\n- Trứng gà 4 quả\n- Đường 100g\n- Cà phê đen 250ml\n- Bánh quy Savoiardi\n- Bột cacao\n\nCách làm thì các bạn xem trong công thức mình đã đăng nhé. Link bên dưới ⬇️', NULL, NULL, 5, 203, 0, 'active');

-- Thêm dữ liệu mẫu cho comments
INSERT INTO `forum_comments` (`post_id`, `user_id`, `parent_id`, `content`) VALUES
(1, 2, NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng hay làm phở tại nhà. Bí quyết của mình là thêm ít đường phèn vào nước dùng cho ngọt tự nhiên.'),
(1, 3, NULL, 'Hay quá! Mình sẽ thử làm theo. Cho mình hỏi xương bò mua ở đâu vậy?'),
(1, 1, 2, 'Ồ, đường phèn à? Mình chưa thử bao giờ. Cảm ơn bạn, mình sẽ thử lần sau!'),
(2, 1, NULL, 'Có thể do cơm còn nóng hoặc quá nhiều cơm đấy bạn. Thử để cơm nguội hẳn, và xếp cơm mỏng thôi nhé.'),
(2, 4, NULL, 'Mình cũng gặp vấn đề này. Sau này mình phát hiện là do tay bị ướt. Phải giữ tay khô và cuốn nhanh tay.'),
(3, 2, NULL, 'Trông ngon quá! Cho mình xin link công thức với ạ 😍');

-- Thêm dữ liệu mẫu cho likes
INSERT INTO `forum_likes` (`post_id`, `user_id`) VALUES
(1, 2), (1, 3), (1, 4), (1, 5),
(2, 1), (2, 3), (2, 4),
(3, 1), (3, 2), (3, 4), (3, 5);

-- Thêm dữ liệu mẫu cho post_tags
INSERT INTO `forum_post_tags` (`post_id`, `tag_id`) VALUES
(1, 1), (1, 4), (1, 5),  -- Món Việt, Bí quyết, Mẹo hay
(2, 2), (2, 7),           -- Món Á, Hỏi đáp
(3, 3), (3, 8);           -- Món Âu, Chia sẻ

-- Tạo trigger để đếm số comment tự động (tùy chọn - để sau)
-- Tạo trigger để tăng views tự động khi xem bài viết (tùy chọn - để sau)

-- Hoàn tất migration
SELECT 'Forum tables created successfully!' as message;

