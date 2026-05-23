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
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>
            <i class="fas fa-graduation-cap text-info me-2"></i>
            Quản lý Khóa học
        </h1>
        <p class="page-description">Tạo và quản lý các khóa học trực tuyến</p>
    </div>
    <div>
        <a href="<?php echo URLROOT; ?>/manager" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Về Dashboard
        </a>
        <a href="<?php echo URLROOT; ?>/manager/addCourse" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i> Thêm khóa học mới
        </a>
    </div>
</div>

<!-- Statistics Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-info">
            <div class="card-body text-center">
                <i class="fas fa-graduation-cap fa-2x text-info mb-2"></i>
                <div class="h4 mb-0"><?php echo count($data['courses'] ?? []); ?></div>
                <div class="small text-muted">Tổng khóa học</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <div class="h4 mb-0">
                    <?php 
                    $published = 0;
                    if(isset($data['courses'])) {
                        foreach($data['courses'] as $course) {
                            if($course->status == 'published') $published++;
                        }
                    }
                    echo $published;
                    ?>
                </div>
                <div class="small text-muted">Đang hoạt động</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning">
            <div class="card-body text-center">
                <i class="fas fa-edit fa-2x text-warning mb-2"></i>
                <div class="h4 mb-0">
                    <?php 
                    $draft = 0;
                    if(isset($data['courses'])) {
                        foreach($data['courses'] as $course) {
                            if($course->status == 'draft') $draft++;
                        }
                    }
                    echo $draft;
                    ?>
                </div>
                <div class="small text-muted">Bản nháp</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-primary">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                <div class="h4 mb-0">
                    <?php 
                    $totalStudents = 0;
                    if(isset($data['courses'])) {
                        foreach($data['courses'] as $course) {
                            $totalStudents += $course->student_count ?? 0;
                        }
                    }
                    echo $totalStudents;
                    ?>
                </div>
                <div class="small text-muted">Học viên</div>
            </div>
        </div>
    </div>
</div>

<!-- Courses List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2"></i>
            <span class="fw-bold">Danh sách khóa học</span>
        </div>
        <div>
            <span class="badge bg-info"><?php echo count($data['courses'] ?? []); ?> khóa học</span>
        </div>
    </div>
    <div class="card-body">
        <?php if(isset($data['courses']) && !empty($data['courses'])): ?>
            <div class="row">
                <?php foreach($data['courses'] as $course): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100 course-card">
                            <!-- Course Image -->
                            <div class="position-relative">
                                <img src="<?php echo URLROOT; ?>/public/uploads/courses/<?php echo $course->image; ?>" 
                                     class="card-img-top" style="height: 180px; object-fit: cover;"
                                     alt="<?php echo htmlspecialchars($course->title); ?>">
                                
                                <!-- Status Badge -->
                                <span class="position-absolute top-0 end-0 m-2 badge bg-<?php 
                                    echo $course->status == 'published' ? 'success' : 
                                         ($course->status == 'draft' ? 'warning' : 'secondary'); 
                                ?>">
                                    <?php 
                                    echo $course->status == 'published' ? 'Hoạt động' : 
                                         ($course->status == 'draft' ? 'Nháp' : 'Lưu trữ'); 
                                    ?>
                                </span>
                                
                                <!-- Level Badge -->
                                <span class="position-absolute top-0 start-0 m-2 badge bg-dark">
                                    <?php 
                                    echo $course->level == 'beginner' ? 'Cơ bản' : 
                                         ($course->level == 'intermediate' ? 'Trung cấp' : 'Nâng cao'); 
                                    ?>
                                </span>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($course->title); ?></h5>
                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo substr(htmlspecialchars($course->description), 0, 100); ?>...
                                </p>
                                
                                <!-- Course Stats -->
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="text-muted small">Giá</div>
                                        <div class="fw-bold text-success">
                                            <?php echo number_format($course->price, 0, ',', '.'); ?>₫
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Học viên</div>
                                        <div class="fw-bold text-primary">
                                            <?php echo $course->student_count ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Bài học</div>
                                        <div class="fw-bold text-info">
                                            <?php echo $course->lesson_count ?? 0; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Course Info -->
                                <div class="small text-muted mb-3">
                                    <div><i class="fas fa-clock me-1"></i> <?php echo $course->duration; ?> phút</div>
                                    <div><i class="fas fa-chalkboard me-1"></i> <?php echo htmlspecialchars($course->classroom_name ?? 'N/A'); ?></div>
                                    <div><i class="fas fa-calendar me-1"></i> <?php echo date('d/m/Y', strtotime($course->created_at)); ?></div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="<?php echo URLROOT; ?>/courses/show/<?php echo $course->id; ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/admin/courses/edit/<?php echo $course->id; ?>" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                    </div>
                                    
                                    <!-- Status Toggle -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-warning dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if($course->status != 'published'): ?>
                                                <li>
                                                    <form method="POST" action="<?php echo URLROOT; ?>/manager/updateCourseStatus/<?php echo $course->id; ?>" class="d-inline">
                                                        <input type="hidden" name="status" value="published">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-play me-2"></i>Xuất bản
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if($course->status != 'draft'): ?>
                                                <li>
                                                    <form method="POST" action="<?php echo URLROOT; ?>/manager/updateCourseStatus/<?php echo $course->id; ?>" class="d-inline">
                                                        <input type="hidden" name="status" value="draft">
                                                        <button type="submit" class="dropdown-item text-warning">
                                                            <i class="fas fa-edit me-2"></i>Chuyển nháp
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if($course->status != 'archived'): ?>
                                                <li>
                                                    <form method="POST" action="<?php echo URLROOT; ?>/manager/updateCourseStatus/<?php echo $course->id; ?>" class="d-inline">
                                                        <input type="hidden" name="status" value="archived">
                                                        <button type="submit" class="dropdown-item text-secondary"
                                                                onclick="return confirm('Bạn có chắc muốn lưu trữ khóa học này?')">
                                                            <i class="fas fa-archive me-2"></i>Lưu trữ
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle fs-4 me-3"></i>
                <div>
                    <h5 class="alert-heading">Chưa có khóa học nào!</h5>
                    <p class="mb-0">Hãy tạo khóa học đầu tiên của bạn để bắt đầu.</p>
                    <hr>
                    <a href="<?php echo URLROOT; ?>/manager/addCourse" class="btn btn-info btn-sm">
                        <i class="fas fa-plus me-1"></i>Tạo khóa học ngay
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.course-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.1);
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.course-card .card-img-top {
    transition: transform 0.3s ease;
}

.course-card:hover .card-img-top {
    transform: scale(1.05);
}

.badge {
    font-size: 0.7em;
}

.dropdown-menu {
    min-width: 150px;
}

.dropdown-item {
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: rgba(0,0,0,0.05);
}
</style>

<?php require_once APPROOT . '/views/manager/includes/footer.php'; ?> 