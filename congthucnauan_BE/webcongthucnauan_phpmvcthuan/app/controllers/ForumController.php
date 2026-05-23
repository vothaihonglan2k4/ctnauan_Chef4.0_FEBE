<?php
class ForumController extends Controller {
    private $forumPostModel;
    private $forumCommentModel;
    private $forumLikeModel;
    private $forumTagModel;
    
    public function __construct() {
        $this->forumPostModel = $this->model('ForumPost');
        $this->forumCommentModel = $this->model('ForumComment');
        $this->forumLikeModel = $this->model('ForumLike');
        $this->forumTagModel = $this->model('ForumTag');
    }

    // Trang chủ diễn đàn - Danh sách bài viết
    public function index() {
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy bài viết
        $posts = $this->forumPostModel->getAllPosts($limit, $offset);
        
        // Lấy tags phổ biến
        $popularTags = $this->forumTagModel->getPopularTags(8);
        
        // Tính tổng số trang
        $totalPosts = $this->forumPostModel->getTotalPosts();
        $totalPages = ceil($totalPosts / $limit);

        $data = [
            'posts' => $posts,
            'popular_tags' => $popularTags,
            'current_page' => $page,
            'total_pages' => $totalPages
        ];

        $this->view('forum/index', $data);
    }

    // Chi tiết bài viết
    public function show($id) {
        // Lấy bài viết
        $post = $this->forumPostModel->getPostById($id);
        
        if(!$post) {
            $_SESSION['error_msg'] = 'Không tìm thấy bài viết';
            $this->redirect('forum');
            return;
        }

        // Tăng views
        $this->forumPostModel->incrementViews($id);
        
        // Lấy comments
        $comments = $this->forumCommentModel->getCommentsByPost($id);
        
        // Lấy tags
        $tags = $this->forumPostModel->getPostTags($id);
        
        // Kiểm tra user đã like chưa
        $userLiked = false;
        if(isset($_SESSION['user_id'])) {
            $userLiked = $this->forumLikeModel->hasUserLiked($id, $_SESSION['user_id']);
        }

        $data = [
            'post' => $post,
            'comments' => $comments,
            'tags' => $tags,
            'user_liked' => $userLiked
        ];

        $this->view('forum/show', $data);
    }

    // Tạo bài viết mới (GET)
    public function create() {
        if(!$this->isLoggedIn()) {
            $_SESSION['error_msg'] = 'Vui lòng đăng nhập để tạo bài viết';
            $this->redirect('users/login');
            return;
        }

        // Lấy danh sách công thức của user (để liên kết)
        require_once APP_ROOT . '/models/Recipe.php';
        $recipeModel = new Recipe();
        $recipeModel->query('SELECT id, title FROM recipes WHERE user_id = :user_id AND status = :status ORDER BY created_at DESC');
        $recipeModel->bind(':user_id', $_SESSION['user_id']);
        $recipeModel->bind(':status', 'approved');
        $userRecipes = $recipeModel->resultSet();

        // Lấy tất cả tags
        $allTags = $this->forumTagModel->getAllTags();

        $data = [
            'title' => '',
            'content' => '',
            'video_url' => '',
            'recipe_id' => '',
            'selected_tags' => [],
            'user_recipes' => $userRecipes,
            'all_tags' => $allTags,
            'title_err' => '',
            'content_err' => '',
            'image_err' => ''
        ];

        $this->view('forum/create', $data);
    }

    // Xử lý tạo bài viết (POST)
    public function store() {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
            return;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'video_url' => trim($_POST['video_url'] ?? ''),
                'recipe_id' => !empty($_POST['recipe_id']) ? $_POST['recipe_id'] : null,
                'selected_tags' => $_POST['tags'] ?? [],
                'image' => null,
                'title_err' => '',
                'content_err' => '',
                'image_err' => ''
            ];

            // Validate
            if(empty($data['title'])) {
                $data['title_err'] = 'Vui lòng nhập tiêu đề';
            }

            if(empty($data['content'])) {
                $data['content_err'] = 'Vui lòng nhập nội dung';
            }

