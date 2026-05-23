<?php
class RecipesController extends Controller {
    private $recipeModel;
    private $categoryModel;
    private $ratingModel;
    
    public function __construct() {
        $this->recipeModel = $this->model('Recipe');
        $this->categoryModel = $this->model('Category');
        $this->ratingModel = $this->model('Rating');
    }

    public function index() {
        // Get approved recipes
        $recipes = $this->recipeModel->getApprovedRecipes();
        $categories = $this->categoryModel->getCategories();

        $data = [
            'recipes' => $recipes,
            'categories' => $categories
        ];

        $this->view('recipes/index', $data);
    }

    public function show($id) {
        $recipe = $this->recipeModel->getApprovedRecipeById($id);

        // If recipe not found or not approved, redirect or show error
        if(!$recipe) {
            $_SESSION['error_msg'] = 'Công thức không tồn tại hoặc chưa được duyệt.';
            $this->redirect('recipes');
            return; // Important to stop execution
        }
        
        $ratings = $this->ratingModel->getRatingsForRecipe($id);
        $avg_rating = $this->ratingModel->getAverageRating($id);
        
        // Check if user has already rated this recipe
        $user_rated = false;
        if($this->isLoggedIn()) {
            $user_rated = $this->ratingModel->checkUserRating($id, $_SESSION['user_id']);
        }

        $data = [
            'recipe' => $recipe,
            'ratings' => $ratings,
            'avg_rating' => $avg_rating,
            'user_rated' => $user_rated
        ];

        $this->view('recipes/show', $data);
    }

    public function add() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Handle file upload
            $image = 'no-image.jpg'; // Default image
            $image_err = '';
            
            if(!empty($_FILES['image']['name'])) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
                $max_size = 2 * 1024 * 1024; // 2MB in bytes
                
                // Check file type
                $file_type = $_FILES['image']['type'];
                if(!in_array($file_type, $allowed_types)) {
                    $image_err = 'Chỉ chấp nhận file JPG, JPEG, PNG';
                }
                
                // Check file size
                if($_FILES['image']['size'] > $max_size) {
                    $image_err = 'Dung lượng file phải dưới 2MB';
                }
                
