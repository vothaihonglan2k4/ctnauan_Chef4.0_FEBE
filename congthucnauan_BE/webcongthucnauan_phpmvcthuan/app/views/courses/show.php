<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/courses">Khóa học online</a></li>
            <li class="breadcrumb-item active"><?php echo $data['course']->title; ?></li>
        </ol>
    </nav>

    <?php flash('course_message'); ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <img src="<?php echo $data['course']->image ? URL_ROOT . '/public/uploads/courses/' . $data['course']->image : URL_ROOT . '/public/img/congthucnauan.jpg'; ?>" 
                     class="card-img-top" alt="<?php echo $data['course']->title; ?>"
                     style="max-height: 400px; object-fit: cover;">
                <div class="card-body">
                    <h1 class="card-title h2"><?php echo $data['course']->title; ?></h1>
                    
                    <div class="d-flex flex-wrap align-items-center mb-3">
                        <div class="me-4 mb-2">
                            <i class="fas fa-users me-1 text-muted"></i>
                            <span><?php echo $data['student_count']; ?> học viên</span>
                        </div>
                        <div class="me-4 mb-2">
                            <i class="fas fa-film me-1 text-muted"></i>
                            <span><?php echo count($data['lessons']); ?> bài học</span>
                        </div>
                        <div class="me-4 mb-2">
                            <i class="fas fa-clock me-1 text-muted"></i>
                            <span><?php echo $data['total_duration']; ?> phút</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-user me-1 text-muted"></i>
                            <span><?php echo $data['course']->instructor_name; ?></span>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border">
                        <h5 class="alert-heading">Mô tả khóa học</h5>
                        <p><?php echo nl2br($data['course']->description); ?></p>
                    </div>
                    
                    <?php if(!empty($data['course']->requirements)): ?>
                    <div class="mt-4">
                        <h5>Yêu cầu</h5>
                        <p><?php echo nl2br($data['course']->requirements); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($data['course']->what_you_will_learn)): ?>
                    <div class="mt-4">
                        <h5>Bạn sẽ học được gì</h5>
                        <div class="row">
                            <?php 
                            $learn_items = explode("\n", $data['course']->what_you_will_learn);
                            foreach($learn_items as $item): 
                                if(trim($item) !== ''):
                            ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex">
                                    <i class="fas fa-check text-success me-2 mt-1"></i>
                                    <span><?php echo trim($item); ?></span>
                                </div>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="card-title h5 mb-0">Nội dung khóa học</h4>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach($data['lessons'] as $index => $lesson): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary me-2"><?php echo $index + 1; ?></span>
                                    <?php echo $lesson->title; ?>
                                    <?php if($lesson->is_free): ?>
                                        <span class="badge bg-success ms-2">Miễn phí</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-3"><?php echo $lesson->duration_minutes; ?> phút</small>
                                    <?php if($data['is_enrolled'] || $lesson->is_free): ?>
                                        <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>/<?php echo $lesson->id; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-play-circle"></i>
                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-lock text-muted"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title">Thông tin khóa học</h5>
                    
                    <?php if($data['is_enrolled']): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> Bạn đã đăng ký khóa học này
                        </div>
                        
                        <?php if(isset($data['enrollment']->progress)): ?>
                            <div class="mb-3">
                                <label class="form-label">Tiến độ học tập</label>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $data['enrollment']->progress; ?>%;" 
                                         aria-valuenow="<?php echo $data['enrollment']->progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $data['enrollment']->progress; ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-play-circle me-2"></i> Tiếp tục học
                        </a>
                    <?php else: ?>
                        <div class="mb-3">
                            <h3 class="card-text text-primary">
                                <?php if($data['course']->price > 0): ?>
                                    <?php echo number_format($data['course']->price, 0, ',', '.'); ?> đ
                                <?php else: ?>
                                    <span class="text-success">Miễn phí</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        
                        <?php if(isLoggedIn()): ?>
                            <a href="<?php echo URL_ROOT; ?>/courses/enroll/<?php echo $data['course']->id; ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                <?php if($data['course']->price > 0): ?>
                                    <i class="fas fa-shopping-cart me-2"></i> Đăng ký ngay
                                <?php else: ?>
                                    <i class="fas fa-hand-point-right me-2"></i> Đăng ký miễn phí
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo URL_ROOT; ?>/users/login" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập để đăng ký
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="card-text">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fas fa-calendar-alt me-2"></i> Cập nhật</span>
                                <span><?php echo isset($data['course']->updated_at) ? date('d/m/Y', strtotime($data['course']->updated_at)) : date('d/m/Y', strtotime($data['course']->created_at)); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fas fa-globe me-2"></i> Ngôn ngữ</span>
                                <span>Tiếng Việt</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fas fa-info-circle me-2"></i> Trạng thái</span>
                                <span>
                                    <?php if($data['course']->status == 'published'): ?>
                                        <span class="badge bg-success">Đang mở</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Sắp ra mắt</span>
                                    <?php endif; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    
                    <?php if(!$data['is_enrolled'] && count($data['free_lessons']) > 0): ?>
                        <div class="mt-3">
                            <h6>Học thử miễn phí</h6>
                            <ul class="list-group list-group-flush">
                                <?php foreach($data['free_lessons'] as $free_lesson): ?>
                                    <li class="list-group-item px-0">
                                        <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>/<?php echo $free_lesson->id; ?>" class="text-decoration-none">
                                            <i class="fas fa-play-circle me-2 text-primary"></i>
                                            <?php echo $free_lesson->title; ?>
                                            <span class="badge bg-success ms-2">Miễn phí</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?> 