<?php

class ApiAuth {
    private $userModel = null;
    
    public function __construct() {
        // User model sẽ được khởi tạo khi cần thiết
    }
    
    private function getUserModel() {
        if ($this->userModel === null) {
            $this->userModel = new User();
        }
        return $this->userModel;
    }
    
    // Kiểm tra token từ header Authorization
    public function checkToken() {
        $headers = getallheaders();
        $token = null;
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }
        
        if (!$token) {
            return false;
        }
        
        // Kiểm tra token trong session hoặc database
        session_start();
        if (isset($_SESSION['api_token']) && $_SESSION['api_token'] === $token && isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        
        return false;
    }
    
    // Tạo token đơn giản (trong thực tế nên dùng JWT)
    public function generateToken($userId) {
        $token = bin2hex(random_bytes(32));
        session_start();
        $_SESSION['api_token'] = $token;
        $_SESSION['user_id'] = $userId;
        return $token;
    }
    
    // Xóa token
    public function revokeToken() {
        session_start();
        unset($_SESSION['api_token']);
        unset($_SESSION['user_id']);
        session_destroy();
    }
    
    // Lấy thông tin user hiện tại
    public function getCurrentUser() {
        $userId = $this->checkToken();
        if ($userId) {
            return $this->getUserModel()->getUserById($userId);
        }
        return false;
    }
} 