                // If no errors, upload file
                if(empty($image_err)) {
                    $target_dir = "public/uploads/";
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image = time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $image;
                    
                    // Move uploaded file
                    if(!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image_err = 'Không thể tải file lên. Vui lòng thử lại';
                        $image = 'no-image.jpg';
                    }
                } else {
                    $image = 'no-image.jpg';
                }
            }
            
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'ingredients' => trim($_POST['ingredients']),
                'instructions' => trim($_POST['instructions']),
                'image' => $image,
                'video_url' => !empty($_POST['video_url']) ? trim($_POST['video_url']) : null,
                'category_id' => $_POST['category_id'],
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'description_err' => '',
                'ingredients_err' => '',
                'instructions_err' => '',
                'category_id_err' => '',
                'video_url_err' => '',
                'image_err' => $image_err
            ];

            // Validate data
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề';
            }
            if(empty($data['description'])) {
                $data['description_err'] = 'Vui lòng nhập mô tả';
            }
            if(empty($data['ingredients'])) {
                $data['ingredients_err'] = 'Vui lòng nhập nguyên liệu';
            }
            if(empty($data['instructions'])) {
                $data['instructions_err'] = 'Vui lòng nhập hướng dẫn';
            }
            if(empty($data['category_id'])) {
                $data['category_id_err'] = 'Vui lòng chọn danh mục';
            }

            // Make sure no errors
            if(empty($data['title_err']) && empty($data['description_err']) && 
               empty($data['ingredients_err']) && empty($data['instructions_err']) && 
               empty($data['category_id_err']) && empty($data['image_err'])) {
                // Add recipe (model handles setting status to pending)
                if($this->recipeModel->addRecipe($data)) {
                    $_SESSION['success_msg'] = 'Công thức của bạn đã được gửi và đang chờ duyệt.';
                    $this->redirect('recipes');
                } else {
                    die('Có lỗi xảy ra khi thêm công thức');
                }
            } else {
                // Load view with errors
                $data['categories'] = $this->categoryModel->getCategories();
                $this->view('recipes/add', $data);
            }
        } else {
            $data = [
                'title' => '',
                'description' => '',
                'ingredients' => '',
                'instructions' => '',
                'image' => '',
                'video_url' => '',
                'category_id' => '',
                'title_err' => '',
                'description_err' => '',
                'ingredients_err' => '',
                'instructions_err' => '',
                'category_id_err' => '',
                'video_url_err' => '',
                'image_err' => '',
                'categories' => $this->categoryModel->getCategories()
            ];

            $this->view('recipes/add', $data);
        }
    }

    public function edit($id) {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        $recipe = $this->recipeModel->getRecipeByIdForAdmin($id);

        // Check if recipe exists
        if(!$recipe) {
            $this->redirect('recipes');
        }

        // Check for recipe owner, admin, or manager
        if($recipe->user_id != $_SESSION['user_id'] && 
           $_SESSION['user_role'] != 'admin' && 
           $_SESSION['user_role'] != 'manager') {
            $this->redirect('recipes');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Handle file upload
            $image = $recipe->image; // Keep existing image by default
            $image_err = '';
            
            if(!empty($_FILES['image']['name'])) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
                $max_size = 2 * 1024 * 1024; // 2MB in bytes
                
                // Check file type
                $file_type = $_FILES['image']['type'];
                if(!in_array($file_type, $allowed_types)) {
                    $image_err = 'Chỉ chấp nhận file JPG, JPEG, PNG';
                }
                
                // Check file size
                if($_FILES['image']['size'] > $max_size) {
                    $image_err = 'Dung lượng file phải dưới 2MB';
                }
                
                // If no errors, upload file
                if(empty($image_err)) {
                    $target_dir = "public/uploads/";
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image = time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $image;
                    
                    // Move uploaded file
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        // File upload successful
                        // Delete old image if it wasn't the default
                        if($recipe->image != 'no-image.jpg') {
                            @unlink($target_dir . $recipe->image);
                        }
                    } else {
                        $image_err = 'Không thể tải file lên. Vui lòng thử lại';
                        $image = $recipe->image;
                    }
                } else {
                    $image = $recipe->image;
                }
            }
            
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'ingredients' => trim($_POST['ingredients']),
                'instructions' => trim($_POST['instructions']),
                'image' => $image,
                'video_url' => !empty($_POST['video_url']) ? trim($_POST['video_url']) : null,
                'category_id' => $_POST['category_id'],
                'title_err' => '',
                'description_err' => '',
                'ingredients_err' => '',
                'instructions_err' => '',
                'category_id_err' => '',
                'video_url_err' => '',
                'image_err' => $image_err
            ];

            // Validate data
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề';
            }
            if(empty($data['description'])) {
                $data['description_err'] = 'Vui lòng nhập mô tả';
            }
            if(empty($data['ingredients'])) {
                $data['ingredients_err'] = 'Vui lòng nhập nguyên liệu';
            }
            if(empty($data['instructions'])) {
                $data['instructions_err'] = 'Vui lòng nhập hướng dẫn';
            }
            if(empty($data['category_id'])) {
                $data['category_id_err'] = 'Vui lòng chọn danh mục';
            }

            // Make sure no errors
            if(empty($data['title_err']) && empty($data['description_err']) && 
               empty($data['ingredients_err']) && empty($data['instructions_err']) && 
               empty($data['category_id_err']) && empty($data['image_err'])) {
                // Update recipe
                if($this->recipeModel->updateRecipe($data)) {
                    $_SESSION['success_msg'] = 'Cập nhật công thức thành công';
                    $this->redirect('recipes/show/'.$id);
                } else {
                    die('Có lỗi xảy ra');
                }
            } else {
                // Load view with errors
                $data['categories'] = $this->categoryModel->getCategories();
                $this->view('recipes/edit', $data);
            }
        } else {
            $data = [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'description' => $recipe->description,
                'ingredients' => $recipe->ingredients,
                'instructions' => $recipe->instructions,
                'image' => $recipe->image,
                'video_url' => $recipe->video_url ?? '',
                'category_id' => $recipe->category_id,
                'title_err' => '',
                'description_err' => '',
                'ingredients_err' => '',
                'instructions_err' => '',
                'category_id_err' => '',
                'video_url_err' => '',
                'image_err' => '',
                'categories' => $this->categoryModel->getCategories()
            ];

            $this->view('recipes/edit', $data);
        }
    }

    public function delete($id) {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        $recipe = $this->recipeModel->getRecipeByIdForAdmin($id);

        // Check if recipe exists
        if(!$recipe) {
            $this->redirect('recipes');
        }

        // Check for recipe owner, admin, or manager
        if($recipe->user_id != $_SESSION['user_id'] && 
           $_SESSION['user_role'] != 'admin' && 
           $_SESSION['user_role'] != 'manager') {
            $this->redirect('recipes');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($this->recipeModel->deleteRecipe($id)) {
                // Delete image if it's not the default
                if($recipe->image != 'no-image.jpg') {
                    @unlink('public/uploads/' . $recipe->image);
                }
                $_SESSION['success_msg'] = 'Xóa công thức thành công';
                $this->redirect('recipes');
            } else {
                die('Có lỗi xảy ra');
            }
        } else {
            $this->redirect('recipes/show/' . $id);
        }
    }

    public function search() {
        if(isset($_GET['term']) || isset($_GET['ingredients']) || isset($_GET['category'])) {
            // Xử lý các tham số tìm kiếm
            $term = isset($_GET['term']) ? trim($_GET['term']) : '';
            $ingredients = isset($_GET['ingredients']) ? trim($_GET['ingredients']) : '';
            $category = isset($_GET['category']) ? trim($_GET['category']) : '';
            
            // Xác định loại tìm kiếm
            $isAdvancedSearch = (!empty($ingredients) || !empty($category));
            
            // Lấy kết quả tìm kiếm
            if($isAdvancedSearch) {
                $searchData = [
                    'term' => $term,
                    'ingredients' => $ingredients,
                    'category' => $category
                ];
                $recipes = $this->recipeModel->searchRecipesAdvanced($searchData);
            } else {
                $recipes = $this->recipeModel->searchRecipes($term);
            }
            
            $categories = $this->categoryModel->getCategories();

            $data = [
                'recipes' => $recipes,
                'categories' => $categories,
                'term' => $term,
                'ingredients' => $ingredients,
                'category' => $category,
                'is_advanced_search' => $isAdvancedSearch
            ];

            $this->view('recipes/search', $data);
        } else {
            $this->redirect('recipes');
        }
    }

    public function category($id) {
        $category = $this->categoryModel->getCategoryById($id);
        
        if(!$category) {
            $this->redirect('recipes');
            return;
        }
        
        // Get approved recipes by category (model method filters by approved status)
        $recipes = $this->recipeModel->getRecipesByCategory($id);
        $categories = $this->categoryModel->getCategories();

        $data = [
            'category' => $category,
            'recipes' => $recipes,
            'categories' => $categories
        ];

        $this->view('recipes/category', $data);
    }
}
?>
