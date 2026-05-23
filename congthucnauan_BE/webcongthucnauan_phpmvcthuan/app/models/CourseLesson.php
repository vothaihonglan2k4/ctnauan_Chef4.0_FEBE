<?php
class CourseLesson {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả bài học của khóa học
    public function getLessonsByCourseId($course_id) {
        $this->db->query("SELECT * FROM course_lessons 
                         WHERE course_id = :course_id 
                         ORDER BY sort_order ASC");
        
        $this->db->bind(':course_id', $course_id);
        
        return $this->db->resultSet();
    }

    // Lấy thông tin bài học theo ID
    public function getLessonById($id) {
        $this->db->query("SELECT * FROM course_lessons WHERE id = :id");
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Thêm bài học mới
    public function addLesson($data) {
        $this->db->query("INSERT INTO course_lessons (course_id, title, content, video_url, duration_minutes, sort_order, is_free, image, summary) 
                         VALUES (:course_id, :title, :content, :video_url, :duration_minutes, :sort_order, :is_free, :image, :summary)");
        
        // Bind values
        $this->db->bind(':course_id', $data['course_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':video_url', $data['video_url']);
        $this->db->bind(':duration_minutes', $data['duration_minutes']);
        $this->db->bind(':sort_order', $data['sort_order']);
        $this->db->bind(':is_free', $data['is_free'] ?? 0);
        $this->db->bind(':image', $data['image'] ?? 'no-image.jpg');
        $this->db->bind(':summary', $data['summary'] ?? '');

        // Execute
        return $this->db->execute();
    }

    // Cập nhật bài học
    public function updateLesson($data) {
        $this->db->query("UPDATE course_lessons 
                         SET title = :title, content = :content, video_url = :video_url, 
                         duration_minutes = :duration_minutes, sort_order = :sort_order, 
                         is_free = :is_free, image = :image, summary = :summary
                         WHERE id = :id");
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':video_url', $data['video_url']);
        $this->db->bind(':duration_minutes', $data['duration_minutes']);
        $this->db->bind(':sort_order', $data['sort_order']);
        $this->db->bind(':is_free', $data['is_free'] ?? 0);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':summary', $data['summary'] ?? '');

        // Execute
        return $this->db->execute();
    }

    // Xóa bài học
    public function deleteLesson($id) {
        $this->db->query("DELETE FROM course_lessons WHERE id = :id");
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // Lấy số lượng bài học của khóa học
    public function getLessonCountByCourse($course_id) {
        $this->db->query("SELECT COUNT(*) as count FROM course_lessons WHERE course_id = :course_id");
        
        $this->db->bind(':course_id', $course_id);
        
        $row = $this->db->single();
        return $row->count;
    }

    // Lấy tổng thời lượng của khóa học
    public function getTotalDurationByCourse($course_id) {
        $this->db->query("SELECT SUM(duration_minutes) as total_duration FROM course_lessons WHERE course_id = :course_id");
        
        $this->db->bind(':course_id', $course_id);
        
        $row = $this->db->single();
        return $row->total_duration ?? 0;
    }

    // Lấy các bài học miễn phí của khóa học
    public function getFreeLessonsByCourse($course_id) {
        $this->db->query("SELECT * FROM course_lessons 
                         WHERE course_id = :course_id AND is_free = 1
                         ORDER BY sort_order ASC");
        
        $this->db->bind(':course_id', $course_id);
        
        return $this->db->resultSet();
    }

    // Kiểm tra xem bài học có thuộc về khóa học không
    public function isLessonBelongToCourse($lessonId, $courseId) {
        $this->db->query("SELECT * FROM course_lessons WHERE id = :id AND course_id = :course_id");
        $this->db->bind(':id', $lessonId);
        $this->db->bind(':course_id', $courseId);
        $row = $this->db->single();
        return !empty($row);
    }
}
?> 