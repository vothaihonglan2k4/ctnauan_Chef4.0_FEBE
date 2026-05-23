# Design Document - Admin Dashboard

## Overview

Admin Dashboard là trang quản trị tổng quan được chuyển đổi từ PHP MVC sang React SPA. Trang này hiển thị các thống kê quan trọng, danh sách hoạt động gần đây và cung cấp điều hướng nhanh đến các chức năng quản lý. Giao diện được thiết kế responsive với sidebar có thể thu gọn trên mobile.

## Architecture

### Component Structure

```
AdminDashboardPage (Main Container)
├── AdminLayout (Wrapper with Sidebar)
│   ├── AdminSidebar (Navigation)
│   │   ├── AdminLogo
│   │   ├── AdminNavigation
│   │   └── AdminUserDropdown
│   └── AdminContent (Main Content Area)
│       ├── DashboardHeader
│       ├── StatsCards (Row of 4 cards)
│       │   ├── StatCard (Người dùng)
│       │   ├── StatCard (Công thức)
│       │   ├── StatCard (Khóa học)
│       │   └── StatCard (Doanh thu)
│       ├── RecentActivitiesRow
│       │   ├── RecentUsersTable
│       │   └── RecentPaymentsTable
│       └── RecentCoursesSection
│           └── CourseCard (Grid of 3 cards)
```

### Component Breakdown

#### 1. Reusable Components

**StatCard Component**
- Props: `title`, `value`, `icon`, `iconColor`, `borderColor`, `footerLink`, `footerText`, `subText`
- Hiển thị thống kê với icon, số liệu và link chi tiết
- Responsive: 1 cột mobile, 2 cột tablet, 4 cột desktop

**RecentUsersTable Component**
- Props: `users`, `loading`
- Hiển thị bảng người dùng mới với avatar, name, email, role, created_at
- Badge màu sắc theo role

**RecentPaymentsTable Component**
- Props: `payments`, `loading`
- Hiển thị bảng thanh toán với mã, user, course, amount, status
- Badge màu sắc theo status

**CourseCard Component**
- Props: `course` (id, title, description, image, price, classroom_name, created_at)
- Hiển thị card khóa học với hình ảnh, thông tin và nút sửa

#### 2. Layout Components

**AdminSidebar Component**
- Fixed sidebar với gradient background
- Navigation items với active state
- Collapsible submenu cho "Quản lý Nội dung"
- User dropdown ở bottom
- Toggle button cho mobile
- Overlay khi mở sidebar trên mobile

**AdminLayout Component**
- Wrapper component cho tất cả admin pages
- Kiểm tra quyền admin trước khi render
- Redirect về login nếu chưa đăng nhập
- Redirect về home với thông báo nếu không phải admin

#### 3. Page Component

**AdminDashboardPage Component**
- Container chính cho dashboard
- Fetch data từ API khi component mount
- Loading states cho từng section
- Error handling

## Data Models

### Dashboard Stats Response
```typescript
interface DashboardStats {
  userCount: number;
  userCountThisMonth: number;
  recipeCount: number;
  courseCount: number;
  totalRevenue: number;
}
```

### Recent User
```typescript
interface RecentUser {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'manager' | 'user';
  avatar?: string;
  created_at: string;
}
```

### Recent Payment
```typescript
interface RecentPayment {
  id: number;
  user_name: string;
  course_title: string;
  amount: number;
  status: 'completed' | 'pending' | 'failed';
  created_at: string;
}
```

### Recent Course
```typescript
interface RecentCourse {
  id: number;
  title: string;
  description: string;
  image?: string;
  price: number;
  classroom_name: string;
  created_at: string;
}
```

## API Endpoints

### GET /api/v1/admin/dashboard/stats
**Purpose:** Lấy thống kê tổng quan

**Request Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:**
```json
{
  "userCount": 150,
  "userCountThisMonth": 12,
  "recipeCount": 320,
  "courseCount": 25,
  "totalRevenue": 45000000
}
```

### GET /api/v1/admin/dashboard/recent-users
**Purpose:** Lấy danh sách người dùng mới

**Request Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters:**
- `limit` (optional): Số lượng records, default = 10

