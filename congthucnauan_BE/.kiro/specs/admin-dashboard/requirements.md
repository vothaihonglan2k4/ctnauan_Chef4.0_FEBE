# Requirements Document

## Introduction

Hệ thống Admin Dashboard cung cấp giao diện quản trị tổng quan cho quản trị viên (admin) để theo dõi và quản lý toàn bộ dữ liệu hệ thống web công thức nấu ăn. Dashboard hiển thị các thống kê quan trọng, biểu đồ trực quan và cung cấp truy cập nhanh đến các chức năng quản lý.

## Glossary

- **Admin Dashboard**: Trang bảng điều khiển dành cho quản trị viên
- **System**: Hệ thống web công thức nấu ăn
- **User**: Người dùng đã đăng ký tài khoản
- **Recipe**: Công thức nấu ăn được chia sẻ
- **Course**: Khóa học nấu ăn
- **Forum Post**: Bài viết trong diễn đàn
- **Payment**: Giao dịch thanh toán
- **Admin User**: Người dùng có vai trò quản trị viên (role = 'admin')
- **Statistics Card**: Thẻ hiển thị số liệu thống kê
- **Chart Component**: Component hiển thị biểu đồ
- **Quick Action**: Liên kết truy cập nhanh đến chức năng quản lý

## Requirements

### Requirement 1

**User Story:** Là một quản trị viên, tôi muốn xem tổng quan thống kê hệ thống, để có thể nắm bắt tình hình hoạt động của website.

#### Acceptance Criteria

1. WHEN Admin_User truy cập route "/admin", THE System SHALL hiển thị trang Admin Dashboard với các thống kê tổng quan
2. THE System SHALL hiển thị Statistics Card "Người dùng" với icon users màu primary, border-left-primary, và số lượng người dùng mới trong tháng
3. THE System SHALL hiển thị Statistics Card "Công thức" với icon utensils màu success và border-left-success
4. THE System SHALL hiển thị Statistics Card "Khóa học" với icon chalkboard-teacher màu info và border-left-info
5. THE System SHALL hiển thị Statistics Card "Doanh thu" với icon money-bill-wave màu warning và border-left-warning
6. THE System SHALL hiển thị link "Xem chi tiết" ở footer của mỗi Statistics Card

### Requirement 2

**User Story:** Là một quản trị viên, tôi muốn xem thống kê doanh thu và giao dịch, để theo dõi tình hình tài chính của hệ thống.

#### Acceptance Criteria

1. THE System SHALL hiển thị tổng doanh thu (total_revenue) trong Statistics Card với định dạng tiền tệ VND
2. THE System SHALL hiển thị số lượng giao dịch thành công (successful_payments_count) trong Statistics Card
3. THE System SHALL hiển thị số lượng giao dịch đang chờ (pending_payments_count) trong Statistics Card
4. WHEN Admin_User xem thống kê doanh thu, THE System SHALL tính toán từ bảng payments với status = 'completed'

### Requirement 3

**User Story:** Là một quản trị viên, tôi muốn xem danh sách khóa học mới nhất, để theo dõi nội dung đào tạo được thêm vào hệ thống.

#### Acceptance Criteria

1. THE System SHALL hiển thị danh sách 3-6 khóa học mới nhất với hình ảnh, tiêu đề, mô tả
2. THE System SHALL hiển thị giá khóa học với định dạng tiền tệ VND
3. THE System SHALL hiển thị tên lớp học (classroom_name) cho mỗi khóa học
4. THE System SHALL hiển thị ngày tạo khóa học với định dạng dd/mm/yyyy
5. WHEN Admin_User click vào nút "Sửa", THE System SHALL điều hướng đến trang chỉnh sửa khóa học

### Requirement 4

**User Story:** Là một quản trị viên, tôi muốn xem danh sách người dùng mới, để theo dõi các đăng ký mới trong hệ thống.

#### Acceptance Criteria

1. THE System SHALL hiển thị danh sách người dùng đăng ký mới nhất trong bảng với các cột: Tên, Email, Ngày đăng ký, Vai trò
2. THE System SHALL hiển thị avatar mặc định cho mỗi người dùng
3. THE System SHALL hiển thị badge màu đỏ (bg-danger) cho admin và màu xám (bg-secondary) cho người dùng thường
4. THE System SHALL hiển thị số lượng người dùng mới trong tháng hiện tại với icon user-plus màu xanh
5. THE System SHALL cung cấp link "Tất cả người dùng" để điều hướng đến trang quản lý người dùng

### Requirement 5

**User Story:** Là một quản trị viên, tôi muốn xem danh sách thanh toán gần đây, để theo dõi các giao dịch trong hệ thống.

#### Acceptance Criteria

1. THE System SHALL hiển thị danh sách thanh toán gần đây trong bảng với các cột: Mã, Người dùng, Khóa học, Số tiền, Trạng thái
2. THE System SHALL hiển thị mã thanh toán dạng code (#ID)
3. THE System SHALL hiển thị số tiền với định dạng tiền tệ VND và màu xanh (text-success)
4. THE System SHALL hiển thị badge trạng thái: màu xanh (bg-success) cho "Hoàn thành", màu vàng (bg-warning) cho "Đang xử lý", màu đỏ (bg-danger) cho "Thất bại"
5. THE System SHALL cung cấp link "Tất cả thanh toán" để điều hướng đến trang quản lý thanh toán

### Requirement 6

**User Story:** Là một quản trị viên, tôi muốn hệ thống bảo mật trang admin, để chỉ người có quyền mới truy cập được.

#### Acceptance Criteria

1. WHEN User không đăng nhập truy cập "/admin", THE System SHALL chuyển hướng đến trang login
2. WHEN User đã đăng nhập nhưng role không phải 'admin' truy cập "/admin", THE System SHALL hiển thị thông báo "Bạn không có quyền truy cập" và chuyển hướng về trang chủ
3. WHEN Admin_User đăng nhập thành công, THE System SHALL lưu thông tin role trong AuthContext
4. THE System SHALL kiểm tra quyền truy cập thông qua middleware hoặc route guard

### Requirement 7

**User Story:** Là một quản trị viên, tôi muốn giao diện admin responsive, để có thể quản lý hệ thống trên nhiều thiết bị.

#### Acceptance Criteria

1. THE System SHALL hiển thị Statistics Cards theo grid layout responsive (4 cột trên desktop, 2 cột trên tablet, 1 cột trên mobile)
2. THE System SHALL điều chỉnh kích thước Chart Components phù hợp với viewport
3. THE System SHALL hiển thị navigation sidebar có thể thu gọn trên mobile
4. THE System SHALL sử dụng Bootstrap grid system hoặc CSS Flexbox/Grid

### Requirement 8

**User Story:** Là một quản trị viên, tôi muốn API endpoint cung cấp dữ liệu dashboard, để frontend có thể lấy thống kê real-time.

#### Acceptance Criteria

1. THE System SHALL cung cấp API endpoint GET "/api/v1/admin/dashboard/stats" trả về tất cả số liệu thống kê
2. THE System SHALL cung cấp API endpoint GET "/api/v1/admin/dashboard/charts" trả về dữ liệu cho các biểu đồ
3. THE System SHALL cung cấp API endpoint GET "/api/v1/admin/dashboard/recent-activities" trả về danh sách hoạt động gần đây
4. WHEN API endpoint được gọi, THE System SHALL kiểm tra Bearer token và role = 'admin'
5. IF User không có quyền admin, THEN THE System SHALL trả về HTTP status 403 với message "Unauthorized"
