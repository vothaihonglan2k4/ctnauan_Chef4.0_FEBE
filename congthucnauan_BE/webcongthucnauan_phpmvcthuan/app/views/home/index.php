<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="jumbotron p-5 mb-4 bg-light rounded-3">
        <div class="container">
            <h1 class="display-4">Khám Phá Công Thức Nấu Ăn</h1>
            <p class="lead">Tìm kiếm, chia sẻ, và đánh giá các công thức nấu ăn tuyệt vời từ khắp nơi trên thế giới.</p>
            <a href="<?php echo URL_ROOT; ?>/recipes" class="btn btn-primary btn-lg">Xem tất cả công thức</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="<?php echo URL_ROOT; ?>/users/register" class="btn btn-outline-primary btn-lg ms-2">Đăng ký ngay</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2>Công Thức Nổi Bật</h2>
                <hr>
            </div>
        </div>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if(!empty($data['recipes'])): ?>
                <?php foreach($data['recipes'] as $recipe): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?php echo URL_ROOT; ?>/public/uploads/<?php echo $recipe->image; ?>" class="card-img-top recipe-thumbnail" alt="<?php echo $recipe->title; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $recipe->title; ?></h5>
                                <p class="card-text"><?php echo mb_substr($recipe->description, 0, 100); ?>...</p>
                                <p class="text-muted">
                                    <small>
                                        <i class="fas fa-user"></i> <?php echo $recipe->author; ?> |
                                        <i class="fas fa-folder"></i> <?php echo $recipe->category_name; ?>
                                    </small>
                                </p>
                            </div>
                            <div class="card-footer">
                                <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $recipe->id; ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">Chưa có công thức nào.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col-12 text-center">
                <h3>Tại sao chọn Công Thức Nấu Ăn?</h3>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-search fa-3x mb-3 text-primary"></i>
                    <h4>Dễ Dàng Tìm Kiếm</h4>
                    <p>Tìm công thức theo từ khóa, nguyên liệu hoặc danh mục một cách nhanh chóng.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h4>Cộng Đồng Đánh Giá</h4>
                    <p>Xem đánh giá và nhận xét từ người dùng khác để chọn công thức phù hợp.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-share-alt fa-3x mb-3 text-primary"></i>
                    <h4>Chia Sẻ Công Thức</h4>
                    <p>Chia sẻ công thức nấu ăn yêu thích của bạn với cộng đồng.</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-5 mb-4">
            <div class="col-12 text-center">
                <p class="lead mb-4">Hãy tham gia cộng đồng của chúng tôi ngay hôm nay!</p>
                <a href="<?php echo URL_ROOT; ?>/users/register" class="btn btn-primary btn-lg">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
