<?php
class ManagerController extends Controller {
    private $userModel;
    private $recipeModel;
    private $categoryModel;
    private $paymentModel;
    private $contactModel;
    private $courseModel;
    private $lessonModel;
    private $classroomModel;
    private $enrollmentModel;
    private $ratingModel;
    
    public function __construct() {
        // Manager hoặc Admin mới có thể truy cập
        if(!isset($_SESSION['user_id']) || 
           ($_SESSION['user_role'] !== 'manager' && $_SESSION['user_role'] !== 'admin')) {
            $_SESSION['error_msg'] = 'Bạn không có quyền truy cập trang này';
            $this->redirect('users/login');
        }
        
        $this->userModel = $this->model('User');
        $this->recipeModel = $this->model('Recipe');
        $this->categoryModel = $this->model('Category');
        $this->paymentModel = $this->model('Payment');
        $this->contactModel = $this->model('Contact');
        $this->courseModel = $this->model('Course');
        $this->lessonModel = $this->model('CourseLesson');
        $this->classroomModel = $this->model('Classroom');
        $this->enrollmentModel = $this->model('CourseEnrollment');
        $this->ratingModel = $this->model('Rating');
    }

    public function index() {
        // Dashboard cho Manager
        $data = [
            'title' => 'Manager Dashboard',
            'total_recipes' => $this->recipeModel->getRecipesCount(),
            'pending_recipes' => $this->recipeModel->getRecipesCount('pending'),
            'total_courses' => $this->courseModel->getCoursesCount(),
            'total_students' => $this->enrollmentModel->getTotalStudents(),
            'recent_contacts' => $this->contactModel->getRecentContacts(5),
            'role' => $_SESSION['user_role']
        ];
        
        $this->view('manager/index', $data);
    }
    
    // ============ QUẢN LÝ NỘI DUNG (RECIPES) ============
    public function recipes() {
        $recipes = $this->recipeModel->getAllRecipes();
        
        $data = [
            'title' => 'Quản lý Công thức',
            'recipes' => $recipes
        ];
        
        $this->view('manager/recipes', $data);
    }
    
    // Duyệt/Từ chối công thức
    public function approveRecipe($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['action'] ?? '';
            
            if($action == 'approve') {
                if($this->recipeModel->updateRecipeStatus($id, 'approved')) {
                    $_SESSION['success_msg'] = 'Đã duyệt công thức thành công';
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra khi duyệt công thức';
                }
            } elseif($action == 'reject') {
                if($this->recipeModel->updateRecipeStatus($id, 'rejected')) {
                    $_SESSION['success_msg'] = 'Đã từ chối công thức';
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra khi từ chối công thức';
                }
            }
        }
        
