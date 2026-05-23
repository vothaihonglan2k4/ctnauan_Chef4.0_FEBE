<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-3"><?php echo $data['title']; ?></h1>
            <p class="lead text-muted">Lịch học offline cho các khóa học của bạn</p>
        </div>
        <div class="col-md-4 d-flex align-items-center justify-content-md-end">
            <a href="<?php echo URL_ROOT; ?>/courses/my_courses" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại khóa học của tôi
            </a>
        </div>
    </div>

    <?php flash('schedule_message'); ?>

    <?php if(empty($data['schedules'])): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h3>Bạn chưa có lịch học offline nào</h3>
                <p class="lead">Các khóa học của bạn hiện chưa có lịch học offline kèm theo</p>
                <a href="<?php echo URL_ROOT; ?>/courses" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-search me-2"></i> Khám phá thêm khóa học
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="scheduleTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" 
                                        type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                                    Lịch học sắp tới
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" 
                                        type="button" role="tab" aria-controls="all" aria-selected="false">
                                    Tất cả lịch học
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="scheduleTabContent">
                            <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Khóa học</th>
                                                <th>Địa điểm</th>
                                                <th>Thời gian</th>
                                                <th>Giảng viên</th>
                                                <th>Lịch học kế tiếp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data['schedules'] as $schedule): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo URL_ROOT; ?>/courses/show/<?php echo $schedule['course_id']; ?>">
                                                            <?php echo $schedule['course_title']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $schedule['location']; ?></td>
                                                    <td><?php echo $schedule['schedule_time']; ?></td>
                                                    <td><?php echo $schedule['instructor']; ?></td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php echo $schedule['next_class']; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Khóa học</th>
                                                <th>Địa điểm</th>
                                                <th>Lịch học</th>
                                                <th>Giảng viên</th>
                                                <th>Lịch học kế tiếp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data['schedules'] as $schedule): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo URL_ROOT; ?>/courses/show/<?php echo $schedule['course_id']; ?>">
                                                            <?php echo $schedule['course_title']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $schedule['location']; ?></td>
                                                    <td><?php echo $schedule['schedule_time']; ?></td>
                                                    <td><?php echo $schedule['instructor']; ?></td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php echo $schedule['next_class']; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Lưu ý về lịch học</h5>
                    <p>Lịch học có thể thay đổi tùy theo tình hình thực tế. Vui lòng kiểm tra thường xuyên để có thông tin cập nhật nhất.</p>
                    <hr>
                    <p class="mb-0">Nếu bạn không thể tham gia buổi học, vui lòng thông báo trước ít nhất 24 giờ theo hotline: <strong>0123.456.789</strong></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?> 