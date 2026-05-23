<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-4 py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-3"><?php echo $data['title']; ?></h1>
            <p class="lead text-muted">Nâng cao kỹ năng nấu ăn của bạn với các khóa học trực tuyến từ các đầu bếp chuyên nghiệp</p>
        </div>
        <div class="col-md-4 d-flex align-items-center justify-content-md-end">
            <?php if(isLoggedIn()): ?>
                <a href="<?php echo URL_ROOT; ?>/courses/my_courses" class="btn btn-outline-success me-2">
                    <i class="fas fa-book-reader me-1"></i> Khóa học của tôi
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php flash('course_message'); ?>

    <?php if(empty($data['courses'])): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Hiện tại chưa có khóa học nào được công bố. Vui lòng quay lại sau.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach($data['courses'] as $course): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $course->image ? URL_ROOT . '/public/uploads/courses/' . $course->image : URL_ROOT . '/public/img/congthucnauan.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo $course->title; ?>"
                             style="height: 200px; object-fit: cover;">
                        
                        <?php if($course->price == 0): ?>
                            <div class="badge bg-success position-absolute top-0 end-0 mt-2 me-2">Miễn phí</div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $course->title; ?></h5>
                            <p class="card-text text-truncate"><?php echo $course->description; ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i> <?php echo $course->student_count ?? 0; ?> học viên
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-film me-1"></i> <?php echo $course->lesson_count ?? 0; ?> bài học
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i> <?php echo $course->total_duration ?? 0; ?> phút
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> <?php echo $course->instructor_name; ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if($course->price > 0): ?>
                                    <span class="fw-bold text-primary"><?php echo number_format($course->price, 0, ',', '.'); ?> đ</span>
                                <?php else: ?>
                                    <span class="fw-bold text-success">Miễn phí</span>
                                <?php endif; ?>
                                
                                <div>
                                    <?php if(isset($course->is_enrolled) && $course->is_enrolled): ?>
                                        <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $course->id; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-play-circle me-1"></i> Học ngay
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo URL_ROOT; ?>/courses/show/<?php echo $course->id; ?>" class="btn btn-outline-primary btn-sm">
                                            Xem chi tiết
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?> 