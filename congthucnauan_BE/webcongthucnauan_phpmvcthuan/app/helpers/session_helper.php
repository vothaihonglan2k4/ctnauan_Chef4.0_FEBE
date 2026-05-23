<?php
/**
 * Session helper
 * 
 * Includes session-related helper functions
 */

// Kiểm tra đã đăng nhập chưa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kiểm tra có phải là admin không
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

// Chuyển hướng
function redirect($page) {
    header('Location: ' . URL_ROOT . '/' . $page);
    exit;
}

// Thiết lập session
function setUserSession($user) {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_name'] = $user->name;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_role'] = $user->role;
}

// Xóa session
function clearUserSession() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_role']);
    session_destroy();
}

// Kiểm tra và lấy ID người dùng hiện tại
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Kiểm tra xem người dùng có quyền truy cập vào tài nguyên không
function checkUserAccess($resource_user_id) {
    // Admin có quyền truy cập tất cả
    if (isAdmin()) {
        return true;
    }
    
    // Người dùng bình thường chỉ có quyền truy cập tài nguyên của mình
    return isLoggedIn() && $_SESSION['user_id'] == $resource_user_id;
}

// Hiển thị thông báo thành công hoặc lỗi từ session
function displayMessages() {
    if(isset($_SESSION['success_msg'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['success_msg']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['success_msg']); // Xóa thông báo sau khi hiển thị
    }

    if(isset($_SESSION['error_msg'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['error_msg']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['error_msg']); // Xóa thông báo sau khi hiển thị
    }
} 