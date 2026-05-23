<?php require_once APPROOT . '/views/manager/includes/header.php'; ?>

<!-- Flash Messages -->
<?php if(isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-utensils text-success me-2"></i>
                Quản Lý Công Thức
            </h1>
            <p class="page-description">Duyệt và quản lý các công thức nấu ăn</p>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>/manager" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Về Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-3">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" data-filter="all">
                            <i class="fas fa-list me-1"></i>
                            Tất cả (<?php echo count($data['recipes'] ?? []); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="pending">
                            <i class="fas fa-clock me-1"></i>
                            Chờ duyệt
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="approved">
                            <i class="fas fa-check me-1"></i>
                            Đã duyệt
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="rejected">
                            <i class="fas fa-times me-1"></i>
                            Đã từ chối
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recipes List -->
<div class="row">
    <?php if(isset($data['recipes']) && !empty($data['recipes'])): ?>
        <?php foreach($data['recipes'] as $recipe): ?>
            <div class="col-lg-4 col-md-6 mb-4 recipe-card" data-status="<?php echo $recipe->status; ?>">
                <div class="card h-100">
                    <!-- Recipe Image -->
                    <div class="position-relative">
                        <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $recipe->image; ?>" 
                             class="card-img-top" style="height: 200px; object-fit: cover;"
                             alt="<?php echo htmlspecialchars($recipe->title); ?>">
                        
                        <!-- Status Badge -->
                        <span class="position-absolute top-0 end-0 m-2 badge bg-<?php 
                            echo $recipe->status == 'approved' ? 'success' : 
                                 ($recipe->status == 'pending' ? 'warning' : 'danger'); 
                        ?>">
                            <?php 
                            echo $recipe->status == 'approved' ? 'Đã duyệt' : 
                                 ($recipe->status == 'pending' ? 'Chờ duyệt' : 'Từ chối'); 
                            ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe->title); ?></h5>
                        <p class="card-text text-muted small">
                            <?php echo substr(htmlspecialchars($recipe->description), 0, 100); ?>...
                        </p>
                        
                        <!-- Recipe Info -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="text-muted small">Tác giả</div>
                                <div class="fw-bold small"><?php echo htmlspecialchars($recipe->author ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Danh mục</div>
                                <div class="fw-bold small"><?php echo htmlspecialchars($recipe->category_name ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Ngày tạo</div>
                                <div class="fw-bold small"><?php echo date('d/m/Y', strtotime($recipe->created_at)); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <a href="<?php echo URLROOT; ?>/recipes/show/<?php echo $recipe->id; ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> Xem
                                </a>
                                <a href="<?php echo URLROOT; ?>/recipes/edit/<?php echo $recipe->id; ?>" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Sửa công thức & video">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </a>
                            </div>
                            
                            <?php if($recipe->status == 'pending'): ?>
                                <div>
                                    <form method="POST" action="<?php echo URLROOT; ?>/manager/approveRecipe/<?php echo $recipe->id; ?>" class="d-inline">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success me-1" 
                                                onclick="return confirm('Bạn có chắc muốn duyệt công thức này?')">
                                            <i class="fas fa-check me-1"></i> Duyệt
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo URLROOT; ?>/manager/approveRecipe/<?php echo $recipe->id; ?>" class="d-inline">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Bạn có chắc muốn từ chối công thức này?')">
                                            <i class="fas fa-times me-1"></i> Từ chối
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-<?php 
                                    echo $recipe->status == 'approved' ? 'success' : 'secondary'; 
                                ?>">
                                    <?php 
                                    echo $recipe->status == 'approved' ? 'Đã xử lý' : 'Đã từ chối'; 
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-utensils fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Chưa có công thức nào</h4>
                    <p class="text-muted">Các công thức sẽ hiển thị tại đây khi có người dùng tạo mới.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterLinks = document.querySelectorAll('[data-filter]');
    const recipeCards = document.querySelectorAll('.recipe-card');
    
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active tab
            filterLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Show/hide cards
            recipeCards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else {
                    const cardStatus = card.getAttribute('data-status');
                    card.style.display = cardStatus === filter ? 'block' : 'none';
                }
            });
        });
    });
});
</script>

<style>
.nav-pills .nav-link {
    color: #6c757d;
    border-radius: 20px;
    margin: 0 5px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link.active,
.nav-pills .nav-link:hover {
    background-color: #198754;
    color: white;
}

.recipe-card {
    transition: all 0.3s ease;
}

.recipe-card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}
</style>

<?php require_once APPROOT . '/views/manager/includes/footer.php'; ?> 