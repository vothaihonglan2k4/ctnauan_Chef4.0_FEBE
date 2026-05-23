<?php
require_once __DIR__ . '/../helpers/ApiLoader.php';
require_once __DIR__ . '/../helpers/ApiAuth.php';

$apiResponse = new ApiResponse();
$auth = new ApiAuth();
$recipeModel = new Recipe();

$method = $_SERVER['REQUEST_METHOD'];

// Lấy path từ global scope của index.php
global $pathParts;
if (!isset($pathParts)) {
    // Fallback parsing nếu không có global
    $path = $_SERVER['REQUEST_URI'];
    $path = parse_url($path, PHP_URL_PATH);
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = dirname($scriptName);
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
    }
    $path = trim($path, '/');
    $pathParts = explode('/', $path);
}

$recipeId = isset($pathParts[1]) ? intval($pathParts[1]) : null;

switch ($method) {
    case 'GET':
        handleGetRecipes($recipeModel, $apiResponse, $recipeId);
        break;
        
    case 'POST':
        handleCreateRecipe($recipeModel, $apiResponse, $auth);
        break;
        
    case 'PUT':
        handleUpdateRecipe($recipeModel, $apiResponse, $auth, $recipeId);
        break;
        
    case 'DELETE':
        handleDeleteRecipe($recipeModel, $apiResponse, $auth, $recipeId);
        break;
        
    default:
        $apiResponse->error('Method không được hỗ trợ', 405);
}

// GET /api/recipes - Lấy danh sách công thức
// GET /api/recipes/{id} - Lấy công thức theo ID
// GET /api/recipes?search=keyword - Tìm kiếm
// GET /api/recipes?category=id - Lọc theo danh mục
function handleGetRecipes($recipeModel, $apiResponse, $recipeId) {
    try {
        if ($recipeId) {
            // Lấy công thức theo ID
            $recipe = $recipeModel->getApprovedRecipeById($recipeId);
            if (!$recipe) {
                $apiResponse->notFound('Không tìm thấy công thức');
            }
            $apiResponse->success($recipe);
        } else {
            // Lấy danh sách công thức với filters
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            $ingredients = $_GET['ingredients'] ?? '';
            
            if ($search || $category || $ingredients) {
                // Tìm kiếm nâng cao
                $searchData = [
                    'term' => $search,
                    'category' => $category,
                    'ingredients' => $ingredients
                ];
                $recipes = $recipeModel->searchRecipesAdvanced($searchData);
            } else {
                // Lấy tất cả công thức đã được duyệt
                $recipes = $recipeModel->getApprovedRecipes();
            }
            
            $apiResponse->success($recipes, 'Lấy danh sách công thức thành công');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi lấy dữ liệu: ' . $e->getMessage());
    }
}

// POST /api/recipes - Tạo công thức mới
function handleCreateRecipe($recipeModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập để thêm công thức');
    }
    
    // Lấy dữ liệu từ request body
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    if (empty($input['title'])) {
        $errors['title'] = 'Tiêu đề không được để trống';
    }
    if (empty($input['description'])) {
        $errors['description'] = 'Mô tả không được để trống';
    }
    if (empty($input['ingredients'])) {
        $errors['ingredients'] = 'Nguyên liệu không được để trống';
    }
    if (empty($input['instructions'])) {
        $errors['instructions'] = 'Hướng dẫn không được để trống';
    }
    if (empty($input['category_id'])) {
        $errors['category_id'] = 'Danh mục không được để trống';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        $data = [
            'title' => trim($input['title']),
            'description' => trim($input['description']),
            'ingredients' => trim($input['ingredients']),
            'instructions' => trim($input['instructions']),
            'image' => $input['image'] ?? 'no-image.jpg',
            'category_id' => intval($input['category_id']),
            'user_id' => $userId
        ];
        
        $result = $recipeModel->addRecipe($data);
        if ($result) {
            $apiResponse->success(null, 'Công thức đã được thêm và đang chờ duyệt', 201);
        } else {
            $apiResponse->error('Không thể thêm công thức');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi thêm công thức: ' . $e->getMessage());
    }
}

// PUT /api/recipes/{id} - Cập nhật công thức
function handleUpdateRecipe($recipeModel, $apiResponse, $auth, $recipeId) {
    if (!$recipeId) {
        $apiResponse->error('ID công thức là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập để cập nhật công thức');
    }
    
    // Kiểm tra quyền sở hữu
    $recipe = $recipeModel->getRecipeByIdForAdmin($recipeId);
    if (!$recipe) {
        $apiResponse->notFound('Không tìm thấy công thức');
    }
    
    if ($recipe->user_id != $userId) {
        $apiResponse->forbidden('Bạn không có quyền chỉnh sửa công thức này');
    }
    
    // Lấy dữ liệu từ request body
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    try {
        $data = [
            'id' => $recipeId,
            'title' => trim($input['title']) ?? $recipe->title,
            'description' => trim($input['description']) ?? $recipe->description,
            'ingredients' => trim($input['ingredients']) ?? $recipe->ingredients,
            'instructions' => trim($input['instructions']) ?? $recipe->instructions,
            'image' => $input['image'] ?? $recipe->image,
            'category_id' => intval($input['category_id']) ?? $recipe->category_id
        ];
        
        $result = $recipeModel->updateRecipe($data);
        if ($result) {
            $apiResponse->success(null, 'Công thức đã được cập nhật');
        } else {
            $apiResponse->error('Không thể cập nhật công thức');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi cập nhật công thức: ' . $e->getMessage());
    }
}

// DELETE /api/recipes/{id} - Xóa công thức
function handleDeleteRecipe($recipeModel, $apiResponse, $auth, $recipeId) {
    if (!$recipeId) {
        $apiResponse->error('ID công thức là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập để xóa công thức');
    }
    
    // Kiểm tra quyền sở hữu
    $recipe = $recipeModel->getRecipeByIdForAdmin($recipeId);
    if (!$recipe) {
        $apiResponse->notFound('Không tìm thấy công thức');
    }
    
    if ($recipe->user_id != $userId) {
        $apiResponse->forbidden('Bạn không có quyền xóa công thức này');
    }
    
    try {
        $result = $recipeModel->deleteRecipe($recipeId);
        if ($result) {
            $apiResponse->success(null, 'Công thức đã được xóa');
        } else {
            $apiResponse->error('Không thể xóa công thức');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi xóa công thức: ' . $e->getMessage());
    }
} 