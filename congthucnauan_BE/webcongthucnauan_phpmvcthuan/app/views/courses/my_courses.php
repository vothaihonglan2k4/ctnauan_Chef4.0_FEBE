<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-3"><?php echo $data['title']; ?></h1>
            <p class="lead text-muted">Danh sách các khóa học bạn đã đăng ký</p>
        </div>
        <div class="col-md-4 d-flex align-items-center justify-content-md-end">
            <a href="<?php echo URL_ROOT; ?>/courses/schedule" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-alt me-1"></i> Lịch học offline
            </a>
            <a href="<?php echo URL_ROOT; ?>/courses" class="btn btn-outline-success">
                <i class="fas fa-graduation-cap me-1"></i> Khám phá khóa học
            </a>
        </div>
    </div>

    <?php flash('course_message'); ?>

    <?php if(empty($data['enrollments'])): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-book-reader fa-4x text-muted mb-3"></i>
                <h3>Bạn chưa đăng ký khóa học nào</h3>
                <p class="lead">Hãy khám phá các khóa học hấp dẫn của chúng tôi để nâng cao kỹ năng nấu ăn</p>
                <a href="<?php echo URL_ROOT; ?>/courses" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-search me-2"></i> Tìm khóa học ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach($data['enrollments'] as $enrollment): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="<?php echo $enrollment->image ? URL_ROOT . '/public/uploads/courses/' . $enrollment->image : URL_ROOT . '/public/img/congthucnauan.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo $enrollment->title; ?>"
                                 style="height: 160px; object-fit: cover;">
                            
                            <div class="position-absolute top-0 end-0 mt-2 me-2">
                                <?php if($enrollment->status == 'completed'): ?>
                                    <span class="badge bg-success">Đã hoàn thành</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Đang học</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $enrollment->title; ?></h5>
                            
                            <div class="progress mt-3 mb-2" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?php echo $enrollment->progress; ?>%;" 
                                     aria-valuenow="<?php echo $enrollment->progress; ?>" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <small class="text-muted">Tiến độ: <?php echo $enrollment->progress; ?>%</small>
                                <small class="text-muted"><?php echo $enrollment->lesson_count; ?> bài học</small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i> Đăng ký: <?php echo date('d/m/Y', strtotime($enrollment->enrollment_date)); ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $enrollment->course_id; ?>" class="btn btn-primary w-100">
                                <?php if($enrollment->progress > 0): ?>
                                    <i class="fas fa-play-circle me-1"></i> Tiếp tục học
                                <?php else: ?>
                                    <i class="fas fa-play-circle me-1"></i> Bắt đầu học
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($data['enrollments']) > 6): ?>
            <div class="text-center mt-4">
                <p class="mb-2">Hiển thị <?php echo count($data['enrollments']); ?> khóa học</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?> 