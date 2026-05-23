<?php
class UsersController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function register() {
        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Init data
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            } else {
                // Check email
                if($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email đã được sử dụng';
                }
            }

            // Validate Name
            if(empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên';
            }

            // Validate Password
            if(empty($data['password'])) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            // Validate Confirm Password
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Vui lòng xác nhận mật khẩu';
            } else {
                if($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Mật khẩu không khớp';
                }
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Validated
                
                // Register User
                if($this->userModel->register($data)) {
                    // Set flash message
                    $_SESSION['success_msg'] = 'Đăng ký thành công, vui lòng đăng nhập';
                    $this->redirect('users/login');
                } else {
                    die('Có lỗi xảy ra');
                }
            } else {
                // Load view with errors
                $this->view('users/register', $data);
            }
        } else {
            // Init data
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load view
            $this->view('users/register', $data);
        }
    }

    public function login() {
        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',      
            ];
            
            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            }
            
            // Validate Password
            if(empty($data['password'])) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu';
            }

            // Check for user/email
            if(!$this->userModel->findUserByEmail($data['email'])) {
                // User not found
                $data['email_err'] = 'Không tìm thấy người dùng';
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if($loggedInUser) {
                    // Create Session
                    $_SESSION['user_id'] = $loggedInUser->id;
                    $_SESSION['user_email'] = $loggedInUser->email;
                    $_SESSION['user_name'] = $loggedInUser->name;
                    $_SESSION['user_role'] = $loggedInUser->role;
                    
                    // Ghi log địa chỉ IP đăng nhập
                    // Lấy địa chỉ IP thực của người dùng (IPv4 công khai)
                    $local_ip = '';
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $local_ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                        $local_ip = trim($ip_list[0]);
                    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
                        $local_ip = $_SERVER['HTTP_X_REAL_IP'];
                    } else {
                        $local_ip = $_SERVER['REMOTE_ADDR'];
                    }
                    
                    // Nếu là địa chỉ localhost, thử lấy IP công khai từ API
                    $ip_address = $local_ip;
                    if ($local_ip == '::1' || $local_ip == '127.0.0.1') {
                        // Thử lấy IP công khai từ dịch vụ bên ngoài
                        $external_ip = @file_get_contents('https://api.ipify.org');
                        if ($external_ip !== false) {
                            $ip_address = $external_ip;
                        }
                    }
                    
                    // Lấy thông tin chi tiết về IP từ ipinfo.io
                    $ip_info = '';
                    $location = '';
                    $isp = '';
                    
                    try {
                        // Thêm token API của ipinfo.io
                        $ipinfo_token = '9cdad51214bba8'; // Thay thế bằng token của bạn
                        $ipinfo_url = "https://ipinfo.io/{$ip_address}/json?token={$ipinfo_token}";
                        $ipinfo_json = @file_get_contents($ipinfo_url);
                        
                        if ($ipinfo_json !== false) {
                            $ipinfo_data = json_decode($ipinfo_json, true);
                            
                            if (isset($ipinfo_data['city']) && isset($ipinfo_data['region']) && isset($ipinfo_data['country'])) {
                                $location = $ipinfo_data['city'] . ', ' . $ipinfo_data['region'] . ', ' . $ipinfo_data['country'];
                            }
                            
                            if (isset($ipinfo_data['org'])) {
                                $isp = $ipinfo_data['org'];
                            }
                            
                            $ip_info = "Vị trí: {$location} | Nhà mạng: {$isp}";
                        }
                    } catch (Exception $e) {
                        // Bỏ qua lỗi khi gọi API
                        $ip_info = "Không thể lấy thông tin IP";
                    }
                    
                    // Lấy User Agent gốc (không parse để tăng tốc độ)
                    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                    
                    $log_time = date('Y-m-d H:i:s');
                    $log_message = "$log_time | Đăng nhập thành công | Email: {$loggedInUser->email} | IP: $ip_address | Local IP: $local_ip | $ip_info | User-Agent: $user_agent\n";
                    $log_file = APP_ROOT . '/logs/login.log';
                    file_put_contents($log_file, $log_message, FILE_APPEND);
                    
                    if($loggedInUser->role == 'admin') {
                        $this->redirect('admin');
                    } else {
                        $this->redirect('recipes');
                    }
                } else {
                    $data['password_err'] = 'Mật khẩu không chính xác';
                    $this->view('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }
        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => '',        
            ];

            // Load view
            $this->view('users/login', $data);
        }
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        $this->redirect('users/login');
    }

    public function profile() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Lấy thông tin user hiện tại
            $currentUser = $this->userModel->getUserById($_SESSION['user_id']);
            
            // Init data
            $data = [
                'id' => $_SESSION['user_id'],
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'avatar' => $currentUser->avatar ?? 'default-avatar.png',
                'name_err' => '',
                'email_err' => '',
                'phone_err' => '',
                'avatar_err' => ''
            ];

            // Validate Name
            if(empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên';
            }

            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            }

            // Validate Phone (nếu có nhập)
            if(!empty($data['phone'])) {
                // Kiểm tra định dạng số điện thoại (10-11 số)
                if(!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
                    $data['phone_err'] = 'Số điện thoại không hợp lệ (10-11 chữ số)';
                }
            }

            // Xử lý upload avatar
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
                $max_size = 2 * 1024 * 1024; // 2MB in bytes
                
                // Check file type
                $file_type = $_FILES['avatar']['type'];
                if(!in_array($file_type, $allowed_types)) {
                    $data['avatar_err'] = 'Chỉ chấp nhận file JPG, JPEG, PNG';
                } 
                // Check file size
                elseif($_FILES['avatar']['size'] > $max_size) {
                    $data['avatar_err'] = 'Dung lượng file phải dưới 2MB';
                } 
                else {
                    // Tạo tên file unique
                    $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                    $newFilename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $destination = 'public/uploads/avatars/' . $newFilename;
                    
                    // Tạo thư mục nếu chưa có
                    if(!is_dir('public/uploads/avatars')) {
                        mkdir('public/uploads/avatars', 0777, true);
                    }
                    
                    // Upload file
                    if(move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                        // Xóa avatar cũ (nếu không phải default)
                        if(!empty($currentUser->avatar) && $currentUser->avatar != 'default-avatar.png' && file_exists('public/uploads/avatars/' . $currentUser->avatar)) {
                            unlink('public/uploads/avatars/' . $currentUser->avatar);
                        }
                        $data['avatar'] = $newFilename;
                    } else {
                        $data['avatar_err'] = 'Không thể tải file lên. Vui lòng thử lại';
                    }
                }
            }

            // Make sure errors are empty
            if(empty($data['name_err']) && empty($data['email_err']) && empty($data['phone_err']) && empty($data['avatar_err'])) {
                // Update user với method mới
                if($this->userModel->updateProfileExtended($data)) {
                    $_SESSION['user_name'] = $data['name'];
                    $_SESSION['user_email'] = $data['email'];
                    $_SESSION['success_msg'] = 'Cập nhật thông tin thành công';
                    $this->redirect('users/profile');
                } else {
                    die('Có lỗi xảy ra');
                }
            } else {
                // Load view with errors
                // Lấy lại thông tin user recipes
                require_once APP_ROOT . '/models/Recipe.php';
                $recipeModel = new Recipe();
                $recipeModel->query('SELECT * FROM recipes WHERE user_id = :user_id ORDER BY created_at DESC');
                $recipeModel->bind(':user_id', $_SESSION['user_id']);
                $data['user_recipes'] = $recipeModel->resultSet();
                
                $this->view('users/profile', $data);
            }
        } else {
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            
            // Lấy danh sách công thức của người dùng
            require_once APP_ROOT . '/models/Recipe.php';
            $recipeModel = new Recipe();
            $recipeModel->query('SELECT * FROM recipes WHERE user_id = :user_id ORDER BY created_at DESC');
            $recipeModel->bind(':user_id', $_SESSION['user_id']);
            $userRecipes = $recipeModel->resultSet();
            
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
                'avatar' => $user->avatar ?? 'default-avatar.png',
                'name_err' => '',
                'email_err' => '',
                'phone_err' => '',
                'avatar_err' => '',
                'user_recipes' => $userRecipes
            ];

            $this->view('users/profile', $data);
        }
    }

    public function changePassword() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Init data
            $data = [
                'id' => $_SESSION['user_id'],
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate Current Password
            if(empty($data['current_password'])) {
                $data['current_password_err'] = 'Vui lòng nhập mật khẩu hiện tại';
            } else {
                // Verify current password
                $user = $this->userModel->getUserById($data['id']);
                if(!password_verify($data['current_password'], $user->password)) {
                    $data['current_password_err'] = 'Mật khẩu hiện tại không chính xác';
                }
            }

            // Validate New Password
            if(empty($data['new_password'])) {
                $data['new_password_err'] = 'Vui lòng nhập mật khẩu mới';
            } elseif(strlen($data['new_password']) < 6) {
                $data['new_password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            // Validate Confirm Password
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Vui lòng xác nhận mật khẩu mới';
            } else {
                if($data['new_password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Mật khẩu mới không khớp';
                }
            }

            // Make sure errors are empty
            if(empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])) {
                // Update password
                if($this->userModel->updatePassword($data)) {
                    $_SESSION['success_msg'] = 'Đổi mật khẩu thành công';
                    $this->redirect('users/profile');
                } else {
                    die('Có lỗi xảy ra');
                }
            } else {
                // Load view with errors
                $this->view('users/change_password', $data);
            }
        } else {
            // Init data
            $data = [
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load view
            $this->view('users/change_password', $data);
        }
    }
}
?>
