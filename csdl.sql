-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 23, 2026 lúc 04:07 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `congthucnauan`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Món Việt Nam', '2025-04-04 06:23:45'),
(2, 'Món Á', '2025-04-04 06:23:45'),
(3, 'Món Âu', '2025-04-04 06:23:45'),
(4, 'Món chay', '2025-04-04 06:23:45'),
(5, 'Bánh ngọt', '2025-04-04 06:23:45'),
(6, 'Đồ uống', '2025-04-04 06:23:45'),
(7, 'Ăn vặt', '2025-04-04 06:23:45'),
(8, 'Salad', '2025-04-04 06:23:45'),
(9, 'Món tráng miệng', '2025-04-04 06:23:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classrooms`
--

CREATE TABLE `classrooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `location` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `classrooms`
--

INSERT INTO `classrooms` (`id`, `name`, `description`, `capacity`, `location`, `active`, `created_at`) VALUES
(1, 'Phòng học online A', 'Phòng học trực tuyến dành cho các khóa học cơ bản', 50, 'Trực tuyến', 1, '2025-04-05 20:28:24'),
(2, 'Phòng học online B', 'Phòng học trực tuyến dành cho các khóa học nâng cao', 30, 'Trực tuyến', 1, '2025-04-05 20:28:24'),
(4, 'PHòng 2 test 1a', 'Abc12345', 100, 'Bảo Trì', 1, '2025-04-08 14:29:56'),
(6, 'Phòng test 2', 'Phòng học thử nghiệm cho các tính năng mới', 35, 'Tầng 3, Tòa C', 1, '2025-09-04 10:14:28'),
(9, 'Test 02', 'abc', 5, 'dsdssd', 0, '2025-12-08 23:40:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','responded') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'ewafawfe', '2222@ssss.fff', 'fszdfdszf', 'dsfszd', 'new', '2025-04-04 14:42:55'),
(2, 'égsrg', 'dsgsd@ddd.fff', 'ằdasf', 'asaf', 'new', '2025-04-06 02:34:02'),
(3, 'ádSAD', '222@ddd.sfdzsg', 'đASACSAC', 'CCxzc', 'new', '2025-04-08 07:33:15'),
(4, 'chao', 'hello@vothaihonglan.net', 'Hello', 'Chao ban , ban co khoe ko ??', 'read', '2025-09-09 03:00:14'),
(5, 'fsdfs', 'fdsfds@ffds.gfgdf', 'dgdfgdfgd', 'fdgdgfdgd', 'new', '2025-11-22 15:33:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` int(11) NOT NULL DEFAULT 0,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `classroom_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no-image.jpg',
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `requirements` text DEFAULT NULL,
  `what_will_learn` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `price`, `duration`, `level`, `classroom_id`, `user_id`, `image`, `status`, `requirements`, `what_will_learn`, `created_at`) VALUES
(1, 'Nấu ăn cơ bản cho người mới bắt đầu', 'Khóa học dành cho những người mới bắt đầu học nấu ăn, giới thiệu các kỹ thuật cơ bản và công thức đơn giản', 299000.00, 720, 'beginner', 1, 1, '1745825702_giai-ngan-ngay-tet-voi-mon-salad-hoa-qua-kieu-han-quoc-202205241325570525.jpg', 'published', '', '', '2025-04-05 20:28:24'),
(2, 'Món ăn Á - Âu fusion', 'Khóa học kết hợp kỹ thuật nấu ăn Á và Âu để tạo ra những món ăn sáng tạo và độc đáo', 499000.00, 960, 'intermediate', 2, 1, '1744090962_images.jfif', 'published', '', '', '2025-04-05 20:28:24'),
(3, 'Làm bánh chuyên nghiệp', 'Khóa học làm bánh từ cơ bản đến nâng cao, hướng dẫn chi tiết các loại bánh phổ biến', 699000.00, 1200, 'advanced', 4, 1, '1744091165_image.jpeg', 'published', 'Đã có kinh nghiệm làm bánh cơ bản', 'Kỹ thuật làm các loại bánh Âu, Trang trí bánh nghệ thuật, Làm socola thủ công', '2025-04-05 20:28:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `enrollment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `completion_date` datetime DEFAULT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `user_id`, `course_id`, `payment_id`, `enrollment_date`, `completion_date`, `progress`, `status`) VALUES
(23, 1, 2, 55, '2025-12-19 18:28:50', NULL, 0, 'active'),
(24, 1, 1, 58, '2025-12-26 19:29:12', NULL, 0, 'active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `course_lessons`
--

CREATE TABLE `course_lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_free` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT 'no-image.jpg',
  `summary` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `course_lessons`
--

INSERT INTO `course_lessons` (`id`, `course_id`, `title`, `content`, `video_url`, `duration_minutes`, `sort_order`, `is_free`, `image`, `summary`, `created_at`) VALUES
(1, 1, 'Giới thiệu về khóa học nấu ăn cơ bản', 'Nội dung giới thiệu tổng quan về khóa học và các kỹ năng sẽ được học', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 15, 1, 1, 'no-image.jpg', NULL, '2025-04-05 20:28:24'),
(2, 1, 'Làm quen với các loại dao và dụng cụ nhà bếp', 'Giới thiệu về các loại dao, dụng cụ và cách sử dụng chúng an toàn, hiệu quả', 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 30, 2, 1, 'no-image.jpg', NULL, '2025-04-05 20:28:24'),
(3, 1, 'Kỹ thuật cắt thái cơ bản', 'Hướng dẫn chi tiết các kỹ thuật cắt thái cơ bản trong nấu ăn', 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 45, 3, 0, 'no-image.jpg', NULL, '2025-04-05 20:28:24'),
(4, 2, 'Giới thiệu về ẩm thực fusion', 'Tìm hiểu về xu hướng ẩm thực fusion và lợi ích của việc kết hợp các nền ẩm thực', 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 20, 1, 1, 'no-image.jpg', NULL, '2025-04-05 20:28:24'),
(5, 2, 'Các nguyên liệu và gia vị đặc trưng của ẩm thực Á', 'Giới thiệu các nguyên liệu và gia vị phổ biến trong ẩm thực châu Á', 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 40, 2, 0, 'no-image.jpg', NULL, '2025-04-05 20:28:24'),
(6, 3, 'Giới thiệu về bánh Âu', 'Tìm hiểu về lịch sử bánh Âu và các loại bánh phổ biến', 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 25, 1, 1, 'no-image.jpg', NULL, '2025-04-05 20:28:24'),
(7, 3, 'Nguyên liệu cơ bản và dụng cụ làm bánh', 'Giới thiệu các nguyên liệu và dụng cụ cần thiết để làm bánh', 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 35, 2, 0, 'no-image.jpg', NULL, '2025-04-05 20:28:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `course_lesson_completion`
--

CREATE TABLE `course_lesson_completion` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `course_lesson_completion`
--

INSERT INTO `course_lesson_completion` (`id`, `user_id`, `course_id`, `lesson_id`, `completed_at`) VALUES
(1, 1, 1, 1, '2025-04-06 02:29:13'),
(2, 1, 1, 2, '2025-04-06 08:44:03'),
(3, 1, 1, 3, '2025-04-08 04:15:26'),
(4, 1, 2, 4, '2025-04-08 04:22:33'),
(5, 1, 2, 5, '2025-04-08 04:23:37'),
(6, 6, 1, 1, '2025-04-08 04:40:47'),
(7, 6, 1, 2, '2025-04-08 04:40:53'),
(8, 6, 1, 3, '2025-04-08 04:40:59'),
(9, 14, 1, 1, '2025-04-08 04:43:45'),
(10, 14, 1, 2, '2025-04-08 04:43:51'),
(11, 14, 1, 3, '2025-04-08 04:44:03'),
(12, 14, 2, 4, '2025-04-08 04:44:23'),
(13, 14, 2, 5, '2025-04-08 04:44:26'),
(14, 14, 3, 6, '2025-04-08 04:48:41'),
(15, 14, 3, 7, '2025-04-08 04:48:45'),
(16, 1, 3, 6, '2025-04-17 13:20:24'),
(17, 1, 3, 7, '2025-04-17 13:20:39'),
(18, 31, 2, 4, '2025-10-11 11:47:52'),
(19, 6, 2, 5, '2025-11-06 07:29:18'),
(20, 30, 1, 1, '2025-11-20 08:18:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_comments`
--

CREATE TABLE `forum_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL COMMENT 'ID bài viết',
  `user_id` int(11) NOT NULL COMMENT 'ID người comment',
  `parent_id` int(11) DEFAULT NULL COMMENT 'ID comment cha (cho reply)',
  `content` text NOT NULL COMMENT 'Nội dung comment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu comment diễn đàn';

--
-- Đang đổ dữ liệu cho bảng `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `post_id`, `user_id`, `parent_id`, `content`, `created_at`) VALUES
(1, 1, 2, NULL, 'Cảm ơn bạn đã chia sẻ! Mình cũng hay làm phở tại nhà. Bí quyết của mình là thêm ít đường phèn vào nước dùng cho ngọt tự nhiên.', '2025-11-13 03:03:20'),
(2, 1, 3, NULL, 'Hay quá! Mình sẽ thử làm theo. Cho mình hỏi xương bò mua ở đâu vậy?', '2025-11-13 03:03:20'),
(3, 1, 1, 2, 'Ồ, đường phèn à? Mình chưa thử bao giờ. Cảm ơn bạn, mình sẽ thử lần sau!', '2025-11-13 03:03:20'),
(4, 2, 1, NULL, 'Có thể do cơm còn nóng hoặc quá nhiều cơm đấy bạn. Thử để cơm nguội hẳn, và xếp cơm mỏng thôi nhé.', '2025-11-13 03:03:20'),
(5, 2, 4, NULL, 'Mình cũng gặp vấn đề này. Sau này mình phát hiện là do tay bị ướt. Phải giữ tay khô và cuốn nhanh tay.', '2025-11-13 03:03:20'),
(6, 3, 2, NULL, 'Trông ngon quá! Cho mình xin link công thức với ạ 😍', '2025-11-13 03:03:20'),
(7, 2, 14, NULL, 'hi', '2025-11-15 15:02:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_likes`
--

CREATE TABLE `forum_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL COMMENT 'ID bài viết',
  `user_id` int(11) NOT NULL COMMENT 'ID người like',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu like bài viết';

--
-- Đang đổ dữ liệu cho bảng `forum_likes`
--

INSERT INTO `forum_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 2, '2025-11-13 03:03:20'),
(2, 1, 3, '2025-11-13 03:03:20'),
(3, 1, 4, '2025-11-13 03:03:20'),
(4, 1, 5, '2025-11-13 03:03:20'),
(5, 2, 1, '2025-11-13 03:03:20'),
(6, 2, 3, '2025-11-13 03:03:20'),
(7, 2, 4, '2025-11-13 03:03:20'),
(8, 3, 1, '2025-11-13 03:03:20'),
(9, 3, 2, '2025-11-13 03:03:20'),
(10, 3, 4, '2025-11-13 03:03:20'),
(11, 3, 5, '2025-11-13 03:03:20'),
(12, 1, 14, '2025-11-15 15:00:30'),
(13, 2, 14, '2025-11-15 15:02:22'),
(14, 5, 1, '2025-12-01 16:36:58'),
(15, 4, 30, '2025-12-26 09:38:50'),
(16, 4, 1, '2026-02-16 18:10:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Tiêu đề bài viết',
  `content` text NOT NULL COMMENT 'Nội dung bài viết',
  `image` varchar(255) DEFAULT NULL COMMENT 'Ảnh đính kèm',
  `video_url` varchar(255) DEFAULT NULL COMMENT 'Link video YouTube',
  `recipe_id` int(11) DEFAULT NULL COMMENT 'Liên kết đến công thức (nếu có)',
  `views` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượt xem',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Ghim bài viết (0: không, 1: có)',
  `status` enum('active','hidden','deleted') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái bài viết',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu bài viết diễn đàn';

--
-- Đang đổ dữ liệu cho bảng `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `title`, `content`, `image`, `video_url`, `recipe_id`, `views`, `is_pinned`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bí quyết làm phở bò thơm ngon tại nhà', 'Xin chào mọi người! Hôm nay mình muốn chia sẻ với các bạn bí quyết làm phở bò thơm ngon tại nhà. Sau nhiều lần thử nghiệm, mình đã tìm ra được công thức phù hợp nhất.\n\nĐiều quan trọng nhất là ninh nước dùng phải đúng cách:\n1. Xương bò phải chần qua nước sôi trước\n2. Nướng gừng và hành tây cho thơm\n3. Rang gia vị (hồi, quế, đinh hương) trước khi cho vào nồi\n4. Ninh ít nhất 3-4 tiếng để nước dùng ngọt tự nhiên\n\nCác bạn có bí quyết gì khác không? Chia sẻ với mình nhé!', NULL, 'https://www.youtube.com/watch?v=wpa_Zc9W7vE', 1, 160, 1, 'active', '2025-11-13 03:03:20', '2026-02-16 11:07:01'),
(2, 2, 'Hỏi: Làm sao để sushi không bị nát khi cuộn?', 'Chào các bạn! Mình mới tập làm sushi nhưng cứ cuộn là nát. Có bạn nào biết mẹo không?\n\nMình đã thử:\n- Dùng chiếu tre\n- Để cơm nguội\n- Cuộn chặt tay\n\nNhưng vẫn không được. Có phải do tay còn non hay do nguyên liệu không đúng nhỉ? 😅', NULL, NULL, 2, 104, 0, 'active', '2025-11-13 03:03:20', '2026-01-12 16:12:40'),
(3, 3, 'Chia sẻ: Cách làm bánh tiramisu không cần lò nướng', 'Mình vừa thử làm tiramisu theo công thức mới và thành công rực rỡ! Muốn chia sẻ với mọi người luôn.\n\nĐặc biệt là công thức này không cần lò nướng, chỉ cần tủ lạnh là được. Rất phù hợp cho những bạn không có lò.\n\nNguyên liệu:\n- Phô mai mascarpone 500g\n- Trứng gà 4 quả\n- Đường 100g\n- Cà phê đen 250ml\n- Bánh quy Savoiardi\n- Bột cacao\n\nCách làm thì các bạn xem trong công thức mình đã đăng nhé. Link bên dưới ⬇️', NULL, NULL, 5, 219, 0, 'active', '2025-11-13 03:03:20', '2025-12-19 11:31:36'),
(4, 14, 'Bí quyết làm gà kho gừng thơm ngon chuẩn vị tại nhà', 'Gà kho gừng là món ăn quen thuộc trong bữa cơm gia đình Việt Nam. Tuy đơn giản nhưng để kho được nồi gà đậm đà, thơm nức mà thịt vẫn mềm mọng thì cũng cần một vài mẹo nhỏ. Hôm nay mình chia sẻ công thức mà mình đã làm nhiều lần và luôn thành công.', '1763822925_ga-kho-gung-1.jpg', 'https://www.youtube.com/watch?v=Qxyz123ABC', NULL, 20, 0, 'active', '2025-11-22 07:48:45', '2026-02-16 11:10:54'),


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_post_tags`
--

CREATE TABLE `forum_post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng liên kết posts và tags';

--
-- Đang đổ dữ liệu cho bảng `forum_post_tags`
--

INSERT INTO `forum_post_tags` (`post_id`, `tag_id`) VALUES
(1, 1),
(1, 4),
(1, 5),
(2, 2),
(2, 7),
(3, 3),
(3, 8),
(4, 6),
(4, 7),
(5, 6),
(5, 7),
(5, 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_tags`
--

CREATE TABLE `forum_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Tên tag',
  `slug` varchar(50) NOT NULL COMMENT 'Slug cho URL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu tags';

--
-- Đang đổ dữ liệu cho bảng `forum_tags`
--

INSERT INTO `forum_tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Món Việt', 'mon-viet', '2025-11-13 03:03:20'),
(2, 'Món Á', 'mon-a', '2025-11-13 03:03:20'),
(3, 'Món Âu', 'mon-au', '2025-11-13 03:03:20'),
(4, 'Bí quyết', 'bi-quyet', '2025-11-13 03:03:20'),
(5, 'Mẹo hay', 'meo-hay', '2025-11-13 03:03:20'),
(6, 'Thảo luận', 'thao-luan', '2025-11-13 03:03:20'),
(7, 'Hỏi đáp', 'hoi-dap', '2025-11-13 03:03:20'),
(8, 'Chia sẻ', 'chia-se', '2025-11-13 03:03:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_11_16_194107_create_personal_access_tokens_table', 1),
(2, '2025_11_20_192000_modify_status_in_course_enrollments', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL COMMENT 'credit_card, bank_transfer, momo, vnpay',
  `status` enum('pending','completed','failed','refunded','cancelled') DEFAULT 'pending' COMMENT 'pending: Đang chờ, completed: Thành công, failed: Thất bại',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT 'Mã giao dịch nội bộ hoặc từ gateway',
  `gateway_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Mã giao dịch từ payment gateway',
  `gateway_response` text DEFAULT NULL COMMENT 'Response từ payment gateway (JSON)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu trữ thông tin thanh toán, hỗ trợ MoMo và VNPay';

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `amount`, `payment_method`, `status`, `transaction_id`, `gateway_transaction_id`, `gateway_response`, `created_at`, `updated_at`) VALUES
(1, 2, 100000.00, 'credit_card', 'completed', 'TRX1234567890', NULL, NULL, '2025-04-04 06:23:45', NULL),
(2, 3, 150000.00, 'momo', 'completed', 'TRX2345678901', NULL, NULL, '2025-04-04 06:23:45', NULL),
(3, 4, 200000.00, 'bank_transfer', 'completed', 'TRX3456789012', NULL, NULL, '2025-04-04 06:23:45', NULL),
(4, 2, 50000.00, 'credit_card', 'completed', 'TRX4567890123', NULL, NULL, '2025-04-04 06:23:45', NULL),
(5, 5, 50000.00, 'momo', 'completed', 'TRX67efc09c6a529', NULL, NULL, '2025-04-04 11:21:00', NULL),
(6, 5, 299000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-05 15:23:30', NULL),
(7, 5, 499000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-05 15:27:05', NULL),
(8, 6, 699000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-05 15:41:47', NULL),
(9, 1, 299000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-06 01:55:42', NULL),
(10, 1, 499000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-06 02:45:40', NULL),
(11, 6, 299000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 04:40:43', NULL),
(12, 5, 699000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 04:41:54', NULL),
(13, 1, 699000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 04:42:11', NULL),
(14, 14, 299000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 04:43:40', NULL),
(15, 14, 499000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 04:44:19', NULL),
(16, 14, 699000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 04:48:37', NULL),
(17, 15, 699000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 07:31:13', NULL),
(18, 1, 50000.00, 'bank_transfer', 'completed', 'TRX67f516b5b3b6b', NULL, NULL, '2025-04-08 12:29:41', NULL),
(19, 1, 50000000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-04-08 14:13:44', NULL),
(20, 1, 50000.00, 'momo', 'completed', 'TRX683871e4d370c', NULL, NULL, '2025-05-29 14:40:36', NULL),
(21, 27, 499000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-09-11 02:15:19', NULL),
(22, 31, 299000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-10-11 11:43:03', NULL),
(23, 31, 499000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-10-11 11:47:28', NULL),
(24, 30, 299000.00, 'demo', 'completed', NULL, NULL, NULL, '2025-11-03 16:25:30', NULL),
(25, 30, 50000.00, 'momo', 'completed', '1762187495', 'TEST_1762187495', NULL, '2025-11-03 16:31:36', '2025-11-03 16:50:15'),
(26, 30, 50000.00, 'momo', 'completed', '1762187645', 'TEST_1762187645', NULL, '2025-11-03 16:34:06', '2025-11-03 16:48:53'),
(27, 30, 50000.00, 'momo', 'completed', '155561152', NULL, NULL, '2025-11-03 16:41:11', '2025-11-03 16:45:08'),
(28, 30, 50000.00, 'momo', 'completed', '259491809', NULL, NULL, '2025-11-03 16:47:01', '2025-11-03 16:47:14'),
(29, 30, 50000.00, 'momo', 'completed', '336556487', 'TEST_1762188606', NULL, '2025-11-03 16:50:07', '2025-11-03 16:52:49'),
(30, 30, 50000.00, 'momo', 'completed', '748925082', NULL, NULL, '2025-11-03 16:57:20', '2025-11-03 16:58:38'),
(31, 5, 50000.00, 'stripe', 'completed', 'pi_3SQAjj0j0Ekxnzx51jASkdLp', NULL, NULL, '2025-11-05 17:42:13', '2025-11-05 17:43:13'),
(32, 30, 50000.00, 'stripe', 'completed', 'pi_3SQAs00j0Ekxnzx509DtLYsd', NULL, NULL, '2025-11-05 17:51:01', '2025-11-05 17:54:12'),
(33, 30, 499000.00, 'stripe', 'completed', 'pi_3SQAvN0j0Ekxnzx51nC8rh6T', NULL, NULL, '2025-11-05 17:54:46', '2025-11-05 17:55:15'),
(34, 30, 699000.00, 'vnpay', 'pending', '1762366024', NULL, NULL, '2025-11-05 18:07:04', NULL),
(35, 30, 699000.00, 'stripe', 'pending', 'cs_test_a1iu3Crp1O2OsPP88Tsj14admGaX9jLZSSb0DhpfOCiqepi8c8tYWtIRLs', NULL, NULL, '2025-11-05 18:07:19', NULL),
(36, 30, 699000.00, 'stripe', 'completed', 'pi_3SQJ5r0j0Ekxnzx51msyVJhP', NULL, NULL, '2025-11-06 02:37:52', '2025-11-06 02:38:37'),
(37, 6, 499000.00, 'stripe', 'completed', 'pi_3SQNcm0j0Ekxnzx50nfwiLUg', NULL, NULL, '2025-11-06 07:28:18', '2025-11-06 07:28:55'),
(38, 1, 200000.00, 'stripe', 'completed', 'pi_3SRrlo0j0Ekxnzx50YSkYY0W', NULL, NULL, '2025-11-10 09:51:49', '2025-11-10 09:52:23'),
(39, 1, 50000.00, 'vnpay', 'pending', '1763105722', NULL, NULL, '2025-11-14 07:35:22', NULL),
(40, 1, 50000.00, 'vnpay', 'pending', '1763106247', NULL, NULL, '2025-11-14 07:44:07', NULL),
(41, 1, 50000.00, 'vnpay', 'completed', '15260761', NULL, NULL, '2025-11-14 07:47:48', '2025-11-14 07:48:26'),
(42, 33, 699000.00, 'vnpay', 'pending', '1763260719', NULL, NULL, '2025-11-16 02:38:39', NULL),
(43, 33, 699000.00, 'vnpay', 'failed', '1763260742', NULL, NULL, '2025-11-16 02:39:02', '2025-11-16 02:39:50'),
(44, 14, 50000.00, 'stripe', 'pending', 'TXN17634564465972', NULL, NULL, '2025-11-18 02:00:46', '2025-11-18 02:00:46'),
(45, 14, 50000.00, 'stripe', 'pending', 'cs_test_a12RZHW0aEJu07hx2cq2fuRhu8ES8pgM3bOjtnd9q9Hp83jvpYz5yJHujG', NULL, NULL, '2025-11-18 02:10:26', '2025-11-18 02:10:27'),
(46, 30, 299000.00, 'vnpay', 'completed', '15272415', NULL, NULL, '2025-11-20 08:15:41', '2025-11-20 08:18:01'),
(47, 14, 50000.00, 'vnpay', 'pending', 'TXN17636291002099', NULL, NULL, '2025-11-20 01:58:20', '2025-11-20 01:58:20'),
(48, 14, 50000.00, 'vnpay', 'completed', '15272637', NULL, NULL, '2025-11-20 02:02:43', '2025-11-20 02:10:00'),
(49, 14, 50000.00, 'vnpay', 'pending', 'VNPAY_17636304365530', NULL, NULL, '2025-11-20 02:20:36', '2025-11-20 02:20:36'),
(50, 14, 50000.00, 'vnpay', 'completed', '15272739', NULL, NULL, '2025-11-20 02:25:18', '2025-11-20 02:26:01'),
(51, 14, 299000.00, 'stripe', 'completed', 'cs_test_a1OKRqoYK4YzUGpygkLqyySs3KWqd95RcB82t9bjG6DQ0GkyGCRHLCt6Zp', NULL, NULL, '2025-11-20 05:07:34', '2025-11-20 05:08:19'),
(52, 14, 299000.00, 'stripe', 'pending', 'STRIPE_17636407818516', NULL, NULL, '2025-11-20 05:13:01', '2025-11-20 05:13:01'),
(53, 14, 299000.00, 'stripe', 'pending', 'STRIPE_17636407881066', NULL, NULL, '2025-11-20 05:13:08', '2025-11-20 05:13:08'),
(54, 14, 299000.00, 'stripe', 'completed', 'cs_test_a1uKCZ3RabLVmQk9ZgQ9utMFTfPTPISXA3lPCoas7pD4JmiVgpcFHU67f2', NULL, NULL, '2025-11-20 05:17:49', '2025-11-20 05:18:31'),
(55, 1, 499000.00, 'vnpay', 'completed', '15355074', NULL, NULL, '2025-12-19 11:26:52', '2025-12-19 11:28:50'),
(56, 1, 699000.00, 'stripe', 'pending', 'cs_test_a1BwGi2qQOmseXLQyAz9EBtlj0GjnPXW5It5rsuNJTHuqnfKGHUgDllkk3', NULL, NULL, '2025-12-19 15:47:46', NULL),
(57, 1, 699000.00, 'vnpay', 'failed', '1766159289', NULL, NULL, '2025-12-19 15:48:09', '2025-12-19 15:48:27'),
(58, 1, 299000.00, 'stripe', 'completed', 'pi_3Sia8n0j0Ekxnzx51tEvFHiF', NULL, NULL, '2025-12-26 12:27:49', '2025-12-26 12:29:12'),
(59, 30, 50000.00, 'vnpay', 'pending', '1772993642', NULL, NULL, '2026-03-08 18:14:02', NULL),
(60, 30, 50000.00, 'vnpay', 'pending', '1772993651', NULL, NULL, '2026-03-08 18:14:11', NULL),
(61, 30, 50000.00, 'vnpay', 'pending', '1772993657', NULL, NULL, '2026-03-08 18:14:17', NULL),
(62, 30, 50000.00, 'vnpay', 'pending', '1772993693', NULL, NULL, '2026-03-08 18:14:53', NULL),
(63, 30, 50000.00, 'vnpay', 'pending', '1772993697', NULL, NULL, '2026-03-08 18:14:57', NULL),
(64, 30, 50000.00, 'momo', 'pending', '1772993708', NULL, NULL, '2026-03-08 18:15:08', NULL),
(65, 30, 50000.00, 'momo', 'pending', '1772993723', NULL, NULL, '2026-03-08 18:15:24', NULL),
(66, 30, 50000.00, 'momo', 'pending', '1772993845', NULL, NULL, '2026-03-08 18:17:25', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_gateway_config`
--

CREATE TABLE `payment_gateway_config` (
  `id` int(11) NOT NULL,
  `gateway` varchar(50) NOT NULL COMMENT 'momo, vnpay',
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `is_encrypted` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng cấu hình payment gateway';

--
-- Đang đổ dữ liệu cho bảng `payment_gateway_config`
--

INSERT INTO `payment_gateway_config` (`id`, `gateway`, `config_key`, `config_value`, `is_encrypted`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'momo', 'partner_code', 'MOMOBKUN20180529', 0, 1, '2025-11-03 16:31:02', NULL),
(2, 'momo', 'access_key', 'klm05TvNBzhg7h7j', 0, 1, '2025-11-03 16:31:02', NULL),
(3, 'momo', 'secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa', 0, 1, '2025-11-03 16:31:02', NULL),
(4, 'momo', 'endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create', 0, 1, '2025-11-03 16:31:02', NULL),
(5, 'momo', 'environment', 'test', 0, 1, '2025-11-03 16:31:02', NULL),
(6, 'vnpay', 'tmn_code', 'CGWT28A1', 0, 1, '2025-11-03 16:31:02', NULL),
(7, 'vnpay', 'hash_secret', 'XNBCJFAKAZRWFFTTENQUXVUHHJDHLRJB', 0, 1, '2025-11-03 16:31:02', NULL),
(8, 'vnpay', 'url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html', 0, 1, '2025-11-03 16:31:02', NULL),
(9, 'vnpay', 'environment', 'test', 0, 1, '2025-11-03 16:31:02', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'create, callback, update, etc.',
  `gateway` varchar(50) DEFAULT NULL COMMENT 'momo, vnpay, etc.',
  `request_data` text DEFAULT NULL COMMENT 'Dữ liệu gửi đi (JSON)',
  `response_data` text DEFAULT NULL COMMENT 'Dữ liệu nhận về (JSON)',
  `status` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu log giao dịch thanh toán';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(4, 'App\\Models\\User', 35, 'auth-token', 'ad4d9263f878c7fe05d21064c4b15e60bef63e003ca69d960b30cdfc208da420', '[\"*\"]', NULL, NULL, '2025-11-18 00:32:09', '2025-11-18 00:32:09'),
(13, 'App\\Models\\User', 14, 'auth-token', '5e1a238e85678db0f9d4cbbccdb2db41cf29f68ccdbe0a77d312d9cb3f11efc0', '[\"*\"]', NULL, NULL, '2025-11-22 07:52:39', '2025-11-22 07:52:39'),
(17, 'App\\Models\\User', 30, 'auth-token', '387cef580f56ef8492c5fe76bf3e407720d276392a780216a6881ad12f976bc5', '[\"*\"]', '2025-12-26 02:38:50', NULL, '2025-12-10 23:26:04', '2025-12-26 02:38:50'),
(19, 'App\\Models\\User', 1, 'auth-token', '4c0f7626d9be381e9abb6fad1b2d32b3545f2e22c31008c3c8f4c7dbca20a675', '[\"*\"]', '2026-03-23 08:05:27', NULL, '2026-03-23 08:04:55', '2026-03-23 08:05:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ratings`
--

INSERT INTO `ratings` (`id`, `recipe_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 3, 5, 'Công thức rất chi tiết và dễ làm theo. Phở ngon như ngoài hàng!', '2025-04-04 06:23:45'),
(2, 1, 4, 4, 'Nước dùng thơm, nhưng tôi nghĩ nên ninh xương lâu hơn.', '2025-04-04 06:23:45'),
(3, 2, 2, 5, 'Lần đầu làm sushi mà thành công, cảm ơn công thức!', '2025-04-04 06:23:45'),
(4, 2, 4, 3, 'Ngon nhưng hơi khó cuốn cho người mới.', '2025-04-04 06:23:45'),
(5, 3, 2, 5, 'Pizza làm tại nhà ngon không kém gì ngoài tiệm.', '2025-04-04 06:23:45'),
(6, 4, 3, 5, 'Gỏi cuốn tôm thịt rất ngon và đầy đủ dinh dưỡng.', '2025-04-04 06:23:45'),
(7, 5, 2, 4, 'Tiramisu ngon, nhưng hơi ngọt với khẩu vị của mình.', '2025-04-04 06:23:45'),
(8, 7, 5, 5, 'Very good', '2025-04-04 06:52:06'),
(11, 8, 14, 5, '@eefff', '2025-11-15 14:54:56'),
(12, 8, 1, 5, 'Test cong thuc', '2025-12-01 16:12:20'),
(13, 7, 1, 5, 'M&oacute;n ngon đấy bạn', '2025-12-19 10:16:48'),
(14, 8, 6, 5, 'hi', '2026-01-12 16:10:35'),
(15, 4, 6, 5, 'Ổn đấy b', '2026-01-12 16:13:14'),
(16, 3, 6, 5, '@33%4', '2026-01-12 16:17:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `image` varchar(255) DEFAULT 'no-image.jpg',
  `video_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `description`, `ingredients`, `instructions`, `image`, `video_url`, `category_id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Phở bò', 'Phở là một món ăn truyền thống của Việt Nam, có xuất xứ từ Nam Định, Hà Nội và được coi là một trong những món ăn tiêu biểu cho nền ẩm thực Việt Nam.', '- 500g thịt bò (thăn, nạm, gầu, bắp...)\r\n- 200g xương ống bò\r\n- 200g gừng\r\n- 2 củ hành tây\r\n- 1 thìa hoa hồi\r\n- 1 thìa đinh hương\r\n- 1 thanh quế\r\n- 1 thìa thảo quả\r\n- 1 thìa ngò\r\n- Gia vị: muối, đường, nước mắm, hạt nêm\r\n- 500g bánh phở\r\n- Rau sống, chanh, ớt', 'Bước 1: Sơ chế nguyên liệu\r\n- Rửa sạch xương và thịt bò\r\n- Cạo sạch gừng, rửa sạch và đập dập\r\n- Hành tây bóc vỏ, rửa sạch và cắt làm đôi\r\n- Hành tím, hành lá rửa sạch và băm nhỏ\r\n\r\nBước 2: Ninh nước dùng\r\n- Cho xương ống và nước vào nồi, ninh 2 giờ\r\n- Nướng gừng và hành tây\r\n- Cho gia vị vào vải lọc và thả vào nồi nước dùng\r\n- Nêm nếm gia vị vừa miệng\r\n\r\nBước 3: Chuẩn bị thịt bò\r\n- Thịt bò thái mỏng\r\n- Trụng bánh phở qua nước sôi\r\n\r\nBước 4: Hoàn thành\r\n- Cho bánh phở vào tô\r\n- Xếp thịt bò lên trên\r\n- Chan nước dùng nóng\r\n- Rắc hành, ngò lên trên\r\n- Dùng kèm với rau sống, chanh, ớt', '1743749375_thumb-12.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 1, 2, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:05'),
(2, 'Sushi', 'Sushi là một món ăn Nhật Bản gồm cơm trộn giấm (shari) kết hợp với các nguyên liệu khác (neta).', '- 2 chén gạo Nhật\r\n- 3 chén nước\r\n- 1/4 chén giấm gạo\r\n- 1 muỗng canh đường\r\n- 1/2 muỗng cà phê muối\r\n- Rong biển (nori)\r\n- Cá hồi tươi\r\n- Bơ\r\n- Dưa leo\r\n- Wasabi\r\n- Nước tương Nhật', 'Bước 1: Nấu cơm\r\n- Rửa sạch gạo nhiều lần cho đến khi nước trong\r\n- Ngâm gạo trong nước khoảng 30 phút\r\n- Nấu cơm với tỷ lệ nước gạo 1:1.2\r\n- Sau khi nấu xong, để cơm nghỉ 10 phút\r\n\r\nBước 2: Pha hỗn hợp giấm\r\n- Trộn giấm gạo, đường, muối cho đến khi hòa tan\r\n- Đổ hỗn hợp giấm vào cơm nóng và trộn đều\r\n- Để cơm nguội đến nhiệt độ phòng\r\n\r\nBước 3: Chuẩn bị nguyên liệu\r\n- Thái mỏng cá hồi, bơ, dưa leo\r\n- Cắt rong biển thành các tấm nhỏ vừa phải\r\n\r\nBước 4: Cuộn sushi\r\n- Đặt một tấm rong biển lên chiếu tre\r\n- Phết một lớp cơm mỏng và đều lên rong biển, chừa lại 1cm ở mép trên\r\n- Xếp nhân (cá hồi, bơ, dưa leo) vào giữa\r\n- Cuộn chặt và cắt thành từng khoanh nhỏ\r\n- Dùng kèm với wasabi và nước tương', '1743749678_Sushi-va-Sashimi.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 2, 3, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:09'),
(3, 'Pizza Margherita', 'Pizza Margherita là một loại pizza truyền thống của Ý với topping đơn giản gồm cà chua, phô mai mozzarella, lá húng quế tươi, muối và dầu ô liu.', '- 500g bột mì\r\n- 300ml nước ấm\r\n- 10g men khô\r\n- 10g muối\r\n- 15ml dầu ô liu\r\n- 200g cà chua cắt lát\r\n- 200g phô mai mozzarella\r\n- Lá húng quế tươi\r\n- Dầu ô liu extra virgin\r\n- Muối và hạt tiêu', 'Bước 1: Làm bột pizza\r\n- Hòa men vào nước ấm, để 5-10 phút cho men hoạt động\r\n- Trộn bột mì, muối trong một tô lớn\r\n- Đổ hỗn hợp nước men vào, trộn đều và nhào bột khoảng 10 phút\r\n- Thêm dầu ô liu, tiếp tục nhào cho đến khi bột mịn và đàn hồi\r\n- Để bột nghỉ và nở gấp đôi (khoảng 1-2 giờ)\r\n\r\nBước 2: Làm nước sốt\r\n- Xay nhuyễn cà chua\r\n- Thêm muối, hạt tiêu và lá húng quế\r\n- Đun sôi nhẹ hỗn hợp khoảng 20 phút\r\n\r\nBước 3: Tạo hình và nướng pizza\r\n- Chia bột thành 2-3 phần, cán mỏng thành hình tròn\r\n- Phết nước sốt cà chua lên mặt bánh\r\n- Rải phô mai mozzarella\r\n- Nướng trong lò ở nhiệt độ cao nhất (250°C) khoảng 10-15 phút\r\n\r\nBước 4: Hoàn thiện\r\n- Lấy bánh ra khỏi lò\r\n- Rải lá húng quế tươi lên trên\r\n- Nhỏ một ít dầu ô liu extra virgin\r\n- Thưởng thức khi còn nóng', '1743750468_images.jfif', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 3, 4, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:14'),
(4, 'Gỏi cuốn tôm thịt', 'Gỏi cuốn là một món ăn truyền thống của Việt Nam, bao gồm các nguyên liệu như tôm, thịt lợn, rau sống, bún... cuốn trong bánh tráng.', '- 20 cái bánh tráng\r\n- 200g tôm sú\r\n- 200g thịt ba chỉ\r\n- 100g bún\r\n- Xà lách\r\n- Húng quế\r\n- Ngò gai\r\n- Hẹ\r\n- Đồ chấm: tương đen trộn với tương ớt, lạc rang giã nhỏ, tỏi băm nhỏ', 'Bước 1: Sơ chế nguyên liệu\r\n- Tôm luộc chín, bóc vỏ, cắt đôi theo chiều dọc\r\n- Thịt ba chỉ luộc chín và cắt thành từng lát mỏng\r\n- Bún ngâm nước nóng, để ráo\r\n- Rửa sạch các loại rau và để ráo\r\n\r\nBước 2: Cuốn gỏi cuốn\r\n- Nhúng bánh tráng vào nước cho mềm\r\n- Đặt bánh tráng ra đĩa\r\n- Xếp rau, bún, thịt, tôm lên trên\r\n- Gấp hai mép bánh tráng và cuốn chặt\r\n- Lặp lại với các bánh tráng còn lại\r\n\r\nBước 3: Làm nước chấm\r\n- Trộn tương đen với ít nước\r\n- Thêm tương ớt, tỏi băm, lạc rang giã nhỏ\r\n- Khuấy đều\r\n\r\nBước 4: Thưởng thức\r\n- Dùng gỏi cuốn với nước chấm', '1743749420_image.jpeg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 1, 2, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:17'),
(5, 'Tiramisu', 'Tiramisu là một món tráng miệng nổi tiếng của Ý với hương vị cà phê đậm đà hòa quyện cùng kem phô mai mềm mịn và bánh quy Savoiardi.', '- 500g phô mai mascarpone\r\n- 100g đường\r\n- 4 quả trứng gà\r\n- 200g bánh quy Savoiardi (bánh ngón tay)\r\n- 250ml cà phê đen đã pha\r\n- 2 muỗng canh rượu Kahlua (tùy chọn)\r\n- 2 muỗng canh bột cacao', 'Bước 1: Chuẩn bị cà phê\r\n- Pha cà phê đen đặc\r\n- Để nguội và thêm rượu Kahlua (nếu dùng)\r\n- Đổ hỗn hợp ra đĩa nông\r\n\r\nBước 2: Làm kem mascarpone\r\n- Tách lòng đỏ và lòng trắng trứng\r\n- Đánh lòng đỏ với 50g đường đến khi bông mịn\r\n- Trộn từ từ phô mai mascarpone vào hỗn hợp lòng đỏ\r\n- Đánh lòng trắng trứng với 50g đường còn lại đến khi tạo thành kem cứng\r\n- Trộn nhẹ hỗn hợp lòng trắng vào hỗn hợp mascarpone\r\n\r\nBước 3: Lắp bánh\r\n- Nhúng nhanh bánh quy Savoiardi vào hỗn hợp cà phê\r\n- Xếp một lớp bánh quy vào đáy hộp\r\n- Phủ một lớp kem mascarpone\r\n- Tiếp tục lớp bánh quy và kem cho đến khi hết nguyên liệu, kết thúc bằng lớp kem\r\n\r\nBước 4: Hoàn thiện\r\n- Phủ đều bột cacao lên mặt bánh\r\n- Bảo quản trong tủ lạnh ít nhất 4 giờ, tốt nhất là qua đêm\r\n- Thưởng thức khi lạnh', '1743750147_tiramisu-la-gi-y-nghia-cua-banh-tiramisu-202108082258460504.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 9, 3, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:20'),
(6, 'Salad trái cây', 'Salad trái cây là món ăn nhẹ, tươi mát và bổ dưỡng, phù hợp cho những ngày nóng hoặc làm món tráng miệng sau bữa chính.', '- 1 quả táo\r\n- 1 quả lê\r\n- 2 quả kiwi\r\n- 1 chùm nho\r\n- 1 quả cam\r\n- 100g dâu tây\r\n- 2 muỗng canh mật ong\r\n- 1 muỗng canh nước cốt chanh\r\n- 1 muỗng cà phê bạc hà thái nhỏ', 'Bước 1: Sơ chế trái cây\r\n- Rửa sạch tất cả trái cây\r\n- Gọt vỏ táo, lê, kiwi, cam\r\n- Cắt táo và lê thành từng khúc vừa ăn\r\n- Cắt kiwi thành từng lát mỏng\r\n- Tách múi cam\r\n- Bỏ cuống dâu tây và cắt đôi\r\n- Tách nho thành từng trái\r\n\r\nBước 2: Làm nước sốt\r\n- Trộn đều mật ong và nước cốt chanh\r\n- Thêm bạc hà thái nhỏ và khuấy đều\r\n\r\nBước 3: Hoàn thiện\r\n- Cho tất cả trái cây vào tô lớn\r\n- Rưới nước sốt lên trên\r\n- Trộn nhẹ nhàng để trái cây không bị dập\r\n- Để lạnh khoảng 30 phút trước khi dùng', '1743750703_giai-ngan-ngay-tet-voi-mon-salad-hoa-qua-kieu-han-quoc-202205241325570525.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 8, 4, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:24'),
(7, 'Cơm chiên dương châu', 'Cơm chiên dương châu là món ăn phổ biến trong ẩm thực Trung Hoa, với nhiều loại nguyên liệu được chiên cùng cơm tạo nên hương vị đặc trưng.', '- 3 chén cơm nguội\r\n- 100g thịt xá xíu (hoặc thịt lợn, gà)\r\n- 2 quả trứng\r\n- 50g đậu Hà Lan\r\n- 1 củ cà rốt\r\n- 50g tôm khô\r\n- 2 cây hành lá\r\n- Gia vị: nước mắm, hạt nêm, tiêu, dầu ăn', 'Bước 1: Sơ chế nguyên liệu\r\n- Tôm khô ngâm nở, cắt nhỏ\r\n- Thịt xá xíu thái hạt lựu\r\n- Cà rốt gọt vỏ, thái hạt lựu\r\n- Đậu Hà Lan luộc chín\r\n- Hành lá rửa sạch, thái nhỏ\r\n- Trứng đánh tan\r\n\r\nBước 2: Chiên trứng\r\n- Đổ trứng vào chảo nóng có dầu\r\n- Đảo đều và tán nhỏ\r\n\r\nBước 3: Xào nguyên liệu\r\n- Phi thơm hành\r\n- Cho tôm khô, thịt, cà rốt vào xào\r\n- Thêm đậu Hà Lan, đảo đều\r\n\r\nBước 4: Chiên cơm\r\n- Cho cơm vào chảo, đảo đều\r\n- Thêm trứng đã chiên\r\n- Nêm gia vị vừa ăn\r\n- Đảo đều cho cơm tơi ra\r\n- Rắc hành lá, tắt bếp\r\n\r\nBước 5: Thưởng thức\r\n- Múc cơm ra đĩa, rắc tiêu\r\n- Dùng nóng', '1743749591_hq720.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 2, 2, 'approved', '2025-04-04 06:23:45', '2025-11-10 11:55:27'),
(8, 'Bún Đậu mắm Tôm', 'Món ăn dân dã truyền thống của miền bắc Việt Nam', '-500g bún lá (có thể chọn bún sợi nếu không có bún lá)\r\n-2 – 3 miếng đậu hũ (đậu phụ) để rán ăn kèm\r\n-300 – 400g thịt chân giò rút xương\r\n-300g chả cốm\r\n-50g mắm tôm (có thể chuẩn bị thêm tùy sở thích của bạn)\r\n-3 quả ớt hiểm\r\n-1 củ tỏi tươi\r\n-2 – 4 quả tắc (quất)\r\n-Rau kinh giới, tía tô, dưa leo, xà lách,… để ăn kèm\r\n-Các loại gia vị cơ bản trong bếp như đường, bột ngọt, dầu ăn,…\r\n-1 thìa lớn rượu trắng (hoặc giấm)', 'Các bước sơ chế những nguyên liệu đã chuẩn bị\r\nBước 1: Luộc thịt\r\nBước 2: Sơ chế các loại rau, củ ăn kèm\r\nBước 3: Chiên đậu hũ (đậu phụ)\r\nBước 4: Chiên chả cốm ăn kèm\r\nBước 5: Ép sợi bún thành từng lá bún\r\nBước 6: Trình bày ra đĩa/mẹt', '1744084110_1744071982_bun-dau-mam-tom-da-nang-2.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 1, 1, 'approved', '2025-04-08 03:48:30', '2025-12-01 16:18:53'),
(9, 'sádf', 'fsadfasdf', 'fasdfasf', 'fasdfsadf', '1744892378_490505103_3631641027134407_5260522345409253318_n.jpg', 'https://www.youtube.com/watch?v=MdvP4t2OZXU', 7, 14, 'rejected', '2025-04-17 12:19:38', '2025-11-10 11:55:32'),
(11, 'ewfaewaf', 'ewfewaf', 'fewafaewf', 'feawfeaw', 'no-image.jpg', NULL, 5, 16, 'rejected', '2025-06-01 02:01:51', '2025-09-08 06:14:12'),
(12, 'gẻgreg', 'ẻgerg', 'ẻgeg', 'gẻgreger', '1763001963_t2utmm1a.mzy.pdf', NULL, 5, 1, 'pending', '2025-11-13 02:46:03', '2025-11-13 02:46:03'),
(13, 'd&acirc;dasd', '&aacute;dsad', '&aacute;dasd', '&aacute;dasdeqw', '1763208950_69186ef62c0c3.jpg', NULL, 7, 1, 'pending', '2025-11-15 12:15:50', '2025-11-15 12:15:50'),
(14, 'ffdfds', 'fdsfdsf', 'dfsdsfsdf', 'dsfdsfsdf', '1763452037_chíae1.png', NULL, 3, 1, 'pending', '2025-11-18 07:47:17', '2025-11-18 07:47:17'),
(15, 'Việt Nam', 'Việt Nam', 'Việt Nam', 'Việt Nam', '1766144399_6945398f0612f.jpg', NULL, 6, 33, 'pending', '2025-12-19 11:39:59', '2025-12-19 11:39:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại người dùng',
  `address` text DEFAULT NULL COMMENT 'Địa chỉ người dùng',
  `avatar` varchar(255) DEFAULT 'default-avatar.png' COMMENT 'Ảnh đại diện người dùng',
  `password` varchar(255) NOT NULL,
  `role` enum('user','manager','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `avatar`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '0922789789', '', 'avatar_1_1763002570.jpg', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'admin', '2025-04-04 06:23:44'),
(2, 'Nguyễn Văn A', 'nguyenvana@example.com', NULL, NULL, 'default-avatar.png', '$2y$10$7rLSvRVyTQORapkDOqmkhetjF6H9lJHngr4hJMSM2lHKM0M/2/5h.', 'user', '2025-04-04 06:23:44'),
(3, 'Trần Thị B', 'tranthib@example.com', NULL, NULL, 'default-avatar.png', '$2y$10$7rLSvRVyTQORapkDOqmkhetjF6H9lJHngr4hJMSM2lHKM0M/2/5h.', 'user', '2025-04-04 06:23:44'),
(4, 'Lê Văn C', 'levanc@example.com', NULL, NULL, 'default-avatar.png', '$2y$10$7rLSvRVyTQORapkDOqmkhetjF6H9lJHngr4hJMSM2lHKM0M/2/5h.', 'user', '2025-04-04 06:23:44'),
(5, 'AnhLan', 'lan@vietnam.com', NULL, NULL, 'default-avatar.png', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'admin', '2025-04-04 06:38:51'),
(6, 'Tran Phuc', 'phuc@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 09:12:44'),
(7, 'Đặng Thị F', 'dangthif@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-04 18:23:45'),
(8, 'Bùi Văn G', 'buivang@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-04 20:45:12'),
(9, 'Vũ Thị H', 'vuthih@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-04 22:12:34'),
(10, 'Phan Văn I', 'phanvani@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 00:34:56'),
(11, 'Đỗ Thị J', 'dothij@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 02:56:23'),
(12, 'Trịnh Văn K', 'trinhvank@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'admin', '2025-04-05 05:34:56'),
(13, 'Lý Thị L', 'lythil@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 08:12:34'),
(14, 'Đinh Văn MM', 'dinhvanm@dtu.edu.vn', '04444222222', 'Nguyen Van A', '1763454137_1763197183_2_testanhquá3MB.jpg', '$2y$12$7Lqi.nAVaGos85HW3G/WjOSxODAijnUQJuXqmZBOfnNC.mKcjXTfu', 'user', '2025-04-05 10:34:56'),
(15, 'Mai Thị N', 'maithin@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 13:45:12'),
(16, 'Trương Văn O', 'truongvano@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 19:34:56'),
(17, 'Ngô Thị P', 'ngothip@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-05 21:56:23'),
(18, 'Lâm Văn Q', 'lamvanq@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-06 00:34:56'),
(19, 'Võ Thị R', 'vothir@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-06 03:12:34'),
(20, 'Phạm Văn S', 'phamvans@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-06 05:45:12'),
(22, 'Trần Văn U', 'tranvanu@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-06 09:34:56'),
(23, 'Lê Thị V', 'lethiv@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-06 11:45:12'),
(24, 'Đỗ Văn W', 'dovanw@dtu.edu.vn', NULL, NULL, 'default-avatar.png', '$2y$10$rQwnES9IEvdTEXDwQaKBoernQ6e.7nkwDPgNzph6wBZxAz2HeV08m', 'user', '2025-04-06 14:12:34'),
(27, 'Quản Lý Nội Dung', 'manager.content@congthucnauan.com', NULL, NULL, 'default-avatar.png', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', '2025-09-08 06:59:10'),
(28, 'Quản L&yacute; Kh&oacute;a Học', 'manager.courses@congthucnauan.com', '', '', 'default-avatar.png', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', '2025-09-08 06:59:10'),
(29, 'Quản Lý Thanh Toán', 'manager.payment@congthucnauan.com', NULL, NULL, 'default-avatar.png', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', '2025-09-08 06:59:10'),
(30, 'Quản Lý Bán Hàng', 'manager.sales@congthucnauan.com', NULL, NULL, 'default-avatar.png', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', '2025-09-08 06:59:10'),
(31, 'anh ;lan', 'abc@test.com', NULL, NULL, 'default-avatar.png', '$2y$10$hrRlV8mq5LGPehAo4Lk18.XN8W0d4ArkUVojF/dCS1owErOv4cpja', 'user', '2025-10-11 11:42:20'),
(32, 'lan1', 'lan@anhlan.com', NULL, NULL, 'default-avatar.png', '$2y$10$NQamTqcgQVylgvO.EdMcfeYXOxwpNeHxkTACkQpSuIhERAsGYampK', 'admin', '2025-11-15 12:39:31'),
(33, 'admin', 'lan1@anhlan.com', NULL, NULL, 'default-avatar.png', '$2y$10$HhdP6CcRJkgE1xcSNoWjPulVicvyhYeBkgndkSpj3zTfPW6wV.x8G', 'manager', '2025-11-15 12:39:54'),
(34, 'Test User', 'testapi@test.com', '0123456789', NULL, 'default-avatar.png', '$2y$12$v8f6TrPRVmIPENlxNpDTFeF.x3CUXlAVdGJloA/89P5NhNEyHHM9O', 'user', '2025-11-16 19:40:12'),
(35, 'New User', 'newuser@test.com', '0987654321', NULL, 'default-avatar.png', '$2y$12$OU765UNqnXLRmLx.kvUkhezN8qzfUqcJZQz8nVUOxZ/CmDDLWMlai', 'user', '2025-11-16 19:42:11');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classroom_id` (`classroom_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_course_unique` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Chỉ mục cho bảng `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Chỉ mục cho bảng `course_lesson_completion`
--
ALTER TABLE `course_lesson_completion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_completion` (`user_id`,`lesson_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Chỉ mục cho bảng `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `forum_likes`
--
ALTER TABLE `forum_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_recipe_id` (`recipe_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_is_pinned` (`is_pinned`);

--
-- Chỉ mục cho bảng `forum_post_tags`
--
ALTER TABLE `forum_post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- Chỉ mục cho bảng `forum_tags`
--
ALTER TABLE `forum_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD UNIQUE KEY `unique_slug` (`slug`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_gateway_transaction_id` (`gateway_transaction_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Chỉ mục cho bảng `payment_gateway_config`
--
ALTER TABLE `payment_gateway_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_gateway_key` (`gateway`,`config_key`),
  ADD KEY `idx_gateway` (`gateway`);

--
-- Chỉ mục cho bảng `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_gateway` (`gateway`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`recipe_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_phone` (`phone`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `course_lesson_completion`
--
ALTER TABLE `course_lesson_completion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `forum_likes`
--
ALTER TABLE `forum_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `forum_tags`
--
ALTER TABLE `forum_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT cho bảng `payment_gateway_config`
--
ALTER TABLE `payment_gateway_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_enrollments_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD CONSTRAINT `course_lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `course_lesson_completion`
--
ALTER TABLE `course_lesson_completion`
  ADD CONSTRAINT `course_lesson_completion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_lesson_completion_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_lesson_completion_ibfk_3` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD CONSTRAINT `forum_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `forum_comments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `forum_likes`
--
ALTER TABLE `forum_likes`
  ADD CONSTRAINT `forum_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `forum_post_tags`
--
ALTER TABLE `forum_post_tags`
  ADD CONSTRAINT `forum_post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `forum_tags` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `fk_payment_logs_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
