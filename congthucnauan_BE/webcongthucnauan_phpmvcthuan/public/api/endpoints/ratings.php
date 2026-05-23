<?php
require_once __DIR__ . '/../helpers/ApiLoader.php';
require_once __DIR__ . '/../helpers/ApiAuth.php';

$apiResponse = new ApiResponse();
$auth = new ApiAuth();
$ratingModel = new Rating();

$method = $_SERVER['REQUEST_METHOD'];

// Lấy path từ global scope
global $pathParts;
if (!isset($pathParts)) {
    $pathParts = $GLOBALS['pathParts'] ?? [];
}

$ratingId = isset($pathParts[1]) ? intval($pathParts[1]) : null;

switch ($method) {
    case 'GET':
        handleGetRatings($ratingModel, $apiResponse, $ratingId);
        break;
        
    case 'POST':
        handleCreateRating($ratingModel, $apiResponse, $auth);
        break;
        
    case 'PUT':
        handleUpdateRating($ratingModel, $apiResponse, $auth, $ratingId);
        break;
        
    case 'DELETE':
        handleDeleteRating($ratingModel, $apiResponse, $auth, $ratingId);
        break;
        
    default:
        $apiResponse->error('Method không được hỗ trợ', 405);
}

// GET /api/ratings - Lấy danh sách đánh giá
// GET /api/ratings/{id} - Lấy đánh giá theo ID
// GET /api/ratings?recipe_id=X - Lấy đánh giá theo recipe
function handleGetRatings($ratingModel, $apiResponse, $ratingId) {
    try {
        if ($ratingId) {
            $rating = $ratingModel->getRatingById($ratingId);
            if (!$rating) {
                $apiResponse->notFound('Không tìm thấy đánh giá');
            }
            $apiResponse->success($rating);
        } else {
            $recipeId = $_GET['recipe_id'] ?? null;
            
            if ($recipeId) {
                $ratings = $ratingModel->getRatingsByRecipe($recipeId);
                $apiResponse->success($ratings, 'Lấy danh sách đánh giá thành công');
            } else {
                $ratings = $ratingModel->getAllRatings();
                $apiResponse->success($ratings, 'Lấy danh sách đánh giá thành công');
            }
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi lấy dữ liệu: ' . $e->getMessage());
    }
}

// POST /api/ratings - Tạo đánh giá mới
function handleCreateRating($ratingModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập để đánh giá');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    
    if (empty($input['recipe_id'])) {
        $errors['recipe_id'] = 'ID công thức không được để trống';
    }
    
    if (empty($input['rating'])) {
        $errors['rating'] = 'Điểm đánh giá không được để trống';
    } elseif ($input['rating'] < 1 || $input['rating'] > 5) {
        $errors['rating'] = 'Điểm đánh giá phải từ 1 đến 5';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        // Kiểm tra user đã đánh giá recipe này chưa
        $existingRating = $ratingModel->getUserRatingForRecipe($userId, $input['recipe_id']);
        if ($existingRating) {
            $apiResponse->error('Bạn đã đánh giá công thức này rồi');
        }
        
        $data = [
            'recipe_id' => intval($input['recipe_id']),
            'user_id' => $userId,
            'rating' => intval($input['rating']),
            'comment' => trim($input['comment']) ?? ''
        ];
        
        $result = $ratingModel->addRating($data);
        if ($result) {
            $apiResponse->success(null, 'Đánh giá đã được thêm', 201);
        } else {
            $apiResponse->error('Không thể thêm đánh giá');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi thêm đánh giá: ' . $e->getMessage());
    }
}

// PUT /api/ratings/{id} - Cập nhật đánh giá
function handleUpdateRating($ratingModel, $apiResponse, $auth, $ratingId) {
    if (!$ratingId) {
        $apiResponse->error('ID đánh giá là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền sở hữu
    $rating = $ratingModel->getRatingById($ratingId);
    if (!$rating) {
        $apiResponse->notFound('Không tìm thấy đánh giá');
    }
    
    if ($rating->user_id != $userId) {
        $apiResponse->forbidden('Bạn không có quyền chỉnh sửa đánh giá này');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    try {
        $data = [
            'id' => $ratingId,
            'rating' => intval($input['rating']) ?? $rating->rating,
            'comment' => trim($input['comment']) ?? $rating->comment
        ];
        
        // Validation rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            $apiResponse->error('Điểm đánh giá phải từ 1 đến 5');
        }
        
        $result = $ratingModel->updateRating($data);
        if ($result) {
            $apiResponse->success(null, 'Đánh giá đã được cập nhật');
        } else {
            $apiResponse->error('Không thể cập nhật đánh giá');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi cập nhật đánh giá: ' . $e->getMessage());
    }
}

// DELETE /api/ratings/{id} - Xóa đánh giá
function handleDeleteRating($ratingModel, $apiResponse, $auth, $ratingId) {
    if (!$ratingId) {
        $apiResponse->error('ID đánh giá là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền sở hữu
    $rating = $ratingModel->getRatingById($ratingId);
    if (!$rating) {
        $apiResponse->notFound('Không tìm thấy đánh giá');
    }
    
    if ($rating->user_id != $userId) {
        $apiResponse->forbidden('Bạn không có quyền xóa đánh giá này');
    }
    
    try {
        $result = $ratingModel->deleteRating($ratingId);
        if ($result) {
            $apiResponse->success(null, 'Đánh giá đã được xóa');
        } else {
            $apiResponse->error('Không thể xóa đánh giá');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi xóa đánh giá: ' . $e->getMessage());
    }
} 