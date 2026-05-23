<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-9">
            <h2 class="mb-4">
                <i class="fas fa-search me-2"></i>
                Kết quả tìm kiếm: "<?php echo htmlspecialchars($data['keyword']); ?>"
            </h2>

            <?php if(!empty($data['posts'])): ?>
                <p class="text-muted">Tìm thấy <?php echo count($data['posts']); ?> kết quả</p>
                
                <?php foreach($data['posts'] as $post): ?>
                    <!-- Giống như index.php -->
                    <div class="card mb-3 forum-post-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="me-3">
                                    <?php 
                                    $avatarPath = !empty($post->author_avatar) && $post->author_avatar != 'default-avatar.png' 
                                        ? URL_ROOT . '/public/uploads/avatars/' . $post->author_avatar
                                        : URL_ROOT . '/public/img/default-avatar.png';
                                    ?>
                                    <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-2">
                                        <a href="<?php echo URL_ROOT; ?>/forum/show/<?php echo $post->id; ?>" class="text-decoration-none text-dark">
                                            <?php echo $post->title; ?>
                                        </a>
                                    </h5>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-user me-1"></i>
                                        <strong><?php echo $post->author_name; ?></strong>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($post->created_at)); ?>
                                    </div>
                                    <p class="mb-2"><?php echo mb_substr(strip_tags($post->content), 0, 200); ?>...</p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-heart text-danger"></i> <?php echo $post->likes_count; ?>
                                        </span>
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-comment text-primary"></i> <?php echo $post->comments_count; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    Không tìm thấy kết quả nào cho "<strong><?php echo htmlspecialchars($data['keyword']); ?></strong>"
                </div>
            <?php endif; ?>
        </div>

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

            <!-- Back to Forum -->
            <div class="card">
                <div class="card-body text-center">
                    <a href="<?php echo URL_ROOT; ?>/forum" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Về diễn đàn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>

