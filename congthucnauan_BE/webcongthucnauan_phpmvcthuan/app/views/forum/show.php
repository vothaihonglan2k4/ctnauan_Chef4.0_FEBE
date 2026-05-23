<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="<?php echo URL_ROOT; ?>/forum" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại diễn đàn
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Post Content Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex align-items-start mb-3">
                        <?php 
                        $avatarPath = !empty($data['post']->author_avatar) && $data['post']->author_avatar != 'default-avatar.png' 
                            ? URL_ROOT . '/public/uploads/avatars/' . $data['post']->author_avatar
                            : URL_ROOT . '/public/img/default-avatar.png';
                        ?>
                        <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        
                        <div class="flex-grow-1">
                            <h4 class="mb-1"><?php echo $data['post']->author_name; ?></h4>
                            <div class="text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($data['post']->created_at)); ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-eye me-1"></i>
                                <?php echo $data['post']->views; ?> lượt xem
                            </div>
                        </div>

                        <!-- Actions (nếu là chủ bài viết) -->
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $data['post']->user_id): ?>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form action="<?php echo URL_ROOT; ?>/forum/delete/<?php echo $data['post']->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>Xóa bài viết
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <!-- Title -->
                    <h2 class="mb-3"><?php echo $data['post']->title; ?></h2>

                    <!-- Tags -->
                    <?php if(!empty($data['tags'])): ?>
                        <div class="mb-3">
                            <?php foreach($data['tags'] as $tag): ?>
                                <a href="<?php echo URL_ROOT; ?>/forum/tag/<?php echo $tag->id; ?>" class="badge bg-primary text-decoration-none me-1">
                                    <?php echo $tag->name; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="post-content mb-4">
                        <?php echo nl2br($data['post']->content); ?>
                    </div>

                    <!-- Image -->
                    <?php if($data['post']->image): ?>
                        <div class="mb-4">
                            <img src="<?php echo URL_ROOT; ?>/public/uploads/forum/<?php echo $data['post']->image; ?>" 
                                 alt="Post image" 
                                 class="img-fluid rounded">
                        </div>
                    <?php endif; ?>

                    <!-- Video -->
                    <?php if($data['post']->video_url): 
                        $video_id = '';
                        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $data['post']->video_url, $id)) {
                            $video_id = $id[1];
                        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $data['post']->video_url, $id)) {
                            $video_id = $id[1];
                        }
                        if($video_id):
                    ?>
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>" 
                                    frameborder="0" 
                                    allowfullscreen
                                    class="rounded">
                            </iframe>
                        </div>
                    <?php endif; endif; ?>

                    <!-- Linked Recipe -->
                    <?php if($data['post']->recipe_id && $data['post']->recipe_title): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-utensils me-2"></i>
                            <strong>Công thức liên quan:</strong>
                            <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $data['post']->recipe_id; ?>" class="alert-link">
                                <?php echo $data['post']->recipe_title; ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <!-- Like & Stats -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-like <?php echo $data['user_liked'] ? 'btn-danger' : 'btn-outline-danger'; ?>" 
                                        data-post-id="<?php echo $data['post']->id; ?>"
                                        onclick="toggleLike(this)">
                                    <i class="fas fa-heart me-1"></i>
                                    <span class="like-count"><?php echo $data['post']->likes_count; ?></span>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo URL_ROOT; ?>/users/login" class="btn btn-outline-danger">
                                    <i class="fas fa-heart me-1"></i>
                                    <?php echo $data['post']->likes_count; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-comment me-1"></i>
                            <?php echo $data['post']->comments_count; ?> bình luận
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Bình luận (<?php echo count($data['comments']); ?>)</h5>
                </div>
                <div class="card-body">
                    <!-- Comment Form -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form id="commentForm" class="mb-4">
                            <input type="hidden" name="post_id" value="<?php echo $data['post']->id; ?>">
                            <div class="mb-3">
                                <textarea class="form-control" name="content" rows="3" placeholder="Viết bình luận của bạn..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Gửi bình luận
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <a href="<?php echo URL_ROOT; ?>/users/login">Đăng nhập</a> để bình luận
                        </div>
                    <?php endif; ?>

                    <hr>

                    <!-- Comments List -->
                    <div id="commentsList">
                        <?php if(!empty($data['comments'])): ?>
                            <?php foreach($data['comments'] as $comment): ?>
                                <?php if(!$comment->parent_id): // Chỉ hiển thị comment cha ?>
                                    <div class="comment-item mb-3 p-3 bg-light rounded">
                                        <div class="d-flex">
                                            <?php 
                                            $commentAvatar = !empty($comment->user_avatar) && $comment->user_avatar != 'default-avatar.png' 
                                                ? URL_ROOT . '/public/uploads/avatars/' . $comment->user_avatar
                                                : URL_ROOT . '/public/img/default-avatar.png';
                                            ?>
                                            <img src="<?php echo $commentAvatar; ?>" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong><?php echo $comment->user_name; ?></strong>
                                                        <small class="text-muted ms-2">
                                                            <?php echo date('d/m/Y H:i', strtotime($comment->created_at)); ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <?php if(isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment->user_id || $_SESSION['user_role'] == 'admin')): ?>
                                                        <form action="<?php echo URL_ROOT; ?>/forum/deleteComment/<?php echo $comment->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Xóa bình luận này?');">
                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="mb-0 mt-1"><?php echo nl2br($comment->content); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-3">
            <!-- Author Info -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Tác giả</h6>
                </div>
                <div class="card-body text-center">
                    <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <h6><?php echo $data['post']->author_name; ?></h6>
                    <small class="text-muted"><?php echo $data['post']->author_email; ?></small>
                </div>
            </div>

            <!-- Share -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Chia sẻ</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fab fa-facebook me-2"></i>Facebook
                        </a>
                        <button class="btn btn-sm btn-secondary" onclick="copyLink()">
                            <i class="fas fa-link me-2"></i>Copy link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Like
function toggleLike(btn) {
    const postId = btn.dataset.postId;
    
    fetch('<?php echo URL_ROOT; ?>/forum/toggleLike/' + postId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const likeCount = btn.querySelector('.like-count');
            likeCount.textContent = data.like_count;
            
            if(data.action === 'liked') {
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
            } else {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
            }
        }
    });
}

// Submit Comment
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo URL_ROOT; ?>/forum/addComment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Bình luận thành công!');
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    });
});

// Copy Link
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Đã copy link!');
    });
}
</script>

<style>
.post-content {
    line-height: 1.8;
    font-size: 1.05rem;
}

.comment-item {
    transition: background-color 0.2s;
}

.comment-item:hover {
    background-color: #f0f0f0 !important;
}
</style>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>

