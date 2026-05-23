<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin">Bảng điều khiển</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/courses">Khóa học</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/lessons/<?php echo $data['course_id']; ?>">Bài học</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa bài học</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <h1>Chỉnh sửa bài học</h1>
            <p class="text-muted">Khóa học: <strong><?php echo $data['course']->title; ?></strong></p>
        </div>
    </div>

    <?php 
    // Hàm flash tạm thời
    function displayFlashMessage($name) {
        if(isset($_SESSION[$name])) {
            $message = $_SESSION[$name];
            $class = isset($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : 'alert alert-success';
            echo '<div class="'.$class.'" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
    
    // Hiển thị flash message
    displayFlashMessage('admin_lesson_message');
    ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-edit"></i> Thông tin bài học
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/admin/edit_lesson/<?php echo $data['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề bài học <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo !empty($data['title_err']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo $data['title']; ?>" required>
                            <div class="invalid-feedback"><?php echo isset($data['title_err']) ? $data['title_err'] : ''; ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung bài học</label>
                            <textarea class="form-control <?php echo !empty($data['content_err']) ? 'is-invalid' : ''; ?>" id="content" name="content" rows="6"><?php echo $data['content']; ?></textarea>
                            <div class="invalid-feedback"><?php echo isset($data['content_err']) ? $data['content_err'] : ''; ?></div>
                            <small class="form-text text-muted">Mô tả chi tiết nội dung bài học</small>
                        </div>

                        <div class="mb-3">
                            <label for="video_url" class="form-label">Link Video YouTube <span class="text-danger">*</span></label>
                            <input type="url" class="form-control <?php echo !empty($data['video_url_err']) ? 'is-invalid' : ''; ?>" id="video_url" name="video_url" value="<?php echo $data['video_url']; ?>" placeholder="https://www.youtube.com/watch?v=..." required>
                            <div class="invalid-feedback"><?php echo isset($data['video_url_err']) ? $data['video_url_err'] : ''; ?></div>
                            <small class="form-text text-muted">Ví dụ: https://www.youtube.com/watch?v=abc123</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Thời lượng (phút) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php echo !empty($data['duration_minutes_err']) ? 'is-invalid' : ''; ?>" id="duration_minutes" name="duration_minutes" value="<?php echo $data['duration_minutes']; ?>" min="1" required>
                                    <div class="invalid-feedback"><?php echo isset($data['duration_minutes_err']) ? $data['duration_minutes_err'] : ''; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Thứ tự bài học <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php echo !empty($data['sort_order_err']) ? 'is-invalid' : ''; ?>" id="sort_order" name="sort_order" value="<?php echo $data['sort_order']; ?>" min="0" required>
                                    <div class="invalid-feedback"><?php echo isset($data['sort_order_err']) ? $data['sort_order_err'] : ''; ?></div>
                                    <small class="form-text text-muted">Số thứ tự hiển thị của bài học</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?php echo URL_ROOT; ?>/admin/lessons/<?php echo $data['course_id']; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật bài học</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-video"></i> Video hiện tại
                </div>
                <div class="card-body">
                    <?php if(!empty($data['video_url'])) : ?>
                        <div class="ratio ratio-16x9">
                            <?php 
                            // Convert YouTube URL to embed format
                            $video_url = $data['video_url'];
                            if(strpos($video_url, 'youtube.com/watch?v=') !== false) {
                                $video_id = substr($video_url, strpos($video_url, 'v=') + 2);
                                // Remove anything after &
                                if(strpos($video_id, '&') !== false) {
                                    $video_id = substr($video_id, 0, strpos($video_id, '&'));
                                }
                                $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                            } elseif(strpos($video_url, 'youtu.be/') !== false) {
                                $video_id = substr($video_url, strpos($video_url, 'youtu.be/') + 9);
                                $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                            } else {
                                $embed_url = $video_url;
                            }
                            ?>
                            <iframe src="<?php echo $embed_url; ?>" allowfullscreen></iframe>
                        </div>
                    <?php else : ?>
                        <p class="text-muted">Chưa có video</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-info-circle"></i> Hướng dẫn
                </div>
                <div class="card-body">
                    <h5>Link Video YouTube</h5>
                    <p>Chỉ hỗ trợ link video từ YouTube. Copy link từ thanh địa chỉ của trình duyệt hoặc nút "Chia sẻ" trên YouTube.</p>
                    
                    <h5>Thời lượng</h5>
                    <p>Nhập thời lượng video tính bằng phút. Ví dụ: video 1 giờ 30 phút = 90 phút.</p>
                    
                    <h5>Thứ tự bài học</h5>
                    <p>Số thứ tự càng nhỏ sẽ hiển thị càng trước.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?>

