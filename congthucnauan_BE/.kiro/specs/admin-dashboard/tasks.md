# Implementation Plan - Admin Dashboard

- [x] 1. Tạo API endpoints cho admin dashboard


  - Tạo AdminDashboardController với các methods để lấy thống kê
  - Implement middleware kiểm tra quyền admin
  - Thêm routes vào api.php
  - _Requirements: 1.1, 6.4, 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 1.1 Tạo AdminDashboardController


  - Tạo file app/Http/Controllers/Api/AdminDashboardController.php
  - Implement method getStats() để lấy thống kê tổng quan
  - Implement method getRecentUsers() để lấy người dùng mới
  - Implement method getRecentPayments() để lấy thanh toán gần đây
  - Implement method getRecentCourses() để lấy khóa học mới
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 4.1, 5.1_



- [ ] 1.2 Thêm admin routes vào api.php
  - Tạo route group /api/v1/admin với middleware auth:sanctum và admin
  - Thêm route GET /dashboard/stats
  - Thêm route GET /dashboard/recent-users
  - Thêm route GET /dashboard/recent-payments


  - Thêm route GET /dashboard/recent-courses
  - _Requirements: 8.1, 8.2, 8.3, 8.4_

- [ ] 2. Tạo reusable components
  - Tạo StatCard component để hiển thị thống kê
  - Tạo RecentUsersTable component

  - Tạo RecentPaymentsTable component

  - Tạo CourseCard component
  - _Requirements: 1.2, 1.3, 1.4, 1.5, 4.1, 5.1, 3.1_

- [x] 2.1 Tạo StatCard component


  - Tạo file resources/js/Components/Admin/StatCard.jsx
  - Implement props: title, value, icon, iconColor, borderColor, footerLink, footerText, subText
  - Thêm styling với Bootstrap classes và custom CSS
  - Thêm hover effect
  - _Requirements: 1.2, 1.3, 1.4, 1.5, 1.6_


- [-] 2.2 Tạo RecentUsersTable component

  - Tạo file resources/js/Components/Admin/RecentUsersTable.jsx
  - Hiển thị bảng với columns: Tên, Email, Ngày đăng ký, Vai trò
  - Hiển thị avatar mặc định
  - Hiển thị badge màu sắc theo role
  - Xử lý empty state và loading state


  - _Requirements: 4.1, 4.2, 4.3_

- [ ] 2.3 Tạo RecentPaymentsTable component
  - Tạo file resources/js/Components/Admin/RecentPaymentsTable.jsx
  - Hiển thị bảng với columns: Mã, Người dùng, Khóa học, Số tiền, Trạng thái


  - Format số tiền với VND currency
  - Hiển thị badge màu sắc theo status
  - Xử lý empty state và loading state
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 2.4 Tạo CourseCard component
  - Tạo file resources/js/Components/Admin/CourseCard.jsx

  - Hiển thị hình ảnh khóa học với fallback image

  - Hiển thị title, description (truncate), price, classroom_name
  - Hiển thị ngày tạo với format dd/mm/yyyy
  - Thêm nút "Sửa" với link đến trang edit
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_


- [ ] 3. Tạo AdminSidebar component
  - Tạo sidebar với gradient background
  - Implement navigation items với active state
  - Thêm collapsible submenu


  - Thêm user dropdown
  - Implement toggle functionality cho mobile
  - _Requirements: 7.3_

- [ ] 3.1 Tạo AdminSidebar component structure
  - Tạo file resources/js/Components/Admin/AdminSidebar.jsx


  - Implement AdminLogo section
  - Implement navigation items với icons
  - Thêm active state highlighting
  - _Requirements: 7.3_

- [x] 3.2 Implement sidebar toggle cho mobile

  - Thêm toggle button
  - Thêm overlay backdrop
  - Implement slide animation
  - Xử lý responsive behavior
  - _Requirements: 7.3, 7.4_



- [ ] 4. Tạo AdminLayout component
  - Tạo layout wrapper cho admin pages
  - Implement route protection
  - Integrate AdminSidebar
  - Thêm responsive styling

  - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.2, 7.3, 7.4_


- [ ] 4.1 Tạo AdminLayout component với route protection
  - Tạo file resources/js/Components/Layout/AdminLayout.jsx
  - Kiểm tra isAuthenticated và isAdmin từ AuthContext
  - Redirect về /login nếu chưa đăng nhập

  - Redirect về / với alert nếu không phải admin
  - Render AdminSidebar và main content area
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 4.2 Thêm responsive styling cho AdminLayout
  - Thêm CSS cho sidebar fixed/collapsible


  - Thêm CSS cho main content margin
  - Implement breakpoints cho mobile/tablet/desktop
  - Thêm smooth transitions
  - _Requirements: 7.1, 7.2, 7.3, 7.4_


- [ ] 5. Tạo AdminDashboardPage component
  - Tạo main dashboard page component
  - Fetch data từ API endpoints
  - Integrate tất cả sub-components

  - Implement loading và error states
  - _Requirements: 1.1, 2.1, 4.1, 5.1, 3.1_

- [ ] 5.1 Tạo AdminDashboardPage với data fetching
  - Tạo file resources/js/Pages/Admin/AdminDashboardPage.jsx
  - Implement useEffect để fetch stats, users, payments, courses
  - Sử dụng Promise.all() để fetch song song
  - Xử lý loading states
  - Xử lý error states với try-catch
  - _Requirements: 1.1, 8.1, 8.2, 8.3_

- [ ] 5.2 Integrate components vào AdminDashboardPage
  - Render DashboardHeader với title và description



  - Render 4 StatCard components trong row
  - Render RecentUsersTable và RecentPaymentsTable trong row
  - Render RecentCoursesSection với CourseCard grid
  - Thêm responsive grid layout
  - _Requirements: 1.2, 1.3, 1.4, 1.5, 4.1, 5.1, 3.1, 7.1, 7.2_

- [ ] 6. Thêm admin route vào router
  - Thêm route /admin vào router.jsx
  - Wrap với AdminLayout
  - Test navigation
  - _Requirements: 1.1, 6.1_

- [ ] 6.1 Cập nhật router.jsx với admin routes
  - Import AdminDashboardPage
  - Thêm route path: 'admin' với element: AdminDashboardPage


  - Đảm bảo route được wrap trong AuthProvider
  - _Requirements: 1.1, 6.1_

- [ ] 7. Tạo admin middleware cho Laravel
  - Tạo middleware CheckAdmin
  - Kiểm tra user role === 'admin'


  - Return 403 nếu không phải admin
  - Register middleware trong Kernel
  - _Requirements: 6.2, 6.3, 6.4, 8.4, 8.5_

- [ ] 7.1 Tạo CheckAdmin middleware
  - Tạo file app/Http/Middleware/CheckAdmin.php
  - Implement logic kiểm tra $request->user()->role === 'admin'
  - Return response()->json(['message' => 'Unauthorized'], 403) nếu không phải admin
  - Register middleware alias 'admin' trong bootstrap/app.php
  - _Requirements: 6.2, 6.3, 6.4, 8.4, 8.5_

- [ ] 8. Test và polish
  - Test tất cả API endpoints với Postman/Thunder Client
  - Test responsive design trên mobile/tablet/desktop
  - Test authentication và authorization
  - Fix bugs và polish UI
  - _Requirements: All_
