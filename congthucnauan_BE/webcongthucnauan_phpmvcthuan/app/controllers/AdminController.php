<?php
class AdminController extends Controller {
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
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error_msg'] = 'Bạn không có quyền truy cập trang này';
            $this->redirect('users/login');
        }
        
        $this->userModel = $this->model('User');
        $this->recipeModel = $this->model('Recipe');
        $this->categoryModel = $this->model('Category');
        $this->paymentModel = $this->model('Payment');
        $this->contactModel = $this->model('Contact');
        
        // Thêm model cho quản lý khóa học
        $this->courseModel = $this->model('Course');
        $this->lessonModel = $this->model('CourseLesson');
        $this->classroomModel = $this->model('Classroom');
        $this->enrollmentModel = $this->model('CourseEnrollment');
        $this->ratingModel = $this->model('Rating');
    }

    public function index() {
        $userCount = $this->userModel->getUsersCount();
        $recipeCount = $this->recipeModel->getRecipesCount();
        $categoryCount = $this->categoryModel->getCategoriesCount();
        $totalRevenue = $this->paymentModel->getTotalRevenue();
        
        // Thêm số liệu về khóa học và phòng học
        $courseCount = $this->courseModel->getCoursesCount();
        $classroomCount = $this->classroomModel->getClassroomsCount();
        
        // Lấy danh sách người dùng mới nhất
        $this->userModel->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5');
        $recentUsers = $this->userModel->resultSet();
        
        // Lấy danh sách thanh toán gần đây
        $this->paymentModel->query('SELECT p.*, u.name as user_name, c.title as course_title 
                                  FROM payments p
                                  LEFT JOIN users u ON p.user_id = u.id
                                  LEFT JOIN course_enrollments e ON e.payment_id = p.id
                                  LEFT JOIN courses c ON e.course_id = c.id
                                  ORDER BY p.created_at DESC LIMIT 5');
        $recentPayments = $this->paymentModel->resultSet();
        
        // Lấy danh sách khóa học mới nhất
        $recentCourses = $this->courseModel->getNewestCourses(3);
        
        $data = [
            'userCount' => $userCount,
            'recipe_count' => $recipeCount,
            'categoryCount' => $categoryCount,
            'totalRevenue' => $totalRevenue,
            'courseCount' => $courseCount,
            'classroomCount' => $classroomCount,
            'recent_users' => $recentUsers,
            'recent_payments' => $recentPayments,
            'recent_courses' => $recentCourses
        ];
        
        $this->view('admin/index', $data);
    }

    public function users() {
        $users = $this->userModel->getAllUsers();

        $data = [
            'users' => $users
        ];

        $this->view('admin/users', $data);
    }

    public function add_user() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lọc và xử lý dữ liệu từ form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'role' => trim($_POST['role']),
                'error' => ''
            ];
            
            // Validate dữ liệu
            if(empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $_SESSION['error_msg'] = 'Vui lòng điền đầy đủ thông tin';
                $this->redirect('admin/users');
                return;
            }
            
            // Kiểm tra email đã tồn tại chưa
            if($this->userModel->findUserByEmail($data['email'])) {
                $_SESSION['error_msg'] = 'Email đã được sử dụng';
                $this->redirect('admin/users');
                return;
            }
            
            // Thêm người dùng mới
            if($this->userModel->addUser($data)) {
                $_SESSION['success_msg'] = 'Thêm người dùng thành công';
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra khi thêm người dùng';
            }
        }
        
        $this->redirect('admin/users');
    }
    
    public function edit_user($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lọc và xử lý dữ liệu từ form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'role' => trim($_POST['role']),
                'error' => ''
            ];
            
            // Validate dữ liệu
            if(empty($data['name']) || empty($data['email'])) {
                $_SESSION['error_msg'] = 'Vui lòng điền đầy đủ thông tin';
                $this->redirect('admin/users');
                return;
            }
            
            // Kiểm tra email đã tồn tại chưa (và không phải của chính người dùng này)
            $user = $this->userModel->getUserById($id);
            if($user->email != $data['email'] && $this->userModel->findUserByEmail($data['email'])) {
                $_SESSION['error_msg'] = 'Email đã được sử dụng bởi tài khoản khác';
                $this->redirect('admin/users');
                return;
            }
            
            // Cập nhật thông tin người dùng
            if($this->userModel->updateUser($data)) {
                // Nếu người dùng đang cập nhật thông tin của chính mình, cập nhật session
                if($id == $_SESSION['user_id']) {
                    $_SESSION['user_name'] = $data['name'];
                    $_SESSION['user_email'] = $data['email'];
                    $_SESSION['user_role'] = $data['role'];
                }
                
                $_SESSION['success_msg'] = 'Cập nhật thông tin người dùng thành công';
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra khi cập nhật thông tin';
            }
        }
        
        $this->redirect('admin/users');
    }
    
    public function delete_user($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Không cho phép xóa tài khoản của chính mình
            if($id == $_SESSION['user_id']) {
                $_SESSION['error_msg'] = 'Bạn không thể xóa tài khoản của chính mình';
                $this->redirect('admin/users');
                return;
            }
            
            // Xóa người dùng
            if($this->userModel->deleteUser($id)) {
                $_SESSION['success_msg'] = 'Xóa người dùng thành công';
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra khi xóa người dùng';
            }
        }
        
        $this->redirect('admin/users');
    }

    // Thêm phương thức getUser để trả về thông tin người dùng dưới dạng JSON
    public function getUser($id) {
        // Kiểm tra quyền truy cập
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Không có quyền truy cập']);
            return;
        }

        // Lấy thông tin người dùng
        $user = $this->userModel->getUserById($id);
        
        if($user) {
            // Loại bỏ thông tin nhạy cảm
            unset($user->password);
            
            // Trả về dữ liệu JSON
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy người dùng']);
        }
    }

    public function categories() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Add or update category
            $name = trim($_POST['name']);
            
            if(empty($name)) {
                $_SESSION['error_msg'] = 'Vui lòng nhập tên danh mục';
            } else {
                if(isset($_POST['id'])) {
                    // Update
                    if($this->categoryModel->updateCategory($_POST['id'], $name)) {
                        $_SESSION['success_msg'] = 'Cập nhật danh mục thành công';
                    } else {
                        $_SESSION['error_msg'] = 'Có lỗi xảy ra';
                    }
                } else {
                    // Add
                    if($this->categoryModel->addCategory($name)) {
                        $_SESSION['success_msg'] = 'Thêm danh mục thành công';
                    } else {
                        $_SESSION['error_msg'] = 'Có lỗi xảy ra';
                    }
                }
            }
        }
        
        $categories = $this->categoryModel->getCategories();

        $data = [
            'categories' => $categories
        ];

        $this->view('admin/categories', $data);
    }

    public function deleteCategory($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($this->categoryModel->deleteCategory($id)) {
                $_SESSION['success_msg'] = 'Xóa danh mục thành công';
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra';
            }
        }
        
        $this->redirect('admin/categories');
    }

    public function payments() {
        $payments = $this->paymentModel->getAllPayments();

        $data = [
            'payments' => $payments
        ];

        $this->view('admin/payments', $data);
    }
    
    // Xem hóa đơn để in
    public function invoice($payment_id) {
        $payment = $this->paymentModel->getPaymentForInvoice($payment_id);
        
        if(!$payment) {
            $_SESSION['error_msg'] = 'Không tìm thấy thanh toán';
            $this->redirect('admin/payments');
        }
        
        $data = [
            'payment' => $payment
        ];
        
        $this->view('admin/invoice', $data);
    }

    public function reports() {
        // Get data for reports
        $users = $this->userModel->getAllUsers();
        $recipes = $this->recipeModel->getAllRecipes();
        $categories = $this->categoryModel->getCategories();
        $payments = $this->paymentModel->getAllPayments();
        
        // Process data for charts
        $categoryData = [];
        foreach($categories as $category) {
            $count = 0;
            foreach($recipes as $recipe) {
                if($recipe->category_id == $category->id) {
                    $count++;
                }
            }
            $categoryData[$category->name] = $count;
        }
        
        $paymentsByMonth = [];
        foreach($payments as $payment) {
            $month = date('M Y', strtotime($payment->created_at));
            if(!isset($paymentsByMonth[$month])) {
                $paymentsByMonth[$month] = 0;
            }
            if($payment->status == 'completed') {
                $paymentsByMonth[$month] += $payment->amount;
            }
        }

        $data = [
            'userCount' => count($users),
            'recipeCount' => count($recipes),
            'categoryCount' => count($categories),
            'paymentCount' => count($payments),
            'categoryData' => $categoryData,
            'paymentsByMonth' => $paymentsByMonth
        ];

        $this->view('admin/reports', $data);
    }

    // API để lấy dữ liệu đăng ký người dùng theo thời gian
    public function users_registration_data() {
        // Lấy dữ liệu người dùng đăng ký trong 12 tháng gần nhất
        $this->userModel->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM users
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        
        $registrationData = $this->userModel->resultSet();
        
        $labels = [];
        $data = [];
        
        // Tạo mảng dữ liệu cho 12 tháng gần nhất
        $end_month = date('Y-m');
        $start_month = date('Y-m', strtotime('-11 months'));
        $current = $start_month;
        
        // Khởi tạo mảng với 0 cho tất cả các tháng
        while ($current <= $end_month) {
            $labels[] = date('M Y', strtotime($current . '-01'));
            $data[$current] = 0;
            $current = date('Y-m', strtotime($current . '-01 +1 month'));
        }
        
        // Điền dữ liệu thực tế
        foreach ($registrationData as $record) {
            $data[$record->month] = (int) $record->count;
        }
        
        // Chuyển đổi mảng associative thành mảng indexed
        $dataValues = array_values($data);
        
        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode([
            'labels' => $labels,
            'data' => $dataValues
        ]);
        exit;
    }

    // Quản lý khóa học
    public function courses() {
        $courses = $this->courseModel->getAllCourses();
        
        $data = [
            'courses' => $courses
        ];
        
        $this->view('admin/courses/index', $data);
    }
    
    // Thêm khóa học mới
    public function add_course() {
        $classrooms = $this->classroomModel->getActiveClassrooms();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'duration' => trim($_POST['duration']),
                'level' => trim($_POST['level']),
                'classroom_id' => trim($_POST['classroom_id']),
                'user_id' => $_SESSION['user_id'],
                'status' => trim($_POST['status']),
                'image' => 'no-image.jpg',
                'classrooms' => $classrooms,
                'title_err' => '',
                'description_err' => '',
                'price_err' => '',
                'duration_err' => '',
                'level_err' => '',
                'classroom_id_err' => '',
                'status_err' => ''
            ];
            
            // Validate data
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề khóa học';
            }
            
            if(empty($data['description'])) {
                $data['description_err'] = 'Vui lòng nhập mô tả khóa học';
            }
            
            if(empty($data['price'])) {
                $data['price_err'] = 'Vui lòng nhập giá khóa học';
            } elseif(!is_numeric($data['price']) || $data['price'] < 0) {
                $data['price_err'] = 'Giá khóa học phải là số dương';
            }
            
            if(empty($data['duration'])) {
                $data['duration_err'] = 'Vui lòng nhập thời lượng khóa học';
            }
            
            if(empty($data['level'])) {
                $data['level_err'] = 'Vui lòng chọn trình độ khóa học';
            }
            
            if(empty($data['classroom_id'])) {
                $data['classroom_id_err'] = 'Vui lòng chọn phòng học';
            }
            
            if(empty($data['status'])) {
                $data['status_err'] = 'Vui lòng chọn trạng thái khóa học';
            }
            
            // Check if there are no errors
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['price_err']) && 
               empty($data['duration_err']) && empty($data['level_err']) && empty($data['classroom_id_err']) && 
               empty($data['status_err'])) {
                
                // Upload image if selected
                if($_FILES['image']['name']) {
                    $upload_dir = 'public/uploads/courses/';
                    
                    // Tạo thư mục nếu chưa tồn tại
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_name = time() . '_' . $_FILES['image']['name'];
                    $upload_file = $upload_dir . $file_name;
                    
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                        $data['image'] = $file_name;
                    } else {
                        $_SESSION['error_msg'] = 'Không thể tải ảnh lên. Lỗi: ' . $_FILES['image']['error'];
                    }
                }
                
                // Add course
                $course_id = $this->courseModel->addCourse($data);
                
                if($course_id) {
                    $_SESSION['success_msg'] = 'Thêm khóa học thành công';
                    $this->redirect('admin/courses');
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                    $this->view('admin/courses/add', $data);
                }
            } else {
                // Load view with errors
                $this->view('admin/courses/add', $data);
            }
        } else {
            // Init data
            $data = [
                'title' => '',
                'description' => '',
                'price' => '',
                'duration' => '',
                'level' => 'beginner',
                'classroom_id' => '',
                'user_id' => $_SESSION['user_id'],
                'status' => 'draft',
                'image' => 'no-image.jpg',
                'classrooms' => $classrooms,
                'title_err' => '',
                'description_err' => '',
                'price_err' => '',
                'duration_err' => '',
                'level_err' => '',
                'classroom_id_err' => '',
                'status_err' => ''
            ];
            
            $this->view('admin/courses/add', $data);
        }
    }
    
    // Sửa khóa học
    public function edit_course($id) {
        $course = $this->courseModel->getCourseById($id);
        $classrooms = $this->classroomModel->getActiveClassrooms();
        
        if(!$course) {
            $_SESSION['error_msg'] = 'Không tìm thấy khóa học';
            $this->redirect('admin/courses');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'duration' => trim($_POST['duration']),
                'level' => trim($_POST['level']),
                'classroom_id' => trim($_POST['classroom_id']),
                'status' => trim($_POST['status']),
                'image' => $course->image,
                'classrooms' => $classrooms,
                'instructor_name' => $course->instructor_name ?? 'Admin',
                'student_count' => $this->enrollmentModel->getEnrollmentCountByCourse($id),
                'created_at' => $course->created_at ?? date('Y-m-d H:i:s'),
                'requirements' => trim($_POST['requirements'] ?? ''),
                'what_will_learn' => trim($_POST['what_will_learn'] ?? ''),
                'title_err' => '',
                'description_err' => '',
                'price_err' => '',
                'duration_err' => '',
                'level_err' => '',
                'classroom_id_err' => '',
                'status_err' => ''
            ];
            
            // Validate data
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề khóa học';
            }
            
            if(empty($data['description'])) {
                $data['description_err'] = 'Vui lòng nhập mô tả khóa học';
            }
            
            if(empty($data['price'])) {
                $data['price_err'] = 'Vui lòng nhập giá khóa học';
            } elseif(!is_numeric($data['price']) || $data['price'] < 0) {
                $data['price_err'] = 'Giá khóa học phải là số dương';
            }
            
            if(empty($data['duration'])) {
                $data['duration_err'] = 'Vui lòng nhập thời lượng khóa học';
            }
            
            if(empty($data['level'])) {
                $data['level_err'] = 'Vui lòng chọn trình độ khóa học';
            }
            
            if(empty($data['classroom_id'])) {
                $data['classroom_id_err'] = 'Vui lòng chọn phòng học';
            }
            
            if(empty($data['status'])) {
                $data['status_err'] = 'Vui lòng chọn trạng thái khóa học';
            }
            
            // Check if there are no errors
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['price_err']) && 
               empty($data['duration_err']) && empty($data['level_err']) && empty($data['classroom_id_err']) && 
               empty($data['status_err'])) {
                
                // Upload image if selected
                if($_FILES['image']['name']) {
                    $upload_dir = 'public/uploads/courses/';
                    
                    // Tạo thư mục nếu chưa tồn tại
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_name = time() . '_' . $_FILES['image']['name'];
                    $upload_file = $upload_dir . $file_name;
                    
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                        // Delete old image if it's not the default one
                        if($data['image'] != 'no-image.jpg') {
                            @unlink($upload_dir . $data['image']);
                        }
                        $data['image'] = $file_name;
                    } else {
                        $_SESSION['error_msg'] = 'Không thể tải ảnh lên. Lỗi: ' . $_FILES['image']['error'];
                    }
                }
                
                // Update course
                if($this->courseModel->updateCourse($data)) {
                    $_SESSION['success_msg'] = 'Cập nhật khóa học thành công';
                    $this->redirect('admin/courses');
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                    $this->view('admin/courses/edit', $data);
                }
            } else {
                // Load view with errors
                $this->view('admin/courses/edit', $data);
            }
        } else {
            // Init data
            $data = [
                'id' => $id,
                'title' => $course->title,
                'description' => $course->description,
                'price' => $course->price,
                'duration' => $course->duration,
                'level' => $course->level,
                'classroom_id' => $course->classroom_id,
                'status' => $course->status,
                'image' => $course->image,
                'classrooms' => $classrooms,
                'instructor_name' => $course->instructor_name ?? 'Admin',
                'student_count' => $this->enrollmentModel->getEnrollmentCountByCourse($id),
                'created_at' => $course->created_at ?? date('Y-m-d H:i:s'),
                'requirements' => $course->requirements ?? '',
                'what_will_learn' => $course->what_will_learn ?? '',
                'title_err' => '',
                'description_err' => '',
                'price_err' => '',
                'duration_err' => '',
                'level_err' => '',
                'classroom_id_err' => '',
                'status_err' => ''
            ];
            
            $this->view('admin/courses/edit', $data);
        }
    }
    
    // Xóa khóa học
    public function delete_course($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course = $this->courseModel->getCourseById($id);
            
            if(!$course) {
                $_SESSION['error_msg'] = 'Không tìm thấy khóa học';
                $this->redirect('admin/courses');
            }
            
            // Delete course
            if($this->courseModel->deleteCourse($id)) {
                // Delete course image if it's not the default one
                if($course->image != 'no-image.jpg') {
                    @unlink('public/uploads/courses/' . $course->image);
                }
                
                $_SESSION['success_msg'] = 'Xóa khóa học thành công';
                $this->redirect('admin/courses');
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                $this->redirect('admin/courses');
            }
        } else {
            $this->redirect('admin/courses');
        }
    }
    
    // Quản lý bài học
    public function lessons($course_id) {
        $course = $this->courseModel->getCourseById($course_id);
        
        if(!$course) {
            $_SESSION['error_msg'] = 'Không tìm thấy khóa học';
            $this->redirect('admin/courses');
        }
        
        $lessons = $this->lessonModel->getLessonsByCourseId($course_id);
        
        $data = [
            'course' => $course,
            'lessons' => $lessons
        ];
        
        $this->view('admin/lessons/index', $data);
    }
    
    // Thêm bài học mới
    public function add_lesson($course_id) {
        $course = $this->courseModel->getCourseById($course_id);
        
        if(!$course) {
            $_SESSION['error_msg'] = 'Không tìm thấy khóa học';
            $this->redirect('admin/courses');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'course_id' => $course_id,
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'video_url' => trim($_POST['video_url']),
                'duration_minutes' => trim($_POST['duration_minutes']),
                'sort_order' => trim($_POST['sort_order']),
                'course' => $course,
                'title_err' => '',
                'content_err' => '',
                'video_url_err' => '',
                'duration_minutes_err' => '',
                'sort_order_err' => ''
            ];
            
            // Validate data
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề bài học';
            }
            
            if(empty($data['video_url'])) {
                $data['video_url_err'] = 'Vui lòng nhập URL video';
            } elseif(!filter_var($data['video_url'], FILTER_VALIDATE_URL)) {
                $data['video_url_err'] = 'URL video không hợp lệ';
            }
            
            if(empty($data['duration_minutes'])) {
                $data['duration_minutes_err'] = 'Vui lòng nhập thời lượng bài học';
            } elseif(!is_numeric($data['duration_minutes']) || $data['duration_minutes'] <= 0) {
                $data['duration_minutes_err'] = 'Thời lượng phải là số dương';
            }
            
            if(empty($data['sort_order'])) {
                $data['sort_order_err'] = 'Vui lòng nhập thứ tự bài học';
            } elseif(!is_numeric($data['sort_order']) || $data['sort_order'] < 0) {
                $data['sort_order_err'] = 'Thứ tự phải là số không âm';
            }
            
            // Check if there are no errors
            if(empty($data['title_err']) && empty($data['video_url_err']) && 
               empty($data['duration_minutes_err']) && empty($data['sort_order_err'])) {
                
                // Add lesson
                if($this->lessonModel->addLesson($data)) {
                    $_SESSION['success_msg'] = 'Thêm bài học thành công';
                    $this->redirect('admin/lessons/' . $course_id);
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                    $this->view('admin/lessons/add', $data);
                }
            } else {
                // Load view with errors
                $this->view('admin/lessons/add', $data);
            }
        } else {
            // Get the highest sort order for the course's lessons
            $lessons = $this->lessonModel->getLessonsByCourseId($course_id);
            $highest_order = 0;
            
            foreach($lessons as $lesson) {
                if($lesson->sort_order > $highest_order) {
                    $highest_order = $lesson->sort_order;
                }
            }
            
            // Init data
            $data = [
                'course_id' => $course_id,
                'title' => '',
                'content' => '',
                'video_url' => '',
                'duration_minutes' => '',
                'sort_order' => $highest_order + 1,
                'course' => $course,
                'title_err' => '',
                'content_err' => '',
                'video_url_err' => '',
                'duration_minutes_err' => '',
                'sort_order_err' => ''
            ];
            
            $this->view('admin/lessons/add', $data);
        }
    }
    
    // Sửa bài học
    public function edit_lesson($id) {
        $lesson = $this->lessonModel->getLessonById($id);
        
        if(!$lesson) {
            $_SESSION['error_msg'] = 'Không tìm thấy bài học';
            $this->redirect('admin/courses');
        }
        
        $course = $this->courseModel->getCourseById($lesson->course_id);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'id' => $id,
                'course_id' => $lesson->course_id,
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'video_url' => trim($_POST['video_url']),
                'duration_minutes' => trim($_POST['duration_minutes']),
                'sort_order' => trim($_POST['sort_order']),
                'course' => $course,
                'title_err' => '',
                'content_err' => '',
                'video_url_err' => '',
                'duration_minutes_err' => '',
                'sort_order_err' => ''
            ];
            
            // Validate data
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề bài học';
            }
            
            if(empty($data['video_url'])) {
                $data['video_url_err'] = 'Vui lòng nhập URL video';
            } elseif(!filter_var($data['video_url'], FILTER_VALIDATE_URL)) {
                $data['video_url_err'] = 'URL video không hợp lệ';
            }
            
            if(empty($data['duration_minutes'])) {
                $data['duration_minutes_err'] = 'Vui lòng nhập thời lượng bài học';
            } elseif(!is_numeric($data['duration_minutes']) || $data['duration_minutes'] <= 0) {
                $data['duration_minutes_err'] = 'Thời lượng phải là số dương';
            }
            
            if(empty($data['sort_order'])) {
                $data['sort_order_err'] = 'Vui lòng nhập thứ tự bài học';
            } elseif(!is_numeric($data['sort_order']) || $data['sort_order'] < 0) {
                $data['sort_order_err'] = 'Thứ tự phải là số không âm';
            }
            
            // Check if there are no errors
            if(empty($data['title_err']) && empty($data['video_url_err']) && 
               empty($data['duration_minutes_err']) && empty($data['sort_order_err'])) {
                
                // Update lesson
                if($this->lessonModel->updateLesson($data)) {
                    $_SESSION['success_msg'] = 'Cập nhật bài học thành công';
                    $this->redirect('admin/lessons/' . $lesson->course_id);
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                    $this->view('admin/lessons/edit', $data);
                }
            } else {
                // Load view with errors
                $this->view('admin/lessons/edit', $data);
            }
        } else {
            // Init data
            $data = [
                'id' => $id,
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'content' => $lesson->content,
                'video_url' => $lesson->video_url,
                'duration_minutes' => $lesson->duration_minutes,
                'sort_order' => $lesson->sort_order,
                'course' => $course,
                'title_err' => '',
                'content_err' => '',
                'video_url_err' => '',
                'duration_minutes_err' => '',
                'sort_order_err' => ''
            ];
            
            $this->view('admin/lessons/edit', $data);
        }
    }
    
    // Xóa bài học
    public function delete_lesson($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lesson = $this->lessonModel->getLessonById($id);
            
            if(!$lesson) {
                $_SESSION['error_msg'] = 'Không tìm thấy bài học';
                $this->redirect('admin/courses');
            }
            
            // Delete lesson
            if($this->lessonModel->deleteLesson($id)) {
                $_SESSION['success_msg'] = 'Xóa bài học thành công';
                $this->redirect('admin/lessons/' . $lesson->course_id);
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                $this->redirect('admin/lessons/' . $lesson->course_id);
            }
        } else {
            $this->redirect('admin/courses');
        }
    }
    
    // Quản lý phòng học
    public function classrooms() {
        $classrooms = $this->classroomModel->getAllClassrooms();
        
        $data = [
            'classrooms' => $classrooms
        ];
        
        $this->view('admin/classrooms/index', $data);
    }
    
    // Thêm phòng học mới
    public function add_classroom() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'capacity' => trim($_POST['capacity']),
                'location' => trim($_POST['location']),
                'active' => trim($_POST['active']),
                'name_err' => '',
                'description_err' => '',
                'capacity_err' => '',
                'active_err' => ''
            ];
            
            // Validate data
            if(empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên phòng học';
            }
            
            if(empty($data['capacity'])) {
                $data['capacity_err'] = 'Vui lòng nhập sức chứa';
            } elseif(!is_numeric($data['capacity']) || $data['capacity'] <= 0) {
                $data['capacity_err'] = 'Sức chứa phải là số dương';
            }
            
            if(empty($data['active'])) {
                $data['active_err'] = 'Vui lòng chọn trạng thái';
            }
            
            // Check if there are no errors
            if(empty($data['name_err']) && empty($data['capacity_err']) && empty($data['active_err'])) {
                // Add classroom
                if($this->classroomModel->addClassroom($data)) {
                    $_SESSION['success_msg'] = 'Thêm phòng học thành công';
                    $this->redirect('admin/classrooms');
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                    $this->view('admin/classrooms/add', $data);
                }
            } else {
                // Load view with errors
                $this->view('admin/classrooms/add', $data);
            }
        } else {
            // Init data
            $data = [
                'name' => '',
                'description' => '',
                'capacity' => '30',
                'location' => '',
                'active' => 'active',
                'name_err' => '',
                'description_err' => '',
                'capacity_err' => '',
                'active_err' => ''
            ];
            
            $this->view('admin/classrooms/add', $data);
        }
    }
    
    // Sửa phòng học
    public function edit_classroom($id) {
        $classroom = $this->classroomModel->getClassroomById($id);
        
        if(!$classroom) {
            $_SESSION['error_msg'] = 'Không tìm thấy phòng học';
            $this->redirect('admin/classrooms');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'capacity' => trim($_POST['capacity']),
                'location' => trim($_POST['location']),
                'active' => trim($_POST['active']),
                'name_err' => '',
                'description_err' => '',
                'capacity_err' => '',
                'active_err' => ''
            ];
            
            // Validate data
            if(empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên phòng học';
            }
            
            if(empty($data['capacity'])) {
                $data['capacity_err'] = 'Vui lòng nhập sức chứa';
            } elseif(!is_numeric($data['capacity']) || $data['capacity'] <= 0) {
                $data['capacity_err'] = 'Sức chứa phải là số dương';
            }
            
            if(empty($data['active'])) {
                $data['active_err'] = 'Vui lòng chọn trạng thái';
            }
            
            // Check if there are no errors
            if(empty($data['name_err']) && empty($data['capacity_err']) && empty($data['active_err'])) {
                // Update classroom
                if($this->classroomModel->updateClassroom($data)) {
                    $_SESSION['success_msg'] = 'Cập nhật phòng học thành công';
                    $this->redirect('admin/classrooms');
                } else {
                    $_SESSION['error_msg'] = 'Có lỗi xảy ra, vui lòng thử lại';
                    $this->view('admin/classrooms/edit', $data);
                }
            } else {
                // Load view with errors
                $this->view('admin/classrooms/edit', $data);
            }
        } else {
            // Init data
            $data = [
                'id' => $id,
                'name' => $classroom->name,
                'description' => $classroom->description,
                'capacity' => $classroom->capacity,
                'location' => $classroom->location,
                'active' => $classroom->active,
                'name_err' => '',
                'description_err' => '',
                'capacity_err' => '',
                'active_err' => ''
            ];
            
            $this->view('admin/classrooms/edit', $data);
        }
    }
    
    // Xóa phòng học
    public function delete_classroom($id) {
        // Log debug info
        error_log("DELETE CLASSROOM DEBUG - Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("DELETE CLASSROOM DEBUG - Classroom ID: " . $id);
        error_log("DELETE CLASSROOM DEBUG - Session User ID: " . ($_SESSION['user_id'] ?? 'not set'));
        error_log("DELETE CLASSROOM DEBUG - Session User Role: " . ($_SESSION['user_role'] ?? 'not set'));
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            error_log("DELETE CLASSROOM DEBUG - Processing POST request");
            
            $classroom = $this->classroomModel->getClassroomById($id);
            error_log("DELETE CLASSROOM DEBUG - Found classroom: " . ($classroom ? "YES (Name: {$classroom->name})" : "NO"));
            
            if(!$classroom) {
                error_log("DELETE CLASSROOM DEBUG - Classroom not found");
                $_SESSION['admin_classroom_message'] = 'Không tìm thấy phòng học với ID: ' . $id;
                $_SESSION['admin_classroom_message_class'] = 'alert alert-danger';
                $this->redirect('admin/classrooms');
            }
            
            // Kiểm tra xem phòng học có đang được sử dụng không
            try {
                $this->db = new Database;
                $this->db->query("SELECT COUNT(*) as count FROM courses WHERE classroom_id = :id");
                $this->db->bind(':id', $id);
                $count = $this->db->single()->count;
                error_log("DELETE CLASSROOM DEBUG - Course count using this classroom: " . $count);
                
                if($count > 0) {
                    error_log("DELETE CLASSROOM DEBUG - Cannot delete, classroom in use");
                    $_SESSION['admin_classroom_message'] = 'Không thể xóa phòng học "' . $classroom->name . '" vì đang có ' . $count . ' khóa học sử dụng';
                    $_SESSION['admin_classroom_message_class'] = 'alert alert-warning';
                    $this->redirect('admin/classrooms');
                }
                
                // Delete classroom
                $deleteResult = $this->classroomModel->deleteClassroom($id);
                error_log("DELETE CLASSROOM DEBUG - Delete result: " . ($deleteResult ? "SUCCESS" : "FAILED"));
                
                if($deleteResult) {
                    error_log("DELETE CLASSROOM DEBUG - Delete successful");
                    $_SESSION['admin_classroom_message'] = 'Xóa phòng học "' . $classroom->name . '" thành công';
                    $_SESSION['admin_classroom_message_class'] = 'alert alert-success';
                    $this->redirect('admin/classrooms');
                } else {
                    error_log("DELETE CLASSROOM DEBUG - Delete failed");
                    $_SESSION['admin_classroom_message'] = 'Có lỗi xảy ra khi xóa phòng học "' . $classroom->name . '", vui lòng thử lại';
                    $_SESSION['admin_classroom_message_class'] = 'alert alert-danger';
                    $this->redirect('admin/classrooms');
                }
            } catch (Exception $e) {
                error_log("DELETE CLASSROOM DEBUG - Exception: " . $e->getMessage());
                $_SESSION['admin_classroom_message'] = 'Lỗi hệ thống: ' . $e->getMessage();
                $_SESSION['admin_classroom_message_class'] = 'alert alert-danger';
                $this->redirect('admin/classrooms');
            }
        } else {
            error_log("DELETE CLASSROOM DEBUG - Not POST request, redirecting");
            $_SESSION['admin_classroom_message'] = 'Yêu cầu không hợp lệ (phải là POST request)';
            $_SESSION['admin_classroom_message_class'] = 'alert alert-danger';
            $this->redirect('admin/classrooms');
        }
    }

    // --- Quản lý duyệt công thức ---

    // Hiển thị trang quản lý công thức chờ duyệt
    public function manageRecipes() {
        // Sử dụng hàm getPendingRecipes từ RecipeModel
        $pendingRecipes = $this->recipeModel->getPendingRecipes(); 

        $data = [
            'pending_recipes' => $pendingRecipes
        ];

        $this->view('admin/manage_recipes', $data);
    }

    // Duyệt (approve) công thức
    public function approveRecipe($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Cập nhật trạng thái thành 'approved'
            if($this->recipeModel->updateRecipeStatus($id, 'approved')) {
                $_SESSION['success_msg'] = 'Công thức ID: ' . $id . ' đã được duyệt thành công.';
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra khi duyệt công thức ID: ' . $id . '.';
            }
            $this->redirect('admin/manageRecipes');
        } else {
            // Chuyển hướng nếu truy cập trực tiếp qua GET
            $this->redirect('admin/manageRecipes');
        }
    }

    // Từ chối (reject) công thức
    public function rejectRecipe($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Cập nhật trạng thái thành 'rejected'
            if($this->recipeModel->updateRecipeStatus($id, 'rejected')) {
                $_SESSION['success_msg'] = 'Công thức ID: ' . $id . ' đã bị từ chối.';
                // Tùy chọn: Gửi email thông báo cho người dùng?
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra khi từ chối công thức ID: ' . $id . '.';
            }
            // Lưu ý: Công thức bị từ chối vẫn còn trong DB với status='rejected'.
            // Nếu muốn xóa hẳn, bạn có thể gọi $this->recipeModel->deleteRecipe($id) thay vì updateRecipeStatus.
            $this->redirect('admin/manageRecipes');
        } else {
            // Chuyển hướng nếu truy cập trực tiếp qua GET
            $this->redirect('admin/manageRecipes');
        }
    }
    // --- Kết thúc Quản lý duyệt công thức ---

    // --- Quản lý Bình luận (từ bảng ratings) ---
    public function manageComments() {
        // Lấy tất cả đánh giá CÓ bình luận
        $ratingsWithComments = $this->ratingModel->getAllRatingsWithComments(); 

        $data = [
            'comments' => $ratingsWithComments, // Giữ tên biến 'comments' cho view
            'total_comments' => $this->ratingModel->getTotalRatingsWithComments()
        ];

        // Sử dụng view manage_comments (sẽ tạo ở bước sau)
        $this->view('admin/manage_comments', $data);
    }
    
    // Xóa đánh giá (bao gồm cả bình luận)
    public function deleteComment($id) { // Giữ tên hàm để khớp với URL/route
         if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra xem rating có tồn tại không
            $rating = $this->ratingModel->getRatingById($id);
            if (!$rating) {
                 $_SESSION['error_msg'] = 'Không tìm thấy đánh giá/bình luận để xóa.';
                 $this->redirect('admin/manageComments');
                 return;
            }
            
            // Tiến hành xóa rating (bao gồm cả comment)
            if($this->ratingModel->deleteRating($id)) {
                $_SESSION['success_msg'] = 'Đã xóa đánh giá/bình luận ID: ' . $id . ' thành công.';
            } else {
                $_SESSION['error_msg'] = 'Có lỗi xảy ra khi xóa đánh giá/bình luận ID: ' . $id . '.';
            }
            $this->redirect('admin/manageComments');
        } else {
            // Chuyển hướng nếu truy cập trực tiếp qua GET
            $this->redirect('admin/manageComments');
        }
    }
    // --- Kết thúc Quản lý Bình luận ---

}
?>
