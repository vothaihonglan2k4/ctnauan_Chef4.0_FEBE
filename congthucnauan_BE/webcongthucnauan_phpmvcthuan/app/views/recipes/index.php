<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Tất cả công thức nấu ăn</h1>
        </div>
        <div class="col-md-4 text-end">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?php echo URL_ROOT; ?>/recipes/add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm công thức
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Danh mục</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo URL_ROOT; ?>/recipes" class="text-decoration-none">Tất cả công thức</a>
                        </li>
                        <?php foreach($data['categories'] as $category): ?>
                            <li class="list-group-item">
                                <a href="<?php echo URL_ROOT; ?>/recipes/category/<?php echo $category->id; ?>" class="text-decoration-none">
                                    <?php echo $category->name; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
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
                        <div class="alert alert-info">
                            <p class="text-center mb-0">Không tìm thấy công thức nào.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
