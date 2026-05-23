<?php
class Home extends Controller {
    private $recipeModel;
    private $categoryModel;
    private $contactModel;
    
    public function __construct() {
        $this->recipeModel = $this->model('Recipe');
        $this->categoryModel = $this->model('Category');
        $this->contactModel = $this->model('Contact');
    }

    // Keep existing methods like index() and about()...

    public function contact() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'subject' => trim($_POST['subject']),
                'message' => trim($_POST['message']),
                'name_err' => '',
                'email_err' => '',
                'subject_err' => '',
                'message_err' => '',
                'success_message' => ''
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
                
                // Add debugging
                try {
                    // Save to database
                    if($this->contactModel->addContact($data)) {
                        $data['success_message'] = 'Cảm ơn! Tin nhắn của bạn đã được gửi thành công.';
                        
                        // Reset form fields
                        $data['name'] = '';
                        $data['email'] = '';
                        $data['subject'] = '';
                        $data['message'] = '';
                    } else {
                        $data['success_message'] = 'Có lỗi xảy ra, vui lòng thử lại sau!';
                    }
                } catch (Exception $e) {
                    $data['success_message'] = 'Lỗi: ' . $e->getMessage();
                }
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
