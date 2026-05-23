<?php
class RatingsController extends Controller {
    private $ratingModel;
    private $recipeModel;
    
    public function __construct() {
        $this->ratingModel = $this->model('Rating');
        $this->recipeModel = $this->model('Recipe');
    }

    public function add() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $recipe_id = $_POST['recipe_id'];
            $rating = $_POST['rating'];
            $comment = trim($_POST['comment']);
            
            // Check if recipe exists and is approved
            $recipe = $this->recipeModel->getApprovedRecipeById($recipe_id);
            if(!$recipe) {
                $_SESSION['error_msg'] = 'Công thức không tồn tại hoặc chưa được duyệt';
                $this->redirect('recipes');
            }
            
            // Check if user already rated this recipe
            if($this->ratingModel->checkUserRating($recipe_id, $_SESSION['user_id'])) {
                $_SESSION['error_msg'] = 'Bạn đã đánh giá công thức này rồi';
                $this->redirect('recipes/show/' . $recipe_id);
            }

            // Validate rating
            if($rating < 1 || $rating > 5) {
                $_SESSION['error_msg'] = 'Đánh giá phải từ 1 đến 5 sao';
                $this->redirect('recipes/show/' . $recipe_id);
            }

            $data = [
                'recipe_id' => $recipe_id,
                'user_id' => $_SESSION['user_id'],
                'rating' => $rating,
                'comment' => $comment
            ];

            if($this->ratingModel->addRating($data)) {
                $_SESSION['success_msg'] = 'Thêm đánh giá thành công';
                $this->redirect('recipes/show/' . $recipe_id);
            } else {
                die('Có lỗi xảy ra');
            }
        } else {
            $this->redirect('recipes');
        }
    }

    public function delete($id) {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
        }

        // Get the rating
        $this->ratingModel->query('SELECT * FROM ratings WHERE id = :id');
        $this->ratingModel->bind(':id', $id);
        $rating = $this->ratingModel->single();

        // Check if rating exists
        if(!$rating) {
            $this->redirect('recipes');
        }

        // Check for owner or admin
        if($rating->user_id != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            $this->redirect('recipes');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($this->ratingModel->deleteRating($id)) {
                $_SESSION['success_msg'] = 'Xóa đánh giá thành công';
                $this->redirect('recipes/show/' . $rating->recipe_id);
            } else {
                die('Có lỗi xảy ra');
            }
        } else {
            $this->redirect('recipes/show/' . $rating->recipe_id);
        }
    }
}
?>
