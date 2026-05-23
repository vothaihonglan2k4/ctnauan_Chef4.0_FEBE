<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar chứa danh sách bài học -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo $data['course']->title; ?></h5>
                    <button class="btn btn-sm btn-outline-primary d-lg-none" id="closeSidebar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="list-group list-group-flush overflow-auto" style="max-height: calc(100vh - 180px);">
                    <?php foreach($data['lessons'] as $index => $lesson): ?>
                        <?php 
                        $is_locked = !$data['is_enrolled'] && !$lesson->is_free;
                        $link_url = $is_locked ? '#' : URL_ROOT . '/courses/learn/' . $data['course']->id . '/' . $lesson->id;
                        ?>
                        <a href="<?php echo $link_url; ?>" 
                           class="list-group-item list-group-item-action <?php echo ($lesson->id == $data['current_lesson']->id) ? 'active' : ''; ?> <?php echo $is_locked ? 'disabled' : ''; ?>"
                           <?php echo $is_locked ? 'onclick="return false;" style="cursor: not-allowed; opacity: 0.6;"' : ''; ?>>
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1 text-truncate">
                                    <span class="me-2"><?php echo $index + 1; ?>.</span>
                                    <?php echo $lesson->title; ?>
                                    <?php if($is_locked): ?>
                                        <i class="fas fa-lock text-warning ms-2" title="Cần đăng ký khóa học"></i>
                                    <?php elseif($lesson->is_free): ?>
                                        <span class="badge bg-success ms-1">Miễn phí</span>
                                    <?php endif; ?>
                                    <?php if(in_array($lesson->id, $data['completed_lessons'])): ?>
                                        <i class="fas fa-check-circle text-success ms-2"></i>
                                        <br />
                                        <span class="badge bg-success ms-1">Hoàn thành</span>
                                    <?php endif; ?>
                                </h6>
                                <small><?php echo $lesson->duration_minutes; ?> phút</small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php if(isset($data['enrollment']) && $data['enrollment']): ?>
                    <div class="card-footer bg-white">
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo $data['enrollment']->progress; ?>%;" 
                                 aria-valuenow="<?php echo $data['enrollment']->progress; ?>" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Tiến độ: <?php echo $data['enrollment']->progress; ?>%</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Nội dung bài học -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-outline-primary me-2 d-lg-none" id="showSidebar">
                        <i class="fas fa-list"></i> Danh sách bài học
                    </button>
                    <a href="<?php echo URL_ROOT; ?>/courses/show/<?php echo $data['course']->id; ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Về trang khóa học
                    </a>
                </div>
                <div class="d-flex">
                    <?php 
                    // Tìm bài học trước và sau
                    $prevLesson = null;
                    $nextLesson = null;
                    $foundCurrent = false;
                    
                    foreach($data['lessons'] as $lesson) {
                        if($foundCurrent) {
                            $nextLesson = $lesson;
                            break;
                        }
                        
                        if($lesson->id == $data['current_lesson']->id) {
                            $foundCurrent = true;
                        } else {
                            $prevLesson = $lesson;
                        }
                    }
                    ?>
                    
                    <?php if($prevLesson): ?>
                        <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>/<?php echo $prevLesson->id; ?>" class="btn btn-outline-primary me-2">
                            <i class="fas fa-step-backward me-1"></i> Bài trước
                        </a>
                    <?php endif; ?>
                    
                    <?php if($nextLesson): ?>
                        <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>/<?php echo $nextLesson->id; ?>" class="btn btn-primary">
                            Bài tiếp theo <i class="fas fa-step-forward ms-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title"><?php echo $data['current_lesson']->title; ?></h2>
                    
                    <?php if(!empty($data['current_lesson']->video_url)): ?>
                        <div class="embed-responsive embed-responsive-16by9 mb-4">
                            <div class="ratio ratio-16x9">
                                <?php 
                                $video_url = $data['current_lesson']->video_url;
                                $valid_embed_url = false;
                                $embed_url = '';
                                
                                // Chuyển đổi URL YouTube thông thường thành URL nhúng
                                if(strpos($video_url, 'youtube.com/watch?v=') !== false) {
                                    $video_id = explode('v=', $video_url)[1];
                                    // Loại bỏ các tham số bổ sung sau video_id nếu có
                                    if(strpos($video_id, '&') !== false) {
                                        $video_id = explode('&', $video_id)[0];
                                    }
                                    if(strlen($video_id) > 3 && !preg_match('/^example\d+$/', $video_id)) {
                                        $valid_embed_url = true;
                                        $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                                    }
                                } elseif(strpos($video_url, 'youtube.com/embed/') !== false) {
                                    $valid_embed_url = true;
                                    $embed_url = $video_url;
                                } elseif(strpos($video_url, 'youtu.be/') !== false) {
                                    $video_id = explode('youtu.be/', $video_url)[1];
                                    if(strpos($video_id, '?') !== false) {
                                        $video_id = explode('?', $video_id)[0];
                                    }
                                    if(strlen($video_id) > 3 && !preg_match('/^example\d+$/', $video_id)) {
                                        $valid_embed_url = true;
                                        $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                                    }
                                }
                                
                                if($valid_embed_url):
                                ?>
                                <iframe
                                    src="<?php echo $embed_url; ?>"
                                    title="<?php echo $data['current_lesson']->title; ?>"
                                    allowfullscreen
                                    class="rounded">
                                </iframe>
                                <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Video không khả dụng. URL video có thể không hợp lệ hoặc video đã bị xóa.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif(!empty($data['current_lesson']->image)): ?>
                        <img src="<?php echo URL_ROOT; ?>/public/uploads/lessons/<?php echo $data['current_lesson']->image; ?>" 
                             class="img-fluid rounded mb-4" alt="<?php echo $data['current_lesson']->title; ?>">
                    <?php endif; ?>
                    
                    <div class="lesson-content mt-4">
                        <?php echo $data['current_lesson']->content; ?>
                    </div>
                </div>
                
                <?php if(isset($data['enrollment']) && $data['enrollment']): ?>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-clock me-1"></i> <?php echo $data['current_lesson']->duration_minutes; ?> phút
                        </span>
                        
                        <div>
                            <button id="markAsCompleteBtn" class="btn btn-success" 
                                   data-course-id="<?php echo $data['course']->id; ?>" 
                                   data-lesson-id="<?php echo $data['current_lesson']->id; ?>">
                                <i class="fas fa-check me-1"></i> Đánh dấu hoàn thành
                            </button>
                            
                            <?php if($nextLesson): ?>
                                <a href="<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>/<?php echo $nextLesson->id; ?>" class="btn btn-primary ms-2">
                                    Bài tiếp theo <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar di động -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="lessonSidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"><?php echo $data['course']->title; ?></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <?php foreach($data['lessons'] as $index => $lesson): ?>
                <?php 
                $is_locked = !$data['is_enrolled'] && !$lesson->is_free;
                $link_url = $is_locked ? '#' : URL_ROOT . '/courses/learn/' . $data['course']->id . '/' . $lesson->id;
                ?>
                <a href="<?php echo $link_url; ?>" 
                   class="list-group-item list-group-item-action <?php echo ($lesson->id == $data['current_lesson']->id) ? 'active' : ''; ?> <?php echo $is_locked ? 'disabled' : ''; ?>"
                   <?php echo $is_locked ? 'onclick="return false;" style="cursor: not-allowed; opacity: 0.6;"' : ''; ?>>
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <h6 class="mb-1">
                            <span class="me-2"><?php echo $index + 1; ?>.</span>
                            <?php echo $lesson->title; ?>
                            <?php if($is_locked): ?>
                                <i class="fas fa-lock text-warning ms-2" title="Cần đăng ký khóa học"></i>
                            <?php elseif($lesson->is_free): ?>
                                <span class="badge bg-success ms-1">Miễn phí</span>
                            <?php endif; ?>
                            <?php if(in_array($lesson->id, $data['completed_lessons'])): ?>
                                <i class="fas fa-check-circle text-success ms-2"></i>
                                <span class="badge bg-success ms-1">Hoàn thành</span>
                            <?php endif; ?>
                        </h6>
                        <small><?php echo $lesson->duration_minutes; ?> phút</small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if(isset($data['enrollment']) && $data['enrollment']): ?>
            <div class="p-3">
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?php echo $data['enrollment']->progress; ?>%;" 
                         aria-valuenow="<?php echo $data['enrollment']->progress; ?>" 
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">Tiến độ: <?php echo $data['enrollment']->progress; ?>%</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý hiển thị sidebar trên thiết bị di động
    const showSidebarBtn = document.getElementById('showSidebar');
    const lessonSidebar = new bootstrap.Offcanvas(document.getElementById('lessonSidebar'));
    
    if(showSidebarBtn) {
        showSidebarBtn.addEventListener('click', function() {
            lessonSidebar.show();
        });
    }
    
    // Xử lý đánh dấu hoàn thành bài học
    const markAsCompleteBtn = document.getElementById('markAsCompleteBtn');
    
    if(markAsCompleteBtn) {
        markAsCompleteBtn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const lessonId = this.getAttribute('data-lesson-id');
            
            // Tính toán tiến độ dựa trên số bài học
            const totalLessons = <?php echo count($data['lessons']); ?>;
            
            // Tìm chỉ số bài học hiện tại bằng PHP (an toàn hơn)
            const currentLessonIndex = <?php 
                $current_index = 0;
                foreach($data['lessons'] as $index => $lesson) {
                    if($lesson->id == $data['current_lesson']->id) {
                        $current_index = $index;
                        break;
                    }
                }
                echo $current_index;
            ?>;
            
            // Tính toán tiến độ
            const progress = Math.max(Math.min(Math.round((100 / totalLessons) * (currentLessonIndex + 1)), 100), 0);
            
            // Gửi request cập nhật tiến độ
            // Sử dụng endpoint mới trực tiếp
            const apiUrl = '<?php echo URL_ROOT; ?>/courses/update_progress_direct';
            // console.log('Gửi request API đến endpoint mới: ' + apiUrl);
            // console.log('Dữ liệu gửi đi: courseId=' + courseId + ', lessonId=' + lessonId + ', progress=' + progress);
            
            // Sử dụng dữ liệu FormData để tránh vấn đề encoding
            const formData = new FormData();
            formData.append('course_id', courseId);
            formData.append('lesson_id', lessonId);
            formData.append('progress', progress);
            
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // console.log('API Status Code: ' + response.status);
                if (!response.ok) {
                    throw new Error('Lỗi gọi API: ' + response.status + ' ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // console.log('API Response:', data);
                if(data.success) {
                    // Cập nhật giao diện
                    markAsCompleteBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Đã hoàn thành';
                    markAsCompleteBtn.disabled = true;
                    
                    // Hiển thị thông báo thành công
                    alert('Đã đánh dấu hoàn thành bài học!');
                    
                    // Thêm dấu tích vào sidebar cho bài học hiện tại
                    const sidebarLessonItem = document.querySelector(`.list-group-item[href*="/learn/${courseId}/${lessonId}"]`);
                    if (sidebarLessonItem) {
                        // Thêm icon đã hoàn thành nếu chưa có
                        const icon = document.createElement('i');
                        icon.className = 'fas fa-check-circle text-success ms-2';
                        
                        const titleElement = sidebarLessonItem.querySelector('h6');
                        if (titleElement && !titleElement.querySelector('.fa-check-circle')) {
                            titleElement.appendChild(icon);
                            
                            // Thêm badge "Hoàn thành"
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-success ms-1';
                            badge.textContent = 'Hoàn thành';
                            titleElement.appendChild(badge);
                        }
                    }
                    
                    // Nếu đã hoàn thành 100%, hiển thị thông báo
                    if(data.is_completed) {
                        alert('Chúc mừng! Bạn đã hoàn thành khóa học này.');
                    }
                    
                    // Cập nhật thanh tiến độ
                    const progressBars = document.querySelectorAll('.progress-bar');
                    const progressTexts = document.querySelectorAll('small.text-muted');
                    
                    progressBars.forEach(bar => {
                        bar.style.width = `${progress}%`;
                        bar.setAttribute('aria-valuenow', progress);
                    });
                    
                    progressTexts.forEach(text => {
                        if(text.textContent.includes('Tiến độ:')) {
                            text.textContent = `Tiến độ: ${progress}%`;
                        }
                    });
                    
                    // Nếu có bài tiếp theo, chuyển sang sau 2 giây
                    <?php if($nextLesson): ?>
                    setTimeout(() => {
                        window.location.href = '<?php echo URL_ROOT; ?>/courses/learn/<?php echo $data['course']->id; ?>/<?php echo $nextLesson->id; ?>';
                    }, 2000);
                    <?php endif; ?>
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật tiến độ');
            });
        });
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?> 