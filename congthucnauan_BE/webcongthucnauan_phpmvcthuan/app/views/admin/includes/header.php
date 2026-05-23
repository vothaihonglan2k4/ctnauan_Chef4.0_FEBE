<?php
// Không cần tải bootstrap.php nữa vì đã điều chỉnh code trong từng view
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - Công thức nấu ăn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/style.css">
    <!-- End of CSS links -->
    <style>
        :root {
            --sidebar-width: 220px;
            --primary-color: #0d6efd;
            --sidebar-bg: linear-gradient(135deg, #304352, #23303f);
            --border-radius: 8px;
        }
        
        html, body {
            height: 100%;
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }
        
        /* Sidebar styling */
        .sidebar {
            background: var(--sidebar-bg);
            color: #fff;
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 1030;
            box-shadow: 0 0 15px rgba(0,0,0,.1);
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        /* Close button for sidebar */
        #closeSidebar {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 1031;
        }
        
        #closeSidebar:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }
        
        /* Main content styling */
        .main-content-wrapper {
            width: 100%;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            flex: 1;
            transition: all 0.3s ease;
            background-color: #f5f7fa;
            position: relative;
            overflow-x: hidden;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
            width: 100%;
            min-height: calc(100vh - 60px); /* Trừ đi chiều cao của footer */
        }
        
        /* Sidebar links */
        .sidebar a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            display: block;
            padding: 13px 20px;
            margin: 4px 12px;
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            position: relative;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateY(-1px);
        }
        
        .sidebar a.active {
            background-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.4);
        }
        
        .sidebar .nav-header {
            font-weight: 500;
            color: #a3abb5;
            padding: 10px 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            transition: all 0.2s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            font-weight: 500;
            padding: 15px 20px;
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.075);
        }
        
        /* Button styling */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 9px 18px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Table styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .table td, .table th {
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        
        /* Badge styling */
        .badge {
            padding: 7px 12px;
            font-weight: 500;
            border-radius: 30px;
        }
        
        /* Dropdown styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            border-radius: var(--border-radius);
            padding: 10px 0;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
        }
        
        /* Alert styling */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            padding: 15px 20px;
        }
        
        /* Breadcrumb styling */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        /* Form controls */
        .form-control, .form-select {
            border-radius: var(--border-radius);
            padding: 12px 15px;
            border: 1px solid #dce4ec;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: rgba(13, 110, 253, 0.4);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        /* Admin logo styling */
        .admin-logo {
            font-weight: 700;
            font-size: 1.25rem;
            padding: 20px 15px;
            background: rgba(0,0,0,0.15);
            margin-bottom: 15px;
            text-align: center;
            letter-spacing: 1px;
        }
        
        .admin-logo i {
            margin-right: 10px;
            font-size: 1.4rem;
        }
        
        /* Page header */
        .page-header {
            margin-bottom: 25px;
            position: relative;
        }
        
        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2d3748;
        }
        
        .page-description {
            color: #718096;
            font-size: 1rem;
            max-width: 80%;
        }
        
        /* Footer styling */
        .footer {
            padding: 20px 30px;
            color: #718096;
            border-top: 1px solid rgba(0,0,0,0.05);
            background-color: #fff;
            font-size: 0.9rem;
            width: 100%;
        }
        
        /* Status badges */
        .status-badge {
            border-radius: 30px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Toggle button for sidebar */
        #sidebarCollapse {
            background: #304352;
            color: white;
            border: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1050;
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
        /* Toggle button when sidebar is closed */
        .sidebar-closed #sidebarCollapse {
            display: block !important;
        }
        
        /* Responsive styling */
        @media (min-width: 992px) {
            .main-content-wrapper {
                width: calc(100% - var(--sidebar-width));
                margin-left: var(--sidebar-width);
            }
        }
        
        @media (max-width: 991.98px) {
            :root {
                --sidebar-width: 220px;
            }
            
            .sidebar {
                left: -220px;
                transition: all 0.3s ease-in-out;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content-wrapper {
                margin-left: 0;
                width: 100%;
                transition: all 0.3s ease-in-out;
            }
            
            #sidebarCollapse {
                display: block;
            }
            
            .main-content {
                padding: 20px 15px;
                padding-top: 70px;
            }
            
            .overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
                opacity: 0;
                transition: all 0.5s ease-in-out;
                top: 0;
                left: 0;
            }
            
            .overlay.active {
                display: block;
                opacity: 1;
            }
        }
        
        @media (max-width: 767.98px) {
            .page-header {
                text-align: center;
            }
            
            .page-description {
                max-width: 100%;
            }
            
            .main-content {
                padding: 15px 10px;
                padding-top: 70px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .table td, .table th {
                padding: 10px;
            }
        }
    </style>
    <!-- Custom CSS for Submenu Items -->
    <style>
        .sidebar .collapse-inner {
            background-color: transparent !important; /* Đảm bảo nền trong suốt */
            padding-top: 0.25rem; 
            padding-bottom: 0.25rem;
            margin-left: 15px; /* Tăng lề trái */
            border-left: 2px solid rgba(255, 255, 255, 0.1); /* Đường kẻ phân cấp */
            padding-left: 10px; /* Tăng padding trái */
        }

        .sidebar .collapse-inner .collapse-item {
            color: rgba(255, 255, 255, 0.8); 
            padding: 8px 15px; /* Padding Top/Bottom, Left/Right */
            margin: 2px 0; /* Margin Top/Bottom */
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            display: block;
            font-size: 0.9rem;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            box-shadow: none !important; /* Loại bỏ bóng đổ */
        }

        .sidebar .collapse-inner .collapse-item:hover {
            background-color: rgba(255, 255, 255, 0.1); 
            color: #fff;
            box-shadow: none !important; /* Loại bỏ bóng đổ khi hover */
        }

        .sidebar .collapse-inner .collapse-item.active {
            background-color: rgba(255, 255, 255, 0.15); 
            color: #fff;
            font-weight: 500;
            box-shadow: none !important; /* Loại bỏ bóng đổ khi active */
        }
        
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar toggle button for mobile -->
        <button type="button" id="sidebarCollapse" class="btn">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Overlay for mobile -->
        <div class="overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar">
            <button type="button" id="closeSidebar" class="btn-close btn-close-white" aria-label="Close"></button>
            
            <div class="admin-logo">
                <i class="fas fa-utensils"></i>
                <span>ADMIN PANEL</span>
            </div>
            <div class="px-2">
                <ul class="nav nav-pills flex-column mb-auto list-unstyled">
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == URL_ROOT . '/admin') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Bảng điều khiển
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin/users" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-users me-2"></i>
                            Quản lý người dùng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin/categories" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-folder me-2"></i>
                            Quản lý danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/recipes" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/recipes') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-utensils me-2"></i>
                            Quản lý công thức
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link collapsed <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/manageRecipes') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/manageComments') !== false) ? 'active' : ''; ?>" href="#" data-bs-toggle="collapse" data-bs-target="#collapseContentManagement" aria-expanded="false" aria-controls="collapseContentManagement">
                            <i class="fas fa-file-alt me-2"></i>
                            <span>Quản lý Nội dung</span>
                        </a>
                        <div id="collapseContentManagement" class="collapse <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/manageRecipes') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/manageComments') !== false) ? 'show' : ''; ?>" aria-labelledby="headingContentManagement">
                            <div class="py-2 collapse-inner rounded">
                                <a class="collapse-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/manageRecipes') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/admin/manageRecipes">Duyệt Công thức</a>
                                <a class="collapse-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/manageComments') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/admin/manageComments">Quản lý Bình luận</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin/courses" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/courses') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/lessons') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-chalkboard-teacher me-2"></i>
                            Quản lý khóa học
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin/classrooms" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/classrooms') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-door-open me-2"></i>
                            Quản lý phòng học
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin/payments" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/payments') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Quản lý thanh toán
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URL_ROOT; ?>/admin/reports" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar me-2"></i>
                            Báo cáo & Thống kê
                        </a>
                    </li>
                </ul>
                
                <hr class="my-3 bg-light opacity-25">
                
                <div class="dropdown mb-3">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle p-3" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo URL_ROOT; ?>/public/img/admin-avatar.png" alt="" width="40" height="40" class="rounded-circle me-2 border border-2 border-white">
                        <div>
                            <strong class="d-block"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin'; ?></strong>
                            <small class="text-light opacity-75">Administrator</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users/profile"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/admin/settings"><i class="fas fa-cog me-2"></i>Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main content wrapper -->
        <div class="main-content-wrapper">
            <!-- Main content -->
            <div class="main-content">
                <!-- Content goes here -->

