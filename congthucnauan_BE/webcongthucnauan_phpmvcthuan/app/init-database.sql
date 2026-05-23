-- Tạo bảng Classrooms (Phòng học)
CREATE TABLE IF NOT EXISTS `classrooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `capacity` int(11) NOT NULL DEFAULT '30',
  `location` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng Courses (Khóa học)
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duration` int(11) NOT NULL DEFAULT '0',
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `classroom_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no-image.jpg',
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `requirements` text,
  `what_will_learn` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `classroom_id` (`classroom_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng Course_Lessons (Bài học trong khóa học)
CREATE TABLE IF NOT EXISTS `course_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `video_url` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `is_free` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT 'no-image.jpg',
  `summary` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `course_lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng Course_Enrollments (Đăng ký khóa học)
CREATE TABLE IF NOT EXISTS `course_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `enrollment_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completion_date` datetime DEFAULT NULL,
  `progress` int(11) NOT NULL DEFAULT '0',
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_course_unique` (`user_id`,`course_id`),
  KEY `course_id` (`course_id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_enrollments_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng Course_Lesson_Completion (Theo dõi hoàn thành bài học)
CREATE TABLE IF NOT EXISTS `course_lesson_completion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `lesson_id` (`lesson_id`),
  CONSTRAINT `course_lesson_completion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_lesson_completion_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_lesson_completion_ibfk_3` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_completion` (`user_id`, `lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm dữ liệu mẫu cho bảng classrooms
INSERT INTO `classrooms` (`name`, `description`, `capacity`, `location`, `active`) VALUES
('Phòng học online A', 'Phòng học trực tuyến dành cho các khóa học cơ bản', 50, 'Trực tuyến', 1),
('Phòng học online B', 'Phòng học trực tuyến dành cho các khóa học nâng cao', 30, 'Trực tuyến', 1),
('Phòng thực hành 1', 'Phòng thực hành nấu ăn với đầy đủ thiết bị hiện đại', 20, 'Tầng 1, Tòa nhà A', 1);

-- Thêm dữ liệu mẫu cho bảng courses (giả sử có user_id = 1 là admin/giảng viên)
INSERT INTO `courses` (`title`, `description`, `price`, `duration`, `level`, `classroom_id`, `user_id`, `status`, `requirements`, `what_will_learn`) VALUES
('Nấu ăn cơ bản cho người mới bắt đầu', 'Khóa học dành cho những người mới bắt đầu học nấu ăn, giới thiệu các kỹ thuật cơ bản và công thức đơn giản', 299000, 720, 'beginner', 1, 1, 'published', 'Không yêu cầu kinh nghiệm nấu ăn trước đó', 'Các kỹ thuật nấu ăn cơ bản, Cách chuẩn bị nguyên liệu, Làm quen với các loại gia vị'),
('Món ăn Á - Âu fusion', 'Khóa học kết hợp kỹ thuật nấu ăn Á và Âu để tạo ra những món ăn sáng tạo và độc đáo', 499000, 960, 'intermediate', 2, 1, 'published', 'Đã có kinh nghiệm nấu ăn cơ bản', 'Các kỹ thuật nấu ăn Á - Âu, Cách kết hợp nguyên liệu hài hòa, Trang trí món ăn đẹp mắt'),
('Làm bánh chuyên nghiệp', 'Khóa học làm bánh từ cơ bản đến nâng cao, hướng dẫn chi tiết các loại bánh phổ biến', 699000, 1200, 'advanced', 3, 1, 'published', 'Đã có kinh nghiệm làm bánh cơ bản', 'Kỹ thuật làm các loại bánh Âu, Trang trí bánh nghệ thuật, Làm socola thủ công');

-- Thêm dữ liệu mẫu cho bảng course_lessons
INSERT INTO `course_lessons` (`course_id`, `title`, `content`, `video_url`, `duration_minutes`, `sort_order`, `is_free`) VALUES
(1, 'Giới thiệu về khóa học nấu ăn cơ bản', 'Nội dung giới thiệu tổng quan về khóa học và các kỹ năng sẽ được học', 'https://www.youtube.com/watch?v=example1', 15, 1, 1),
(1, 'Làm quen với các loại dao và dụng cụ nhà bếp', 'Giới thiệu về các loại dao, dụng cụ và cách sử dụng chúng an toàn, hiệu quả', 'https://www.youtube.com/watch?v=example2', 30, 2, 1),
(1, 'Kỹ thuật cắt thái cơ bản', 'Hướng dẫn chi tiết các kỹ thuật cắt thái cơ bản trong nấu ăn', 'https://www.youtube.com/watch?v=example3', 45, 3, 0),
(2, 'Giới thiệu về ẩm thực fusion', 'Tìm hiểu về xu hướng ẩm thực fusion và lợi ích của việc kết hợp các nền ẩm thực', 'https://www.youtube.com/watch?v=example4', 20, 1, 1),
(2, 'Các nguyên liệu và gia vị đặc trưng của ẩm thực Á', 'Giới thiệu các nguyên liệu và gia vị phổ biến trong ẩm thực châu Á', 'https://www.youtube.com/watch?v=example5', 40, 2, 0),
(3, 'Giới thiệu về bánh Âu', 'Tìm hiểu về lịch sử bánh Âu và các loại bánh phổ biến', 'https://www.youtube.com/watch?v=example6', 25, 1, 1),
(3, 'Nguyên liệu cơ bản và dụng cụ làm bánh', 'Giới thiệu các nguyên liệu và dụng cụ cần thiết để làm bánh', 'https://www.youtube.com/watch?v=example7', 35, 2, 0); 