        $this->redirect('manager/recipes');
    }
    
    // ============ QUẢN LÝ KHÓA HỌC ============
    public function courses() {
        $courses = $this->courseModel->getAllCourses();
        $classrooms = $this->classroomModel->getAllClassrooms();
        
        // Thêm thông tin chi tiết cho từng khóa học
        foreach($courses as $course) {
            $course->student_count = $this->enrollmentModel->getEnrollmentCountByCourse($course->id);
            $course->lesson_count = $this->lessonModel->getLessonCountByCourse($course->id);
        }
        
        $data = [
            'title' => 'Quản lý Khóa học',
            'courses' => $courses,
            'classrooms' => $classrooms
        ];
        
        $this->view('manager/courses', $data);
    }
    
    // Thêm khóa học mới  
    public function addCourse() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'price' => floatval($_POST['price']),
                'duration' => intval($_POST['duration']),
                'level' => $_POST['level'],
                'classroom_id' => intval($_POST['classroom_id']),
                'user_id' => $_SESSION['user_id'], // Manager tự tạo
                'requirements' => trim($_POST['requirements'] ?? ''),
                'what_will_learn' => trim($_POST['what_will_learn'] ?? ''),
                'status' => 'published', // Manager có thể publish luôn
                'image' => 'no-image.jpg'
            ];
            
            // Validation
            $errors = [];
            if(empty($data['title'])) $errors['title_err'] = 'Vui lòng nhập tiêu đề';
            if(empty($data['description'])) $errors['description_err'] = 'Vui lòng nhập mô tả';
            if($data['price'] < 0) $errors['price_err'] = 'Giá không hợp lệ';
            if($data['duration'] <= 0) $errors['duration_err'] = 'Thời lượng không hợp lệ';
            
            // Handle image upload
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = 'public/img/courses/';
                if(!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if(in_array($file_extension, $allowed_types)) {
                    $file_name = time() . '_' . $_FILES['image']['name'];
                    $upload_file = $upload_dir . $file_name;
                    
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                        $data['image'] = $file_name;
                    }
                }
            }
            
            if(empty($errors)) {
                if($this->courseModel->addCourse($data)) {
                    $_SESSION['success_msg'] = 'Thêm khóa học thành công';
                    $this->redirect('manager/courses');
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra khi thêm khóa học';
                }
            } else {
                $_SESSION['error_msg'] = implode(', ', $errors);
            }
        }
        
        $classrooms = $this->classroomModel->getAllClassrooms();
        $data = [
            'title' => 'Thêm Khóa học',
            'classrooms' => $classrooms,
            'title' => '',
            'description' => '',
            'price' => '',
            'duration' => '',
            'level' => 'beginner',
            'classroom_id' => '',
            'requirements' => '',
            'what_will_learn' => ''
        ];
        
        $this->view('manager/courses/add', $data);
    }
    
    // Cập nhật trạng thái khóa học
    public function updateCourseStatus($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = $_POST['status'] ?? '';
            
            if(in_array($status, ['published', 'draft', 'archived'])) {
                if($this->courseModel->updateCourseStatus($id, $status)) {
                    $_SESSION['success_msg'] = 'Cập nhật trạng thái thành công';
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra';
                }
            }
        }
        
        $this->redirect('manager/courses');
    }
    
    // ============ QUẢN LÝ THANH TOÁN ============
    public function payments() {
        // Chỉ admin hoặc manager payment mới xem được
        $payments = $this->paymentModel->getAllPayments();
        
        $data = [
            'title' => 'Quản lý Thanh toán',
            'payments' => $payments
        ];
        
        $this->view('manager/payments', $data);
    }
    
    // Xem hóa đơn để in
    public function invoice($payment_id) {
        $payment = $this->paymentModel->getPaymentForInvoice($payment_id);
        
        if(!$payment) {
            $_SESSION['error_msg'] = 'Không tìm thấy thanh toán';
            $this->redirect('manager/payments');
        }
        
        $data = [
            'payment' => $payment
        ];
        
        $this->view('admin/invoice', $data);
    }
    
    // ============ QUẢN LÝ LIÊN HỆ ============ 
    public function contacts() {
        $contacts = $this->contactModel->getAllContacts();
        
        $data = [
            'title' => 'Quản lý Liên hệ',
            'contacts' => $contacts
        ];
        
        $this->view('manager/contacts', $data);
    }
    
    // Đánh dấu đã đọc liên hệ
    public function markContactRead($id) {
        if($this->contactModel->updateContactStatus($id, 'read')) {
            $_SESSION['success_msg'] = 'Đã đánh dấu đã đọc';
        } else {
            $_SESSION['error_msg'] = 'Có lỗi xảy ra';
        }
        
        $this->redirect('manager/contacts');
    }
    
    // ============ BÁO CÁO & THỐNG KÊ ============
    public function reports() {
        $stats = [
            'recipes' => [
                'total' => $this->recipeModel->getRecipesCount(),
                'approved' => $this->recipeModel->getRecipesCount('approved'),
                'pending' => $this->recipeModel->getRecipesCount('pending'),
                'rejected' => $this->recipeModel->getRecipesCount('rejected')
            ],
            'courses' => [
                'total' => $this->courseModel->getCoursesCount(),
                'published' => $this->courseModel->getCoursesCount('published'),
                'draft' => $this->courseModel->getCoursesCount('draft')
            ],
            'users' => [
                'total' => $this->userModel->getUsersCount(),
                'this_month' => $this->userModel->getUsersCountByMonth(date('Y-m'))
            ],
            'revenue' => [
                'total' => $this->paymentModel->getTotalRevenue(),
                'this_month' => $this->paymentModel->getRevenueByMonth(date('Y-m'))
            ]
        ];
        
        $data = [
            'title' => 'Báo cáo & Thống kê',
            'stats' => $stats
        ];
        
        $this->view('manager/reports', $data);
    }
    
    // Helper method: Kiểm tra quyền theo chức năng
    private function hasPermission($function) {
        $role = $_SESSION['user_role'];
        
        // Admin có full quyền
        if($role === 'admin') return true;
        
        // Manager permissions based on function
        $permissions = [
            'recipes' => true,      // Quản lý nội dung
            'courses' => true,      // Quản lý khóa học  
            'contacts' => true,     // Quản lý liên hệ
            'payments' => false,    // Chỉ admin xem được payment
            'users' => false,       // Chỉ admin quản lý users
            'settings' => false     // Chỉ admin quản lý settings
        ];
        
        return $permissions[$function] ?? false;
    }
} 