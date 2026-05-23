<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - Công thức nấu ăn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/style.css">
    <!-- End of CSS links -->
    <style>
        :root {
            --sidebar-width: 240px;
            --primary-color: #0d6efd;
            --manager-color: #198754; /* Green for manager theme */
            --sidebar-bg: linear-gradient(135deg, #155724, #28a745);
            --border-radius: 8px;
        }
        
        html, body {
            height: 100%;
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
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
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .sidebar-header .role-badge {
            background: var(--manager-color);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-top: 5px;
            display: inline-block;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 15px 0 0 0;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 12px 20px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
            border-left-color: #fff;
            transform: translateX(3px);
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid var(--manager-color);
        }
        
        .page-header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .page-description {
            margin: 5px 0 0 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .border-left-primary {
            border-left: 4px solid #0d6efd !important;
        }
        
        .border-left-success {
            border-left: 4px solid #198754 !important;
        }
        
        .border-left-info {
            border-left: 4px solid #0dcaf0 !important;
        }
        
        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }
        
        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-footer {
            background: rgba(0,0,0,0.02);
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
        }
        
        /* Quick actions */
        .quick-action {
            transition: all 0.3s ease;
        }
        
        .quick-action:hover {
            transform: translateY(-1px);
        }
        
        /* Stats cards animation */
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }
        
        .stat-icon {
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-user-tie fa-2x mb-2"></i>
                <h4>Manager Panel</h4>
                <span class="role-badge">Quản Lý</span>
                <div class="mt-2 small">
                    <i class="fas fa-user me-1"></i>
                    <?php echo $_SESSION['user_name'] ?? 'Manager'; ?>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo URLROOT; ?>/manager" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'manager' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/manager/recipes">
                        <i class="fas fa-utensils"></i>
                        Quản lý Công thức
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/manager/courses">
                        <i class="fas fa-graduation-cap"></i>
                        Quản lý Khóa học
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/manager/contacts">
                        <i class="fas fa-envelope"></i>
                        Liên hệ
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/manager/reports">
                        <i class="fas fa-chart-bar"></i>
                        Báo cáo
                    </a>
                </li>
                
                <hr style="border-color: rgba(255,255,255,0.2); margin: 15px 0;">
                
                <li>
                    <a href="<?php echo URLROOT; ?>">
                        <i class="fas fa-home"></i>
                        Về trang chủ
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/users/logout" class="text-warning">
                        <i class="fas fa-sign-out-alt"></i>
                        Đăng xuất
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content"> 