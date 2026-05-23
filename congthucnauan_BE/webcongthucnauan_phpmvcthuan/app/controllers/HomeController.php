<?php
require_once APP_ROOT . '/helpers/debug_helper.php';

class HomeController extends Controller {
    private $recipeModel;
    private $categoryModel;
    private $contactModel;
    
    public function __construct() {
        $this->recipeModel = $this->model('Recipe');
        $this->categoryModel = $this->model('Category');
        $this->contactModel = $this->model('Contact');
    }

    public function index() {
        // Get featured recipes (most recent AND approved)
        $this->recipeModel->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                                FROM recipes 
                                INNER JOIN categories ON recipes.category_id = categories.id 
                                INNER JOIN users ON recipes.user_id = users.id 
                                WHERE recipes.status = :status
                                ORDER BY recipes.created_at DESC LIMIT 6');
        $this->recipeModel->bind(':status', 'approved');
        $recipes = $this->recipeModel->resultSet();
        
        // Get categories
        $categories = $this->categoryModel->getCategories();

        $data = [
            'recipes' => $recipes,
            'categories' => $categories
        ];

        $this->view('home/index', $data);
    }

    public function about() {
        $this->view('home/about');
    }

    public function contact() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            debug_to_file('Contact form submitted', $_POST);
            
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'subject' => trim($_POST['subject']),
                'message' => trim($_POST['message']),
                'name_err' => '',
                'email_err' => '',
                'subject_err' => '',
                'message_err' => '',
                'success_message' => '',
                'error_message' => ''
            ];

            // Validate name
            if(empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên';
            }

            // Validate email
            if(empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            } else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Email không hợp lệ';
            }

            // Validate subject
            if(empty($data['subject'])) {
                $data['subject_err'] = 'Vui lòng nhập tiêu đề';
            }

            // Validate message
            if(empty($data['message'])) {
                $data['message_err'] = 'Vui lòng nhập nội dung';
            }

            // Make sure errors are empty
            if(empty($data['name_err']) && empty($data['email_err']) && 
               empty($data['subject_err']) && empty($data['message_err'])) {
                
                debug_to_file('Form validation passed, attempting database insertion');
                
                // Save to database
                if($this->contactModel->addContact($data)) {
                    debug_to_file('Contact successfully saved to database');
                    $data['success_message'] = 'Cảm ơn bạn đã liên hệ với chúng tôi. Chúng tôi sẽ phản hồi sớm nhất có thể!';
                    
                    // Reset form fields after successful submission
                    $data['name'] = '';
                    $data['email'] = '';
                    $data['subject'] = '';
                    $data['message'] = '';
                } else {
                    debug_to_file('Failed to save contact to database');
                    $data['error_message'] = 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại sau hoặc liên hệ với chúng tôi qua số điện thoại.';
                }
            } else {
                debug_to_file('Form validation failed', [
                    'name_err' => $data['name_err'],
                    'email_err' => $data['email_err'],
                    'subject_err' => $data['subject_err'],
                    'message_err' => $data['message_err']
                ]);
            }
            
            $this->view('home/contact', $data);
            
        } else {
            $data = [
                'name' => '',
                'email' => '',
                'subject' => '',
                'message' => '',
                'name_err' => '',
                'email_err' => '',
                'subject_err' => '',
                'message_err' => '',
                'success_message' => ''
            ];

            $this->view('home/contact', $data);
        }
    }
}
?>
