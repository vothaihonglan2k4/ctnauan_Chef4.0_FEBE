<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Kết quả tìm kiếm: "<?php echo $data['term']; ?>"</h1>
        </div>
        <div class="col-md-4">
            <form class="d-flex" action="<?php echo URL_ROOT; ?>/recipes/search" method="GET">
                <input class="form-control me-2" name="term" type="search" placeholder="Tìm công thức" value="<?php echo $data['term']; ?>" required>
                <button class="btn btn-primary" type="submit">Tìm</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bộ lọc tìm kiếm</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/recipes/search" method="GET">
                        <?php if(!empty($data['term'])): ?>
                            <input type="hidden" name="term" value="<?php echo $data['term']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="ingredients" class="form-label fw-bold">Tìm theo nguyên liệu</label>
                            <input type="text" class="form-control" id="ingredients" name="ingredients" 
                                value="<?php echo isset($data['ingredients']) ? $data['ingredients'] : ''; ?>" 
                                placeholder="Nhập nguyên liệu (VD: thịt bò, hành...)">
                            <div class="form-text">Nhập các nguyên liệu bạn muốn tìm</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Tìm theo danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">-- Tất cả danh mục --</option>
                                <?php foreach($data['categories'] as $category): ?>
                                    <option value="<?php echo $category->id; ?>" 
                                        <?php echo (isset($data['category']) && $data['category'] == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo $category->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Lọc kết quả</button>
                            <?php if(!empty($data['ingredients']) || !empty($data['category'])): ?>
                                <a href="<?php echo URL_ROOT; ?>/recipes/search?term=<?php echo $data['term']; ?>" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
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
            <?php if(!empty($data['recipes'])): ?>
                <p>Tìm thấy <?php echo count($data['recipes']); ?> công thức phù hợp.</p>
                <div class="row row-cols-1 row-cols-md-3 g-4">
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
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <h4 class="alert-heading">Không tìm thấy kết quả!</h4>
                    <p>Không tìm thấy công thức phù hợp với từ khóa "<?php echo $data['term']; ?>".</p>
                    <hr>
                    <p class="mb-0">Gợi ý:</p>
                    <ul>
                        <li>Kiểm tra chính tả của từ khóa</li>
                        <li>Thử sử dụng từ khóa khác</li>
                        <li>Sử dụng từ khóa ngắn hơn</li>
                        <li>Tìm kiếm theo danh mục</li>
                    </ul>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h4>Có thể bạn quan tâm</h4>
                    </div>
                </div>
                
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php 
                    $recipeModel = new Recipe();
                    $recipeModel->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                                      FROM recipes 
                                      INNER JOIN categories ON recipes.category_id = categories.id 
                                      INNER JOIN users ON recipes.user_id = users.id 
                                      ORDER BY RAND() LIMIT 3');
                    $randomRecipes = $recipeModel->resultSet();
                    
                    foreach($randomRecipes as $recipe): 
                    ?>
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
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
