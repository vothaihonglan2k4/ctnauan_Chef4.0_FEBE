<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <img src="<?php echo URL_ROOT; ?>/public/uploads/<?php echo $data['recipe']->image; ?>" class="card-img-top recipe-full-img" alt="<?php echo $data['recipe']->title; ?>">
                <div class="card-body">
                    <h1 class="card-title"><?php echo $data['recipe']->title; ?></h1>
                    <div class="mb-3">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <?php if($i <= round($data['avg_rating'])): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php else: ?>
                                <i class="far fa-star text-warning"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span class="ms-1">(<?php echo number_format($data['avg_rating'], 1); ?>/5 - <?php echo count($data['ratings']); ?> đánh giá)</span>
                    </div>
                    
                    <p class="text-muted">
                        <i class="fas fa-user"></i> Đăng bởi: <?php echo $data['recipe']->author; ?> |
                        <i class="fas fa-calendar"></i> Ngày đăng: <?php echo date('d/m/Y', strtotime($data['recipe']->created_at)); ?> |
                        <i class="fas fa-folder"></i> Danh mục: <?php echo $data['recipe']->category_name; ?>
                    </p>
                    
                    <div class="mb-4">
                        <h5>Mô tả</h5>
                        <p><?php echo $data['recipe']->description; ?></p>
                    </div>
                    
                    <?php if(isset($data['recipe']->video_url) && !empty($data['recipe']->video_url)): 
                        // Extract YouTube video ID from URL
                        $video_url = $data['recipe']->video_url;
                        $video_id = '';
                        
                        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video_url, $id)) {
                            $video_id = $id[1];
                        } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $video_url, $id)) {
                            $video_id = $id[1];
                        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $video_url, $id)) {
                            $video_id = $id[1];
                        }
                    ?>
                    <div class="mb-4">
                        <h5><i class="fab fa-youtube text-danger me-2"></i>Video hướng dẫn</h5>
                        <div class="ratio ratio-16x9 mb-3">
                            <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>" 
                                    title="<?php echo $data['recipe']->title; ?>" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    class="rounded">
                            </iframe>
                        </div>
                        
                        <!-- Social Share Buttons -->
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <span class="text-muted me-2"><i class="fas fa-share-alt me-1"></i>Chia sẻ video:</span>
                            
                            <!-- Facebook Share -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($data['recipe']->video_url); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-primary"
                               rel="noopener noreferrer">
                                <i class="fab fa-facebook-f me-1"></i>Facebook
                            </a>
                            
                            <!-- Twitter Share -->
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($data['recipe']->video_url); ?>&text=<?php echo urlencode('Xem video nấu ' . $data['recipe']->title); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-info text-white"
                               rel="noopener noreferrer">
                                <i class="fab fa-twitter me-1"></i>Twitter
                            </a>
                            
                            <!-- WhatsApp Share -->
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode('Xem video nấu ' . $data['recipe']->title . ': ' . $data['recipe']->video_url); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-success"
                               rel="noopener noreferrer">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                            
                            <!-- Telegram Share -->
                            <a href="https://t.me/share/url?url=<?php echo urlencode($data['recipe']->video_url); ?>&text=<?php echo urlencode('Xem video nấu ' . $data['recipe']->title); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-info"
                               rel="noopener noreferrer">
                                <i class="fab fa-telegram-plane me-1"></i>Telegram
                            </a>
                            
                            <!-- Copy Link -->
                            <button type="button" 
                                    class="btn btn-sm btn-secondary"
                                    onclick="copyVideoLink('<?php echo addslashes($data['recipe']->video_url); ?>')">
                                <i class="fas fa-link me-1"></i>Copy Link
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Nguyên liệu</h5>
                                </div>
                                <div class="card-body">
                                    <?php echo nl2br($data['recipe']->ingredients); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Cách làm</h5>
                                </div>
                                <div class="card-body">
                                    <?php echo nl2br($data['recipe']->instructions); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $data['recipe']->user_id || $_SESSION['user_role'] == 'admin')): ?>
                        <div class="mt-3">
                            <a href="<?php echo URL_ROOT; ?>/recipes/edit/<?php echo $data['recipe']->id; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form class="d-inline" action="<?php echo URL_ROOT; ?>/recipes/delete/<?php echo $data['recipe']->id; ?>" method="post">
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa công thức này?');">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Ratings and Reviews -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0">Đánh giá và nhận xét</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['user_id']) && !$data['user_rated']): ?>
                        <form action="<?php echo URL_ROOT; ?>/ratings/add" method="post">
                            <input type="hidden" name="recipe_id" value="<?php echo $data['recipe']->id; ?>">
                            <div class="mb-3">
                                <label class="form-label">Đánh giá của bạn</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating1" value="1">
                                        <label class="form-check-label" for="rating1">1 <i class="far fa-star text-warning"></i></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                                        <label class="form-check-label" for="rating2">2 <i class="far fa-star text-warning"></i></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating3" value="3" checked>
                                        <label class="form-check-label" for="rating3">3 <i class="far fa-star text-warning"></i></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                                        <label class="form-check-label" for="rating4">4 <i class="far fa-star text-warning"></i></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                                        <label class="form-check-label" for="rating5">5 <i class="far fa-star text-warning"></i></label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Nhận xét của bạn</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                        <hr>
                    <?php endif; ?>
                    
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-info">
                            <a href="<?php echo URL_ROOT; ?>/users/login">Đăng nhập</a> để đánh giá công thức này.
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($data['ratings'])): ?>
                        <?php foreach($data['ratings'] as $rating): ?>
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo $rating->user_name; ?></strong>
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= $rating->rating): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($rating->created_at)); ?></small>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo $rating->comment; ?></p>
                                    <?php if(isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $rating->user_id || $_SESSION['user_role'] == 'admin')): ?>
                                        <form action="<?php echo URL_ROOT; ?>/ratings/delete/<?php echo $rating->id; ?>" method="post" class="mt-2 text-end">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Chưa có đánh giá nào cho công thức này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Danh mục</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php 
                    $categoryModel = new Category();
                    $categories = $categoryModel->getCategories();
                    
                    foreach($categories as $category): 
                    ?>
                        <li class="list-group-item">
                            <a href="<?php echo URL_ROOT; ?>/recipes/category/<?php echo $category->id; ?>" class="text-decoration-none">
                                <?php echo $category->name; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Công thức liên quan</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $recipeModel = new Recipe();
                    $recipeModel->query('SELECT recipes.*, categories.name as category_name 
                                    FROM recipes 
                                    INNER JOIN categories ON recipes.category_id = categories.id 
                                    WHERE recipes.category_id = :category_id AND recipes.id != :id 
                                    ORDER BY RAND() LIMIT 3');
                    $recipeModel->bind(':category_id', $data['recipe']->category_id);
                    $recipeModel->bind(':id', $data['recipe']->id);
                    $relatedRecipes = $recipeModel->resultSet();
                    
                    if(!empty($relatedRecipes)):
                        foreach($relatedRecipes as $related):
                    ?>
                        <div class="mb-3">
                            <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $related->id; ?>" class="text-decoration-none">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="<?php echo URL_ROOT; ?>/public/uploads/<?php echo $related->image; ?>" class="img-fluid rounded" alt="<?php echo $related->title; ?>">
                                    </div>
                                    <div class="col-8 ps-2">
                                        <p class="mb-0 fw-bold"><?php echo $related->title; ?></p>
                                        <small class="text-muted"><?php echo $related->category_name; ?></small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <p class="text-muted">Không có công thức liên quan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ratio-16x9 iframe {
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-sm {
    transition: all 0.3s ease;
}

.btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.share-buttons {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.video-thumbnail-overlay {
    position: relative;
    cursor: pointer;
}

.video-thumbnail-overlay:hover::after {
    content: '\f04b';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 4rem;
    color: white;
    text-shadow: 0 0 20px rgba(0,0,0,0.5);
}
</style>

<script>
// Function to copy video link to clipboard
function copyVideoLink(url) {
    // Create temporary input element
    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    
    // Select and copy
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success feedback
        const copyBtn = event.target.closest('button');
        const originalHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Đã copy!';
        copyBtn.classList.remove('btn-secondary');
        copyBtn.classList.add('btn-success');
        
        // Reset button after 2 seconds
        setTimeout(function() {
            copyBtn.innerHTML = originalHTML;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-secondary');
        }, 2000);
    } catch (err) {
        alert('Không thể copy link. Vui lòng copy thủ công: ' + url);
    }
    
    // Remove temporary input
    document.body.removeChild(tempInput);
}

// Add smooth scroll animation for video section
document.addEventListener('DOMContentLoaded', function() {
    const videoSection = document.querySelector('.ratio-16x9');
    if (videoSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeIn 0.6s ease-in';
                }
            });
        }, { threshold: 0.1 });
        
        observer.observe(videoSection);
    }
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
