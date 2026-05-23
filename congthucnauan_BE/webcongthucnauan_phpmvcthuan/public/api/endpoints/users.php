<?php
require_once __DIR__ . '/../helpers/ApiLoader.php';
require_once __DIR__ . '/../helpers/ApiAuth.php';

$apiResponse = new ApiResponse();
$auth = new ApiAuth();
$userModel = new User();

$method = $_SERVER['REQUEST_METHOD'];

// Lấy path từ global scope
global $pathParts;
if (!isset($pathParts)) {
    $pathParts = $GLOBALS['pathParts'] ?? [];
}

$action = isset($pathParts[1]) ? $pathParts[1] : '';

switch ($method) {
    case 'GET':
        if ($action === 'profile') {
            handleGetProfile($userModel, $apiResponse, $auth);
        } else {
            $apiResponse->error('Endpoint không hợp lệ', 404);
        }
        break;
        
    case 'POST':
        if ($action === 'register') {
            handleRegister($userModel, $apiResponse);
        } elseif ($action === 'login') {
            handleLogin($userModel, $apiResponse, $auth);
        } elseif ($action === 'logout') {
            handleLogout($apiResponse, $auth);
        } else {
            $apiResponse->error('Endpoint không hợp lệ', 404);
        }
        break;
        
    case 'PUT':
        if ($action === 'profile') {
            handleUpdateProfile($userModel, $apiResponse, $auth);
        } elseif ($action === 'password') {
            handleChangePassword($userModel, $apiResponse, $auth);
        } else {
            $apiResponse->error('Endpoint không hợp lệ', 404);
        }
        break;
        
    default:
        $apiResponse->error('Method không được hỗ trợ', 405);
}

// POST /api/users/register - Đăng ký tài khoản
function handleRegister($userModel, $apiResponse) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    
    if (empty($input['name'])) {
        $errors['name'] = 'Tên không được để trống';
    }
    
    if (empty($input['email'])) {
        $errors['email'] = 'Email không được để trống';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    } elseif ($userModel->findUserByEmail($input['email'])) {
        $errors['email'] = 'Email đã được sử dụng';
    }
    
    if (empty($input['password'])) {
        $errors['password'] = 'Mật khẩu không được để trống';
    } elseif (strlen($input['password']) < 6) {
        $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }
    
    if (empty($input['confirm_password'])) {
        $errors['confirm_password'] = 'Xác nhận mật khẩu không được để trống';
    } elseif ($input['password'] !== $input['confirm_password']) {
        $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        $data = [
            'name' => trim($input['name']),
            'email' => trim($input['email']),
            'password' => password_hash($input['password'], PASSWORD_DEFAULT),
            'role' => 'user' // Default role
        ];
        
        $result = $userModel->register($data);
        if ($result) {
            $apiResponse->success([
                'message' => 'Đăng ký thành công'
            ], 'Tài khoản đã được tạo thành công', 201);
        } else {
            $apiResponse->error('Không thể tạo tài khoản');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi tạo tài khoản: ' . $e->getMessage());
    }
}

// POST /api/users/login - Đăng nhập
function handleLogin($userModel, $apiResponse, $auth) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    
    if (empty($input['email'])) {
        $errors['email'] = 'Email không được để trống';
    }
    
    if (empty($input['password'])) {
        $errors['password'] = 'Mật khẩu không được để trống';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        $user = $userModel->findUserByEmail(trim($input['email']));
        
        if ($user && password_verify($input['password'], $user->password)) {
            $token = $auth->generateToken($user->id);
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at
            ];
            
            $apiResponse->success([
                'user' => $userData,
                'token' => $token
            ], 'Đăng nhập thành công');
        } else {
            $apiResponse->error('Email hoặc mật khẩu không chính xác', 401);
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi đăng nhập: ' . $e->getMessage());
    }
}

// POST /api/users/logout - Đăng xuất
function handleLogout($apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Bạn chưa đăng nhập');
    }
    
    $auth->revokeToken();
    $apiResponse->success(null, 'Đăng xuất thành công');
}

// GET /api/users/profile - Lấy thông tin profile
function handleGetProfile($userModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    try {
        $user = $userModel->getUserById($userId);
        if ($user) {
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at
            ];
            
            $apiResponse->success($userData, 'Lấy thông tin profile thành công');
        } else {
            $apiResponse->notFound('Không tìm thấy thông tin người dùng');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi lấy thông tin profile: ' . $e->getMessage());
    }
}

// PUT /api/users/profile - Cập nhật profile
function handleUpdateProfile($userModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    
    if (empty($input['name'])) {
        $errors['name'] = 'Tên không được để trống';
    }
    
    if (empty($input['email'])) {
        $errors['email'] = 'Email không được để trống';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    } else {
        // Kiểm tra email đã được sử dụng bởi user khác chưa
        $existingUser = $userModel->findUserByEmail($input['email']);
        if ($existingUser && $existingUser->id != $userId) {
            $errors['email'] = 'Email đã được sử dụng';
        }
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        $data = [
            'id' => $userId,
            'name' => trim($input['name']),
            'email' => trim($input['email'])
        ];
        
        $result = $userModel->updateUser($data);
        if ($result) {
            $apiResponse->success(null, 'Cập nhật profile thành công');
        } else {
            $apiResponse->error('Không thể cập nhật profile');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi cập nhật profile: ' . $e->getMessage());
    }
}

// PUT /api/users/password - Đổi mật khẩu
function handleChangePassword($userModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    
    if (empty($input['current_password'])) {
        $errors['current_password'] = 'Mật khẩu hiện tại không được để trống';
    }
    
    if (empty($input['new_password'])) {
        $errors['new_password'] = 'Mật khẩu mới không được để trống';
    } elseif (strlen($input['new_password']) < 6) {
        $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
    }
    
    if (empty($input['confirm_password'])) {
        $errors['confirm_password'] = 'Xác nhận mật khẩu không được để trống';
    } elseif ($input['new_password'] !== $input['confirm_password']) {
        $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        // Kiểm tra mật khẩu hiện tại
        $user = $userModel->getUserById($userId);
        if (!password_verify($input['current_password'], $user->password)) {
            $apiResponse->error('Mật khẩu hiện tại không chính xác', 400);
        }
        
        $data = [
            'id' => $userId,
            'password' => password_hash($input['new_password'], PASSWORD_DEFAULT)
        ];
        
        $result = $userModel->updatePassword($data);
        if ($result) {
            $apiResponse->success(null, 'Đổi mật khẩu thành công');
        } else {
            $apiResponse->error('Không thể đổi mật khẩu');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi đổi mật khẩu: ' . $e->getMessage());
    }
} 