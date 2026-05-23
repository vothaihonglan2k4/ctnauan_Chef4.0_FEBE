<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-comments me-2"></i>Diễn đàn chia sẻ công thức nấu ăn</h1>
            <p class="text-muted">Nơi cộng đồng chia sẻ kinh nghiệm, bí quyết nấu ăn</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?php echo URL_ROOT; ?>/forum/create" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>Tạo bài viết mới
                </a>
            <?php else: ?>
                <a href="<?php echo URL_ROOT; ?>/users/login" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để đăng bài
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content - Danh sách bài viết -->
        <div class="col-md-9">
            <!-- Search Box -->
            <div class="card mb-3">
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/forum/search" method="GET" class="d-flex">
                        <input type="text" name="q" class="form-control me-2" placeholder="Tìm kiếm bài viết..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danh sách bài viết -->
            <?php if(!empty($data['posts'])): ?>
                <?php foreach($data['posts'] as $post): ?>
                    <div class="card mb-3 forum-post-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <!-- Avatar -->
                                <div class="me-3">
                                    <?php 
                                    $avatarPath = !empty($post->author_avatar) && $post->author_avatar != 'default-avatar.png' 
                                        ? URL_ROOT . '/public/uploads/avatars/' . $post->author_avatar
                                        : URL_ROOT . '/public/img/default-avatar.png';
                                    ?>
                                    <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">
                                    <!-- Title -->
                                    <h5 class="mb-2">
                                        <?php if($post->is_pinned): ?>
                                            <span class="badge bg-danger me-2">📌 Ghim</span>
                                        <?php endif; ?>
                                        <a href="<?php echo URL_ROOT; ?>/forum/show/<?php echo $post->id; ?>" class="text-decoration-none text-dark">
                                            <?php echo $post->title; ?>
                                        </a>
                                    </h5>

                                    <!-- Author & Time -->
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-user me-1"></i>
                                        <strong><?php echo $post->author_name; ?></strong>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($post->created_at)); ?>
                                    </div>

                                    <!-- Excerpt -->
                                    <p class="mb-2"><?php echo mb_substr(strip_tags($post->content), 0, 200); ?>...</p>

                                    <!-- Stats -->
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-heart text-danger"></i> <?php echo $post->likes_count; ?>
                                        </span>
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-comment text-primary"></i> <?php echo $post->comments_count; ?>
                                        </span>
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-eye text-secondary"></i> <?php echo $post->views; ?>
                                        </span>
                                        
                                        <?php if($post->recipe_id): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-utensils"></i> Có công thức
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Thumbnail (nếu có) -->
                                <?php if($post->image): ?>
                                    <div class="ms-3">
                                        <img src="<?php echo URL_ROOT; ?>/public/uploads/forum/<?php echo $post->image; ?>" 
                                             alt="Thumbnail" 
                                             class="rounded" 
                                             style="width: 120px; height: 80px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if($data['total_pages'] > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for($i = 1; $i <= $data['total_pages']; $i++): ?>
                                <li class="page-item <?php echo ($i == $data['current_page']) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo URL_ROOT; ?>/forum?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Chưa có bài viết nào. Hãy là người đầu tiên chia sẻ!
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-3">
            <!-- Popular Tags -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Tags phổ biến</h6>
                </div>
                <div class="card-body">
                    <?php if(!empty($data['popular_tags'])): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($data['popular_tags'] as $tag): ?>
                                <a href="<?php echo URL_ROOT; ?>/forum/tag/<?php echo $tag->id; ?>" 
                                   class="badge bg-primary text-decoration-none">
                                    <?php echo $tag->name; ?>
                                    <?php if(isset($tag->post_count)): ?>
                                        <span class="badge bg-light text-dark"><?php echo $tag->post_count; ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Chưa có tag nào</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Rules/Guidelines -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quy tắc diễn đàn</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Tôn trọng các thành viên khác</li>
                        <li>Không spam hoặc quảng cáo</li>
                        <li>Chia sẻ nội dung hữu ích</li>
                        <li>Sử dụng ngôn ngữ lịch sự</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.forum-post-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.forum-post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.badge {
    font-weight: normal;
}

.gap-2 {
    gap: 0.5rem;
}
</style>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>