**Response:**
```json
{
  "users": [
    {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com",
      "role": "user",
      "avatar": "avatar.jpg",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### GET /api/v1/admin/dashboard/recent-payments
**Purpose:** Lấy danh sách thanh toán gần đây

**Request Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters:**
- `limit` (optional): Số lượng records, default = 10

**Response:**
```json
{
  "payments": [
    {
      "id": 1,
      "user_name": "Nguyễn Văn A",
      "course_title": "Khóa học nấu ăn cơ bản",
      "amount": 500000,
      "status": "completed",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### GET /api/v1/admin/dashboard/recent-courses
**Purpose:** Lấy danh sách khóa học mới nhất

**Request Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters:**
- `limit` (optional): Số lượng records, default = 6

**Response:**
```json
{
  "courses": [
    {
      "id": 1,
      "title": "Khóa học nấu ăn cơ bản",
      "description": "Học các kỹ năng nấu ăn cơ bản...",
      "image": "course.jpg",
      "price": 500000,
      "classroom_name": "Lớp A1",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

## Styling Approach

### CSS Strategy
- Sử dụng Bootstrap 5 classes cho layout và components
- Custom CSS cho sidebar gradient và animations
- CSS variables cho colors và spacing
- Responsive breakpoints: mobile (<768px), tablet (768-991px), desktop (>992px)

### Color Scheme
```css
--primary-color: #0d6efd;
--success-color: #28a745;
--info-color: #17a2b8;
--warning-color: #ffc107;
--danger-color: #dc3545;
--sidebar-bg: linear-gradient(135deg, #304352, #23303f);
--border-radius: 8px;
```

### Sidebar Behavior
- Desktop (>992px): Fixed sidebar, always visible
- Mobile (<992px): Sidebar hidden by default, slide in from left
- Toggle button visible on mobile
- Overlay backdrop when sidebar open on mobile

## Error Handling

### Authentication Errors
- 401 Unauthorized: Redirect to login page
- 403 Forbidden: Show alert "Bạn không có quyền truy cập" và redirect về home

### API Errors
- Network errors: Show alert "Không thể kết nối đến server"
- 500 Server errors: Show alert "Có lỗi xảy ra, vui lòng thử lại sau"
- Empty data: Show friendly message "Chưa có dữ liệu"

### Loading States
- Skeleton loaders cho stats cards
- Spinner cho tables
- Disable buttons khi đang load

## Route Protection

### Admin Route Guard
```typescript
// Trong AdminLayout component
useEffect(() => {
  if (!isAuthenticated) {
    navigate('/login', { state: { from: location } });
    return;
  }
  
  if (!isAdmin) {
    alert('Bạn không có quyền truy cập trang này');
    navigate('/');
    return;
  }
}, [isAuthenticated, isAdmin, navigate, location]);
```

## Performance Considerations

### Data Fetching
- Fetch tất cả data song song bằng Promise.all()
- Cache data trong state để tránh re-fetch không cần thiết
- Debounce cho search/filter nếu có

### Component Optimization
- Sử dụng React.memo cho các component con không thay đổi thường xuyên
- useMemo cho computed values
- useCallback cho event handlers

### Image Optimization
- Lazy load images cho course cards
- Placeholder image khi không có ảnh
- Optimize image size (max 200px height cho course cards)

## Responsive Design

### Breakpoints
- Mobile: < 768px
  - Stats cards: 1 column
  - Tables: Horizontal scroll
  - Sidebar: Hidden, toggle button visible
  - Padding: 15px

- Tablet: 768px - 991px
  - Stats cards: 2 columns
  - Tables: Full width
  - Sidebar: Hidden, toggle button visible
  - Padding: 20px

- Desktop: > 992px
  - Stats cards: 4 columns
  - Tables: Side by side (2 columns)
  - Sidebar: Always visible
  - Padding: 30px

## Testing Strategy

### Unit Tests
- Test StatCard component với different props
- Test table components với empty/loading/error states
- Test utility functions (formatCurrency, formatDate)

### Integration Tests
- Test AdminLayout với authentication states
- Test API calls và error handling
- Test navigation và routing

### Manual Testing
- Test responsive behavior trên các devices
- Test sidebar toggle functionality
- Test all links và navigation
- Test với different user roles
