<?php
class CourseEnrollment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả đăng ký khóa học
    public function getAllEnrollments() {
        $this->db->query("SELECT ce.*, c.title as course_title, u.name as user_name, u.email as user_email 
                         FROM course_enrollments ce
                         JOIN courses c ON ce.course_id = c.id
                         JOIN users u ON ce.user_id = u.id
                         ORDER BY ce.enrollment_date DESC");
        return $this->db->resultSet();
    }

    // Lấy đăng ký theo ID
    public function getEnrollmentById($id) {
        $this->db->query("SELECT ce.*, c.title as course_title, u.name as user_name, u.email as user_email 
                         FROM course_enrollments ce
                         JOIN courses c ON ce.course_id = c.id
                         JOIN users u ON ce.user_id = u.id
                         WHERE ce.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Lấy tất cả đăng ký của người dùng
    public function getEnrollmentsByUserId($user_id) {
        $this->db->query("SELECT e.*, c.title, c.image, c.price,
                         (SELECT COUNT(*) FROM course_lessons WHERE course_id = e.course_id) as lesson_count
                         FROM course_enrollments e
                         JOIN courses c ON e.course_id = c.id
                         WHERE e.user_id = :user_id
                         ORDER BY e.enrollment_date DESC");
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }

    // Lấy tất cả đăng ký của khóa học
    public function getEnrollmentsByCourseId($course_id) {
        $this->db->query("SELECT e.*, u.name, u.email
                         FROM course_enrollments e
                         JOIN users u ON e.user_id = u.id
                         WHERE e.course_id = :course_id
                         ORDER BY e.enrollment_date DESC");
        
        $this->db->bind(':course_id', $course_id);
        
        return $this->db->resultSet();
    }

    // Thêm đăng ký mới
    public function addEnrollment($data) {
        $this->db->query("INSERT INTO course_enrollments 
                         (user_id, course_id, payment_id, enrollment_date, status, progress) 
                         VALUES (:user_id, :course_id, :payment_id, NOW(), :status, :progress)");
        
        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':course_id', $data['course_id']);
        $this->db->bind(':payment_id', $data['payment_id'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':progress', $data['progress'] ?? 0);

        // Execute
        return $this->db->execute();
    }

    // Cập nhật đăng ký
    public function updateEnrollment($data) {
        $this->db->query("UPDATE course_enrollments SET 
                         status = :status, 
                         completion_date = :completion_date, 
                         progress = :progress
                         WHERE id = :id");
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':completion_date', $data['completion_date'] ?? null);
        $this->db->bind(':progress', $data['progress'] ?? 0);

        // Execute
        return $this->db->execute();
    }

    // Xóa đăng ký
    public function deleteEnrollment($user_id, $course_id) {
        $this->db->query("DELETE FROM course_enrollments 
                         WHERE user_id = :user_id AND course_id = :course_id");
        
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':course_id', $course_id);
        
        return $this->db->execute();
    }

    // Kiểm tra người dùng đã đăng ký khóa học chưa
    public function checkEnrollment($user_id, $course_id) {
        $this->db->query("SELECT * FROM course_enrollments 
                         WHERE user_id = :user_id AND course_id = :course_id");
        
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':course_id', $course_id);
        
        $row = $this->db->single();
        
        return !empty($row);
    }

    // Lấy số lượng đăng ký
    public function getEnrollmentsCount() {
        $this->db->query("SELECT COUNT(*) as count FROM course_enrollments");
        $row = $this->db->single();
        return $row->count;
    }

    // Lấy số lượng đăng ký của khóa học
    public function getEnrollmentCountByCourse($course_id) {
        $this->db->query("SELECT COUNT(*) as count FROM course_enrollments 
                         WHERE course_id = :course_id");
        
        $this->db->bind(':course_id', $course_id);
        
        $row = $this->db->single();
        return $row->count;
    }

    // Lấy doanh thu từ đăng ký khóa học
    public function getTotalRevenue() {
        $this->db->query("SELECT SUM(p.amount) as total 
                         FROM course_enrollments ce
                         JOIN payments p ON ce.payment_id = p.id
                         WHERE p.status = 'completed'");
        $row = $this->db->single();
        return $row->total ?? 0;
    }

    // Cập nhật tiến độ học
    public function updateProgress($user_id, $course_id, $progress) {
        try {
            $isCompleted = false;
            
            // Đảm bảo progress là một số hợp lệ
            $progress = intval($progress);
            if($progress < 0) $progress = 0;
            if($progress > 100) $progress = 100;
            
            // Ghi log câu truy vấn để debug
            $query = "UPDATE course_enrollments SET progress = {$progress}";
            if ($progress >= 100) {
                $query .= ", status = 'completed', completion_date = NOW()";
                $isCompleted = true;
            }
            $query .= " WHERE user_id = {$user_id} AND course_id = {$course_id}";
            error_log("SQL Query: " . $query);
            
            // Thực hiện truy vấn
            if ($progress >= 100) {
                $this->db->query("UPDATE course_enrollments 
                                SET progress = :progress, status = 'completed', completion_date = NOW()
                                WHERE user_id = :user_id AND course_id = :course_id");
            } else {
                $this->db->query("UPDATE course_enrollments 
                                SET progress = :progress
                                WHERE user_id = :user_id AND course_id = :course_id");
            }
            
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':course_id', $course_id);
            $this->db->bind(':progress', $progress);
            
            $result = $this->db->execute();
            
            if($result) {
                // Kiểm tra xem có bản ghi nào bị ảnh hưởng không
                if($this->db->rowCount() > 0) {
                    error_log("Cập nhật tiến độ thành công: affected rows = " . $this->db->rowCount());
                    return $isCompleted;
                } else {
                    error_log("Không có bản ghi nào được cập nhật");
                    // Kiểm tra xem bản ghi tồn tại không
                    $this->db->query("SELECT * FROM course_enrollments WHERE user_id = :user_id AND course_id = :course_id");
                    $this->db->bind(':user_id', $user_id);
                    $this->db->bind(':course_id', $course_id);
                    $enrollment = $this->db->single();
                    
                    if($enrollment) {
                        error_log("Bản ghi tồn tại nhưng không cập nhật được: " . print_r($enrollment, true));
                        return $isCompleted; // Vẫn trả về thành công vì bản ghi tồn tại
                    } else {
                        error_log("Không tìm thấy bản ghi");
                        return false;
                    }
                }
            }
            
            // Ghi log lỗi nếu không thành công
            error_log("Lỗi cập nhật tiến độ: " . $this->db->getError());
            return false;
        } catch (Exception $e) {
            // Ghi log lỗi ngoại lệ
            error_log("Ngoại lệ khi cập nhật tiến độ: " . $e->getMessage());
            return false;
        }
    }

    // Lấy thông tin đăng ký cụ thể
    public function getEnrollment($user_id, $course_id) {
        $this->db->query("SELECT * FROM course_enrollments 
                         WHERE user_id = :user_id AND course_id = :course_id");
        
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':course_id', $course_id);
        
        return $this->db->single();
    }

    // Ghi danh khóa học
    public function enrollCourse($user_id, $course_id, $payment_id = null) {
        return $this->addEnrollment([
            'user_id' => $user_id,
            'course_id' => $course_id,
            'payment_id' => $payment_id,
            'status' => 'active'
        ]);
    }

    // Lấy danh sách khóa học đã ghi danh của người dùng
    public function getUserEnrollments($user_id) {
        return $this->getEnrollmentsByUserId($user_id);
    }
    
    // Get total students for dashboard
    public function getTotalStudents() {
        $this->db->query('SELECT COUNT(DISTINCT user_id) as total FROM course_enrollments WHERE status = "active"');
        $row = $this->db->single();
        return $row->total ?? 0;
    }
}
?> 