            // Xử lý upload ảnh
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                
                if(in_array(strtolower($filetype), $allowed)) {
                    if($_FILES['image']['size'] <= 5242880) { // 5MB
                        $newFilename = 'forum_' . time() . '_' . uniqid() . '.' . $filetype;
                        $destination = 'public/uploads/forum/' . $newFilename;
                        
                        if(!is_dir('public/uploads/forum')) {
                            mkdir('public/uploads/forum', 0777, true);
                        }
                        
                        if(move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            $data['image'] = $newFilename;
                        } else {
                            $data['image_err'] = 'Không thể upload ảnh';
                        }
                    } else {
                        $data['image_err'] = 'Kích thước ảnh không được vượt quá 5MB';
                    }
                } else {
                    $data['image_err'] = 'Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)';
                }
            }

            // Kiểm tra lỗi
            if(empty($data['title_err']) && empty($data['content_err']) && empty($data['image_err'])) {
                // Tạo bài viết
                $post_id = $this->forumPostModel->createPost($data);
                
                if($post_id) {
                    // Thêm tags
                    if(!empty($data['selected_tags'])) {
                        foreach($data['selected_tags'] as $tag_id) {
                            $this->forumPostModel->addPostTag($post_id, $tag_id);
                        }
                    }

                    $_SESSION['success_msg'] = 'Tạo bài viết thành công';
                    $this->redirect('forum/show/' . $post_id);
                } else {
                    die('Có lỗi xảy ra');
                }
            } else {
                // Load lại form với lỗi
                $this->create();
            }
        } else {
            $this->redirect('forum/create');
        }
    }

    // Like/Unlike bài viết (AJAX)
    public function toggleLike($post_id) {
        if(!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $hasLiked = $this->forumLikeModel->hasUserLiked($post_id, $user_id);

        if($hasLiked) {
            // Unlike
            $this->forumLikeModel->unlikePost($post_id, $user_id);
            $action = 'unliked';
        } else {
            // Like
            $this->forumLikeModel->likePost($post_id, $user_id);
            $action = 'liked';
        }

        $likeCount = $this->forumLikeModel->getLikeCount($post_id);

        echo json_encode([
            'success' => true,
            'action' => $action,
            'like_count' => $likeCount
        ]);
    }

    // Thêm comment (AJAX)
    public function addComment() {
        if(!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? 0;
            $content = trim($_POST['content'] ?? '');
            $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

            if(empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung']);
                return;
            }

            $data = [
                'post_id' => $post_id,
                'user_id' => $_SESSION['user_id'],
                'parent_id' => $parent_id,
                'content' => $content
            ];

            $comment_id = $this->forumCommentModel->addComment($data);

            if($comment_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Bình luận thành công',
                    'comment_id' => $comment_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
            }
        }
    }

    // Xóa comment
    public function deleteComment($id) {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
            return;
        }

        if($this->forumCommentModel->deleteComment($id, $_SESSION['user_id'])) {
            $_SESSION['success_msg'] = 'Xóa bình luận thành công';
        } else {
            $_SESSION['error_msg'] = 'Không thể xóa bình luận';
        }

        // Redirect về trang trước
        if(isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect('forum');
        }
    }

    // Tìm kiếm bài viết
    public function search() {
        $keyword = trim($_GET['q'] ?? '');
        
        if(empty($keyword)) {
            $this->redirect('forum');
            return;
        }

        $posts = $this->forumPostModel->searchPosts($keyword);
        $popularTags = $this->forumTagModel->getPopularTags(8);

        $data = [
            'posts' => $posts,
            'popular_tags' => $popularTags,
            'keyword' => $keyword
        ];

        $this->view('forum/search', $data);
    }

    // Lọc theo tag
    public function tag($tag_id) {
        $tag = $this->forumTagModel->getTagById($tag_id);
        
        if(!$tag) {
            $_SESSION['error_msg'] = 'Không tìm thấy tag';
            $this->redirect('forum');
            return;
        }

        $posts = $this->forumPostModel->getPostsByTag($tag_id);
        $popularTags = $this->forumTagModel->getPopularTags(8);

        $data = [
            'posts' => $posts,
            'popular_tags' => $popularTags,
            'current_tag' => $tag
        ];

        $this->view('forum/tag', $data);
    }

    // Xóa bài viết
    public function delete($id) {
        if(!$this->isLoggedIn()) {
            $this->redirect('users/login');
            return;
        }

        if($this->forumPostModel->deletePost($id, $_SESSION['user_id'])) {
            $_SESSION['success_msg'] = 'Xóa bài viết thành công';
        } else {
            $_SESSION['error_msg'] = 'Không thể xóa bài viết';
        }

        $this->redirect('forum');
    }
}
?>

