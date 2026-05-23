<?php
require_once __DIR__ . '/../helpers/ApiLoader.php';
require_once __DIR__ . '/../helpers/ApiAuth.php';

$apiResponse = new ApiResponse();
$auth = new ApiAuth();
$courseModel = new Course();
$lessonModel = new CourseLesson();

$method = $_SERVER['REQUEST_METHOD'];

// Lấy path từ global scope
global $pathParts;
if (!isset($pathParts)) {
    $pathParts = $GLOBALS['pathParts'] ?? [];
}

$courseId = isset($pathParts[1]) ? intval($pathParts[1]) : null;
$action = isset($pathParts[2]) ? $pathParts[2] : null;

switch ($method) {
    case 'GET':
        if ($action === 'lessons') {
            handleGetCourseLessons($lessonModel, $apiResponse, $courseId);
        } else {
            handleGetCourses($courseModel, $apiResponse, $courseId);
        }
        break;
        
    case 'POST':
        if ($action === 'enroll') {
            handleEnrollCourse($courseModel, $apiResponse, $auth, $courseId);
        } else {
            handleCreateCourse($courseModel, $apiResponse, $auth);
        }
        break;
        
    case 'PUT':
        handleUpdateCourse($courseModel, $apiResponse, $auth, $courseId);
        break;
        
    case 'DELETE':
        handleDeleteCourse($courseModel, $apiResponse, $auth, $courseId);
        break;
        
    default:
        $apiResponse->error('Method không được hỗ trợ', 405);
}

// GET /api/courses - Lấy danh sách khóa học
// GET /api/courses/{id} - Lấy khóa học theo ID
function handleGetCourses($courseModel, $apiResponse, $courseId) {
    try {
        if ($courseId) {
            $course = $courseModel->getCourseById($courseId);
            if (!$course) {
                $apiResponse->notFound('Không tìm thấy khóa học');
            }
            $apiResponse->success($course);
        } else {
            $courses = $courseModel->getAllCourses();
            $apiResponse->success($courses, 'Lấy danh sách khóa học thành công');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi lấy dữ liệu: ' . $e->getMessage());
    }
}

// GET /api/courses/{id}/lessons - Lấy danh sách bài học của khóa học
function handleGetCourseLessons($lessonModel, $apiResponse, $courseId) {
    if (!$courseId) {
        $apiResponse->error('ID khóa học là bắt buộc');
    }
    
    try {
        $lessons = $lessonModel->getLessonsByCourse($courseId);
        $apiResponse->success($lessons, 'Lấy danh sách bài học thành công');
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi lấy dữ liệu: ' . $e->getMessage());
    }
}

// POST /api/courses - Tạo khóa học mới (chỉ admin/instructor)
function handleCreateCourse($courseModel, $apiResponse, $auth) {
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền
    $user = $auth->getCurrentUser();
    if (!$user || ($user->role !== 'admin' && $user->role !== 'instructor')) {
        $apiResponse->forbidden('Chỉ admin hoặc instructor mới có thể tạo khóa học');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validation
    $errors = [];
    if (empty($input['title'])) {
        $errors['title'] = 'Tên khóa học không được để trống';
    }
    if (empty($input['description'])) {
        $errors['description'] = 'Mô tả không được để trống';
    }
    if (empty($input['price']) || !is_numeric($input['price'])) {
        $errors['price'] = 'Giá khóa học phải là số';
    }
    
    if (!empty($errors)) {
        $apiResponse->validation($errors);
    }
    
    try {
        $data = [
            'title' => trim($input['title']),
            'description' => trim($input['description']),
            'price' => floatval($input['price']),
            'instructor_id' => $userId,
            'duration' => intval($input['duration']) ?? 0,
            'level' => $input['level'] ?? 'beginner',
            'image' => $input['image'] ?? 'no-image.jpg'
        ];
        
        $result = $courseModel->addCourse($data);
        if ($result) {
            $apiResponse->success(null, 'Khóa học đã được tạo', 201);
        } else {
            $apiResponse->error('Không thể tạo khóa học');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi tạo khóa học: ' . $e->getMessage());
    }
}

// POST /api/courses/{id}/enroll - Đăng ký khóa học
function handleEnrollCourse($courseModel, $apiResponse, $auth, $courseId) {
    if (!$courseId) {
        $apiResponse->error('ID khóa học là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập để đăng ký khóa học');
    }
    
    try {
        // Kiểm tra khóa học có tồn tại
        $course = $courseModel->getCourseById($courseId);
        if (!$course) {
            $apiResponse->notFound('Không tìm thấy khóa học');
        }
        
        // Kiểm tra đã đăng ký chưa
        $enrollment = $courseModel->getEnrollment($userId, $courseId);
        if ($enrollment) {
            $apiResponse->error('Bạn đã đăng ký khóa học này rồi');
        }
        
        $result = $courseModel->enrollCourse($userId, $courseId);
        if ($result) {
            $apiResponse->success(null, 'Đăng ký khóa học thành công', 201);
        } else {
            $apiResponse->error('Không thể đăng ký khóa học');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi đăng ký khóa học: ' . $e->getMessage());
    }
}

// PUT /api/courses/{id} - Cập nhật khóa học
function handleUpdateCourse($courseModel, $apiResponse, $auth, $courseId) {
    if (!$courseId) {
        $apiResponse->error('ID khóa học là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền sở hữu
    $course = $courseModel->getCourseById($courseId);
    if (!$course) {
        $apiResponse->notFound('Không tìm thấy khóa học');
    }
    
    $user = $auth->getCurrentUser();
    if ($course->instructor_id != $userId && $user->role !== 'admin') {
        $apiResponse->forbidden('Bạn không có quyền chỉnh sửa khóa học này');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    try {
        $data = [
            'id' => $courseId,
            'title' => trim($input['title']) ?? $course->title,
            'description' => trim($input['description']) ?? $course->description,
            'price' => floatval($input['price']) ?? $course->price,
            'duration' => intval($input['duration']) ?? $course->duration,
            'level' => $input['level'] ?? $course->level,
            'image' => $input['image'] ?? $course->image
        ];
        
        $result = $courseModel->updateCourse($data);
        if ($result) {
            $apiResponse->success(null, 'Khóa học đã được cập nhật');
        } else {
            $apiResponse->error('Không thể cập nhật khóa học');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi cập nhật khóa học: ' . $e->getMessage());
    }
}

// DELETE /api/courses/{id} - Xóa khóa học
function handleDeleteCourse($courseModel, $apiResponse, $auth, $courseId) {
    if (!$courseId) {
        $apiResponse->error('ID khóa học là bắt buộc');
    }
    
    $userId = $auth->checkToken();
    if (!$userId) {
        $apiResponse->unauthorized('Vui lòng đăng nhập');
    }
    
    // Kiểm tra quyền sở hữu
    $course = $courseModel->getCourseById($courseId);
    if (!$course) {
        $apiResponse->notFound('Không tìm thấy khóa học');
    }
    
    $user = $auth->getCurrentUser();
    if ($course->instructor_id != $userId && $user->role !== 'admin') {
        $apiResponse->forbidden('Bạn không có quyền xóa khóa học này');
    }
    
    try {
        $result = $courseModel->deleteCourse($courseId);
        if ($result) {
            $apiResponse->success(null, 'Khóa học đã được xóa');
        } else {
            $apiResponse->error('Không thể xóa khóa học');
        }
    } catch (Exception $e) {
        $apiResponse->error('Lỗi khi xóa khóa học: ' . $e->getMessage());
    }
} 