<?php
class Classroom {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả phòng học
    public function getAllClassrooms() {
        $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM courses WHERE classroom_id = c.id) as course_count 
                         FROM classrooms c 
                         ORDER BY c.name ASC");
        return $this->db->resultSet();
    }

    // Lấy các phòng học đang hoạt động
    public function getActiveClassrooms() {
        $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM courses WHERE classroom_id = c.id) as course_count 
                         FROM classrooms c 
                         WHERE c.active = 1 
                         ORDER BY c.name ASC");
        return $this->db->resultSet();
    }

    // Lấy thông tin phòng học theo ID
    public function getClassroomById($id) {
        $this->db->query("SELECT * FROM classrooms WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Thêm phòng học mới
    public function addClassroom($data) {
        $this->db->query("INSERT INTO classrooms (name, description, capacity, location, active, created_at) 
                         VALUES (:name, :description, :capacity, :location, :active, NOW())");
        
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':capacity', $data['capacity']);
        $this->db->bind(':location', $data['location'] ?? '');
        $this->db->bind(':active', $data['active'] ?? 1);

        // Execute
        return $this->db->execute();
    }

    // Cập nhật phòng học
    public function updateClassroom($data) {
        $this->db->query("UPDATE classrooms SET 
                         name = :name, 
                         description = :description, 
                         capacity = :capacity, 
                         location = :location, 
                         active = :active 
                         WHERE id = :id");
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':capacity', $data['capacity']);
        $this->db->bind(':location', $data['location'] ?? '');
        $this->db->bind(':active', $data['active'] ?? 1);

        // Execute
        return $this->db->execute();
    }

    // Xóa phòng học
    public function deleteClassroom($id) {
        error_log("CLASSROOM MODEL DEBUG - Delete classroom ID: " . $id);
        
        try {
            // Kiểm tra xem có khóa học đang sử dụng phòng học này không
            $this->db->query("SELECT COUNT(*) as count FROM courses WHERE classroom_id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            $count = $result ? $result->count : 0;
            
            error_log("CLASSROOM MODEL DEBUG - Course count check: " . $count);
            
            if($count > 0) {
                error_log("CLASSROOM MODEL DEBUG - Cannot delete, classroom in use by " . $count . " courses");
                return false; // Không thể xóa vì phòng học đang được sử dụng
            }
            
            error_log("CLASSROOM MODEL DEBUG - Executing delete query");
            $this->db->query("DELETE FROM classrooms WHERE id = :id");
            $this->db->bind(':id', $id);
            $deleteResult = $this->db->execute();
            
            error_log("CLASSROOM MODEL DEBUG - Delete query result: " . ($deleteResult ? "SUCCESS" : "FAILED"));
            return $deleteResult;
            
        } catch (Exception $e) {
            error_log("CLASSROOM MODEL DEBUG - Exception in deleteClassroom: " . $e->getMessage());
            return false;
        }
    }

    // Lấy số lượng phòng học
    public function getClassroomsCount() {
        $this->db->query("SELECT COUNT(*) as count FROM classrooms");
        $row = $this->db->single();
        return $row->count;
    }

    // Kiểm tra sức chứa của phòng học
    public function checkCapacity($id) {
        $this->db->query("SELECT c.capacity, 
                         (SELECT COUNT(*) FROM course_enrollments ce 
                          JOIN courses co ON ce.course_id = co.id 
                          WHERE co.classroom_id = c.id) as enrolled 
                         FROM classrooms c 
                         WHERE c.id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        return [
            'capacity' => $row->capacity,
            'enrolled' => $row->enrolled,
            'available' => $row->capacity - $row->enrolled
        ];
    }

    // Lấy thông tin phòng học theo khóa học
    public function getClassroomsByCourse($course_id) {
        $this->db->query("SELECT c.*, co.title as course_title, 
                         CONCAT('T2, T4, T6: 18:00-20:00') as schedule_time,
                         (SELECT name FROM users WHERE id = co.user_id) as instructor
                         FROM classrooms c
                         JOIN courses co ON co.classroom_id = c.id
                         WHERE co.id = :course_id AND c.active = 1");
        $this->db->bind(':course_id', $course_id);
        return $this->db->resultSet();
    }
}
?> 