<script>
    // Toggle sidebar on mobile and desktop
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        const closeSidebarButton = document.getElementById('closeSidebar');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');
        const mainContentWrapper = document.querySelector('.main-content-wrapper');
        const wrapper = document.querySelector('.wrapper');
        
        // Hàm mở sidebar
        function openSidebar() {
            sidebar.style.left = '0';
            mainContentWrapper.style.marginLeft = 'var(--sidebar-width)';
            mainContentWrapper.style.width = 'calc(100% - var(--sidebar-width))';
            wrapper.classList.remove('sidebar-closed');
            
            if (window.innerWidth < 992) {
                overlay.classList.add('active');
            }
        }
        
        // Hàm đóng sidebar
        function closeSidebar() {
            sidebar.style.left = '-220px';
            mainContentWrapper.style.marginLeft = '0';
            mainContentWrapper.style.width = '100%';
            wrapper.classList.add('sidebar-closed');
            
            if (window.innerWidth < 992) {
                overlay.classList.remove('active');
            }
        }
        
        // Xử lý sự kiện nút toggle
        if (sidebarCollapse) {
            sidebarCollapse.addEventListener('click', function() {
                if (sidebar.style.left === '-220px' || !sidebar.style.left) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }
        
        // Xử lý sự kiện nút đóng
        if (closeSidebarButton) {
            closeSidebarButton.addEventListener('click', function() {
                closeSidebar();
            });
        }
        
        // Đóng sidebar khi click vào overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                closeSidebar();
            });
        }
        
        // Điều chỉnh khi thay đổi kích thước màn hình
        window.addEventListener('resize', function() {
            if (window.innerWidth < 992) {
                // Ẩn sidebar trên màn hình nhỏ nhưng giữ lại nút toggle
                if (sidebar.style.left !== '-220px') {
                    sidebar.style.left = '-220px';
                    overlay.classList.remove('active');
                }
                mainContentWrapper.style.marginLeft = '0';
                mainContentWrapper.style.width = '100%';
                sidebarCollapse.style.display = 'block';
            } else {
                // Kiểm tra nếu sidebar đang bị ẩn, hiển thị nút toggle
                if (sidebar.style.left === '-220px') {
                    sidebarCollapse.style.display = 'block';
                } else {
                    // Khôi phục trạng thái mặc định trên màn hình lớn
                    sidebar.style.left = '0';
                    mainContentWrapper.style.marginLeft = 'var(--sidebar-width)';
                    mainContentWrapper.style.width = 'calc(100% - var(--sidebar-width))';
                    sidebarCollapse.style.display = 'none';
                }
            }
        });
    });
</script> 