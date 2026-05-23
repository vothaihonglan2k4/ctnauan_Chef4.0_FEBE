<?php
class Course {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả khóa học
    public function getAllCourses() {
        $this->db->query("SELECT c.*, cl.name as classroom_name, 
                         (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.id) as student_count,
                         u.name as instructor_name
                         FROM courses c
                         LEFT JOIN classrooms cl ON c.classroom_id = cl.id
                         LEFT JOIN users u ON c.user_id = u.id
                         ORDER BY c.created_at DESC");
        return $this->db->resultSet();
    }

    // Lấy thông tin khóa học theo ID
    public function getCourseById($id) {
        $this->db->query("SELECT c.*, cl.name as classroom_name, 
                         u.name as instructor_name
                         FROM courses c
                         LEFT JOIN classrooms cl ON c.classroom_id = cl.id
                         LEFT JOIN users u ON c.user_id = u.id
                         WHERE c.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Thêm khóa học mới
    public function addCourse($data) {
        $this->db->query("INSERT INTO courses (title, description, price, duration, level, classroom_id, user_id, status, image, requirements, what_will_learn, created_at) 
                         VALUES (:title, :description, :price, :duration, :level, :classroom_id, :user_id, :status, :image, :requirements, :what_will_learn, NOW())");
        
        // Bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':level', $data['level']);
        $this->db->bind(':classroom_id', $data['classroom_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':requirements', $data['requirements'] ?? '');
        $this->db->bind(':what_will_learn', $data['what_will_learn'] ?? '');

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Cập nhật khóa học
    public function updateCourse($data) {
        $this->db->query("UPDATE courses SET 
                         title = :title, 
                         description = :description, 
                         price = :price, 
                         duration = :duration, 
                         level = :level, 
                         classroom_id = :classroom_id, 
                         status = :status, 
                         image = :image,
                         requirements = :requirements,
                         what_will_learn = :what_will_learn
                         WHERE id = :id");
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':level', $data['level']);
        $this->db->bind(':classroom_id', $data['classroom_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':requirements', $data['requirements'] ?? '');
        $this->db->bind(':what_will_learn', $data['what_will_learn'] ?? '');

        // Execute
        return $this->db->execute();
    }

    // Xóa khóa học
    public function deleteCourse($id) {
        // Xóa các bài học trước
        $this->db->query("DELETE FROM course_lessons WHERE course_id = :course_id");
        $this->db->bind(':course_id', $id);
        $this->db->execute();
        
        // Xóa các đăng ký khóa học
        $this->db->query("DELETE FROM course_enrollments WHERE course_id = :course_id");
        $this->db->bind(':course_id', $id);
        $this->db->execute();
        
        // Xóa khóa học
        $this->db->query("DELETE FROM courses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Lấy số lượng khóa học
    public function getCoursesCount($status = null) {
        if($status) {
            $this->db->query("SELECT COUNT(*) as count FROM courses WHERE status = :status");
            $this->db->bind(':status', $status);
        } else {
            $this->db->query("SELECT COUNT(*) as count FROM courses");
        }
        $row = $this->db->single();
        return $row->count ?? 0;
    }

    // Lấy các khóa học phổ biến
    public function getPopularCourses($limit = 6) {
        $this->db->query("SELECT c.*, cl.name as classroom_name, 
                         (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.id) as student_count,
                         u.name as instructor_name
                         FROM courses c
                         LEFT JOIN classrooms cl ON c.classroom_id = cl.id
                         LEFT JOIN users u ON c.user_id = u.id
                         WHERE c.status = 'published'
                         ORDER BY student_count DESC, c.created_at DESC
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Lấy các khóa học mới nhất
    public function getNewestCourses($limit = 6) {
        $this->db->query("SELECT c.*, cl.name as classroom_name, 
                         (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.id) as student_count,
                         u.name as instructor_name
                         FROM courses c
                         LEFT JOIN classrooms cl ON c.classroom_id = cl.id
                         LEFT JOIN users u ON c.user_id = u.id
                         WHERE c.status = 'published'
                         ORDER BY c.created_at DESC
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Tìm kiếm khóa học
    public function searchCourses($keyword) {
        $this->db->query("SELECT c.*, cl.name as classroom_name, 
                         (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.id) as student_count,
                         u.name as instructor_name
                         FROM courses c
                         LEFT JOIN classrooms cl ON c.classroom_id = cl.id
                         LEFT JOIN users u ON c.user_id = u.id
                         WHERE c.title LIKE :keyword OR c.description LIKE :keyword
                         AND c.status = 'published'
                         ORDER BY c.created_at DESC");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }

    // Kiểm tra xem người dùng đã đăng ký khóa học chưa
    public function isEnrolled($userId, $courseId) {
        $this->db->query("SELECT * FROM course_enrollments WHERE user_id = :user_id AND course_id = :course_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':course_id', $courseId);
        $row = $this->db->single();
        return !empty($row);
    }

    // Lấy tất cả khóa học đã xuất bản
    public function getPublishedCourses() {
        $this->db->query("SELECT c.*, cl.name as classroom_name, 
                         (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.id) as student_count,
                         u.name as instructor_name
                         FROM courses c
                         LEFT JOIN classrooms cl ON c.classroom_id = cl.id
                         LEFT JOIN users u ON c.user_id = u.id
                         WHERE c.status = 'published'
                         ORDER BY c.created_at DESC");
        return $this->db->resultSet();
    }
    
    // Cập nhật trạng thái khóa học
    public function updateCourseStatus($id, $status) {
        // Validate status
        $allowed_statuses = ['published', 'draft', 'archived'];
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }

        $this->db->query('UPDATE courses SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        return $this->db->execute();
    }
}
?> 