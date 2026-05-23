-- =============================================
-- MIGRATION: Thêm Manager Role
-- Tạo ngày: <?php echo date('Y-m-d H:i:s'); ?>
-- =============================================

-- 1. Cập nhật enum role để thêm 'manager'
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('user', 'manager', 'admin') DEFAULT 'user';

-- 2. Thêm user Manager mẫu
-- Mật khẩu cho tất cả account manager: "manager123"
INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`) VALUES
('Quản Lý Nội Dung', 'manager.content@congthucnauan.com', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', NOW()),
('Quản Lý Khóa Học', 'manager.courses@congthucnauan.com', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', NOW()),
('Quản Lý Thanh Toán', 'manager.payment@congthucnauan.com', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', NOW()),
('Quản Lý Bán Hàng', 'manager.sales@congthucnauan.com', '$2y$10$y/r9ZBFB5xw5P2kIhDdzM.PaxYW1HQOs4Sv.QrcVRLD2O/1FQ6inS', 'manager', NOW());

-- 3. Cập nhật một số user hiện tại thành manager (tùy chọn)
-- UPDATE `users` SET `role` = 'manager' WHERE `id` IN (5, 12);

-- 4. Thêm bảng permissions nếu cần (tùy chọn cho tương lai)
-- CREATE TABLE `role_permissions` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `role` enum('user','manager','admin') NOT NULL,
--   `permission` varchar(50) NOT NULL,
--   `allowed` tinyint(1) DEFAULT 1,
--   `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `role_permission` (`role`, `permission`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insert default permissions cho Manager (tùy chọn)
-- INSERT INTO `role_permissions` (`role`, `permission`, `allowed`) VALUES
-- ('manager', 'manage_recipes', 1),
-- ('manager', 'manage_courses', 1), 
-- ('manager', 'manage_contacts', 1),
-- ('manager', 'view_reports', 1),
-- ('manager', 'manage_users', 0),
-- ('manager', 'manage_payments', 0),
-- ('manager', 'system_settings', 0);

-- =============================================
-- HƯỚNG DẪN SỬ DỤNG:
-- =============================================

-- 1. ĐĂNG NHẬP VỚI ACCOUNT MANAGER:
--    Email: manager.content@congthucnauan.com
--    Password: manager123
--    
--    Hoặc các account khác:
--    - manager.courses@congthucnauan.com / manager123
--    - manager.payment@congthucnauan.com / manager123  
--    - manager.sales@congthucnauan.com / manager123

-- 2. TRUY CẬP MANAGER DASHBOARD:
--    http://localhost/webcongthucnauan/manager

-- 3. CHỨC NĂNG MANAGER CÓ THỂ TRUY CẬP:
--    ✅ Quản lý công thức (duyệt/từ chối)
--    ✅ Quản lý khóa học
--    ✅ Xem liên hệ từ khách hàng
--    ✅ Xem báo cáo thống kê
--    ❌ Quản lý người dùng (chỉ admin)
--    ❌ Cài đặt hệ thống (chỉ admin)

-- 4. PHÂN QUYỀN:
--    USER < MANAGER < ADMIN
--    
--    - User: Chỉ xem và tạo nội dung
--    - Manager: Quản lý nội dung, khóa học, liên hệ
--    - Admin: Full quyền quản trị hệ thống

-- =============================================
-- KẾT QUẢ SAU KHI CHẠY MIGRATION:
-- =============================================

-- 1. Bảng users sẽ có role mới: 'manager'
-- 2. Có 4 tài khoản manager mẫu
-- 3. Navbar sẽ hiển thị link "Quản lý" cho manager
-- 4. Manager có thể truy cập /manager dashboard
-- 5. API cũng hỗ trợ manager role (nếu cần)

SELECT 'Migration completed successfully!' as status; 