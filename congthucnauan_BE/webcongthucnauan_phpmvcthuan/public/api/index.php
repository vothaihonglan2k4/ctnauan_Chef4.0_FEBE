<?php
// Thiết lập header cho API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Xử lý preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Khởi tạo ứng dụng
require_once __DIR__ . '/helpers/ApiLoader.php';
require_once __DIR__ . '/helpers/ApiResponse.php';

$apiResponse = new ApiResponse();

try {
    // Lấy method và đường dẫn
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'];
    
    // Loại bỏ query string
    $path = parse_url($path, PHP_URL_PATH);
    
    // Debug path - uncomment for testing
    // echo "Original path: " . $path . "<br>";
    // echo "Script name: " . $scriptName . "<br>";
    // echo "Base path: " . $basePath . "<br>";
    // echo "Final path: " . $path . "<br>";
    // echo "Endpoint: " . $endpoint . "<br>";
    // die();
    
    // Tìm và loại bỏ phần project từ path
    $scriptName = $_SERVER['SCRIPT_NAME']; // /webcongthucnauan/public/api/index.php
    $basePath = dirname($scriptName); // /webcongthucnauan/public/api
    
    // Loại bỏ base path
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
    }
    
    $path = trim($path, '/');
    
    // Chia path thành các phần
    $pathParts = explode('/', $path);
    $endpoint = $pathParts[0] ?? '';
    
    // Tạo global pathParts để endpoints có thể sử dụng
    $GLOBALS['pathParts'] = $pathParts;
    
    // Route tới các endpoint
    switch ($endpoint) {
        case '':
        case 'docs':
            // API Documentation
            require_once __DIR__ . '/docs.php';
            break;
            
        case 'recipes':
            require_once __DIR__ . '/endpoints/recipes.php';
            break;
            
        case 'users':
            require_once __DIR__ . '/endpoints/users.php';
            break;
            
        case 'categories':
            require_once __DIR__ . '/endpoints/categories.php';
            break;
            
        case 'courses':
            require_once __DIR__ . '/endpoints/courses.php';
            break;
            
        case 'ratings':
            require_once __DIR__ . '/endpoints/ratings.php';
            break;
            
        default:
            $apiResponse->error('Endpoint không tồn tại', 404);
    }
    
} catch (Exception $e) {
    $apiResponse->error('Lỗi server: ' . $e->getMessage(), 500);
} 