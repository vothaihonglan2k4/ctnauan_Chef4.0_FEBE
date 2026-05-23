<?php
require_once __DIR__ . '/../helpers/ApiLoader.php';
require_once __DIR__ . '/../helpers/ApiAuth.php';

$apiResponse = new ApiResponse();
$auth = new ApiAuth();
$categoryModel = new Category();

$method = $_SERVER['REQUEST_METHOD'];

// Lấy path từ global scope
global $pathParts;
if (!isset($pathParts)) {
    $pathParts = $GLOBALS['pathParts'] ?? [];
}

$categoryId = isset($pathParts[1]) ? intval($pathParts[1]) : null;

switch ($method) {
    case 'GET':
        handleGetCategories($categoryModel, $apiResponse, $categoryId);
        break;
        
    case 'POST':
        handleCreateCategory($categoryModel, $apiResponse, $auth);
        break;
        
    case 'PUT':
        handleUpdateCategory($categoryModel, $apiResponse, $auth, $categoryId);
        break;
        
    case 'DELETE':
        handleDeleteCategory($categoryModel, $apiResponse, $auth, $categoryId);
        break;
        
    default:
        $apiResponse->error('Method không được hỗ trợ', 405);
}

// GET /api/categories - Lấy danh sách danh mục
// GET /api/categories/{id} - Lấy danh mục theo ID
function handleGetCategories($categoryModel, $apiResponse, $categoryId) {
    try {
        if ($categoryId) {
            $category = $categoryModel->getCategoryById($categoryId);
            if (!$category) {
                $apiResponse->notFound('Không tìm thấy danh mục');
            }
            $apiResponse->success($category);
        } else {
            $categories = $categoryModel->getCategories();
            $apiResponse->success($categories, 'Lấy danh sách danh mục thành công');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi lấy dữ liệu: ' . $e->getMessage());
    }
}

// POST /api/categories - Tạo danh mục mới (chỉ admin)
function handleCreateCategory($categoryModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền admin hoặc manager
    $user = $auth->getCurrentUser();
    if (!$user || ($user->role !== 'admin' && $user->role !== 'manager')) {
        $apiResponse->forbidden('Chỉ admin hoặc manager mới có thể tạo danh mục');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    if (empty($input['name'])) {
        $errors['name'] = 'Tên danh mục không được để trống';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        $data = [
            'name' => trim($input['name']),
            'description' => trim($input['description']) ?? ''
        ];
        
        $result = $categoryModel->addCategory($data);
        if ($result) {
            $apiResponse->success(null, 'Danh mục đã được tạo', 201);
        } else {
            $apiResponse->error('Không thể tạo danh mục');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi tạo danh mục: ' . $e->getMessage());
    }
}

// PUT /api/categories/{id} - Cập nhật danh mục (chỉ admin)
function handleUpdateCategory($categoryModel, $apiResponse, $auth, $categoryId) {
    if (!$categoryId) {
        $apiResponse->error('ID danh mục là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền admin hoặc manager
    $user = $auth->getCurrentUser();
    if (!$user || ($user->role !== 'admin' && $user->role !== 'manager')) {
        $apiResponse->forbidden('Chỉ admin hoặc manager mới có thể cập nhật danh mục');
    }
    
    // Kiểm tra danh mục có tồn tại
    $category = $categoryModel->getCategoryById($categoryId);
    if (!$category) {
        $apiResponse->notFound('Không tìm thấy danh mục');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    try {
        $data = [
            'id' => $categoryId,
            'name' => trim($input['name']) ?? $category->name,
            'description' => trim($input['description']) ?? $category->description
        ];
        
        $result = $categoryModel->updateCategory($data);
        if ($result) {
            $apiResponse->success(null, 'Danh mục đã được cập nhật');
        } else {
            $apiResponse->error('Không thể cập nhật danh mục');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi cập nhật danh mục: ' . $e->getMessage());
    }
}

// DELETE /api/categories/{id} - Xóa danh mục (chỉ admin)
function handleDeleteCategory($categoryModel, $apiResponse, $auth, $categoryId) {
    if (!$categoryId) {
        $apiResponse->error('ID danh mục là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền admin hoặc manager
    $user = $auth->getCurrentUser();
    if (!$user || ($user->role !== 'admin' && $user->role !== 'manager')) {
        $apiResponse->forbidden('Chỉ admin hoặc manager mới có thể xóa danh mục');
    }
    
    // Kiểm tra danh mục có tồn tại
    $category = $categoryModel->getCategoryById($categoryId);
    if (!$category) {
        $apiResponse->notFound('Không tìm thấy danh mục');
    }
    
    try {
        $result = $categoryModel->deleteCategory($categoryId);
        if ($result) {
            $apiResponse->success(null, 'Danh mục đã được xóa');
        } else {
            $apiResponse->error('Không thể xóa danh mục');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi xóa danh mục: ' . $e->getMessage());
    }
} 