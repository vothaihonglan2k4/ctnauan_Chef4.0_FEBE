<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Add forwarding methods to the Database object
    public function query($sql) {
        return $this->db->query($sql);
    }

    public function bind($param, $value, $type = null) {
        return $this->db->bind($param, $value, $type);
    }

    public function execute() {
        return $this->db->execute();
    }

    public function resultSet() {
        return $this->db->resultSet();
    }

    public function single() {
        return $this->db->single();
    }

    public function rowCount() {
        return $this->db->rowCount();
    }

    // Add the missing lastId method
    public function lastId() {
        // This assumes your Database class has a lastInsertId method
        // If it's named differently, adjust accordingly
        return $this->db->lastInsertId();
    }

    // Register user
    public function register($data) {
        $this->db->query('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', 'user');

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        // Bind value
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get User by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        // Bind value
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Update user profile
    public function updateProfile($data) {
        $this->db->query('UPDATE users SET name = :name, email = :email WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update user profile with phone, address, avatar
    public function updateProfileExtended($data) {
        // Kiểm tra xem có cập nhật avatar không
        if(isset($data['avatar']) && !empty($data['avatar'])) {
            $this->db->query('UPDATE users SET name = :name, email = :email, phone = :phone, address = :address, avatar = :avatar WHERE id = :id');
            $this->db->bind(':avatar', $data['avatar']);
        } else {
            $this->db->query('UPDATE users SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id');
        }
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updatePassword($data) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':password', password_hash($data['new_password'], PASSWORD_DEFAULT));

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get all users (for admin)
    public function getAllUsers() {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Lấy số lượng người dùng
    public function getUsersCount() {
        $this->db->query("SELECT COUNT(*) as count FROM users");
        $row = $this->db->single();
        return $row->count;
    }

    // Thêm người dùng mới (cho admin)
    public function addUser($data) {
        $this->db->query('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId(); // Changed from lastId() to lastInsertId()
        } else {
            return false;
        }
    }
    
    // Cập nhật thông tin người dùng (cho admin)
    public function updateUser($data) {
        // Nếu có mật khẩu mới
        if(!empty($data['password'])) {
            $this->db->query('UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id');
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            // Không cập nhật mật khẩu
            $this->db->query('UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id');
        }
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);

        // Execute
        return $this->db->execute();
    }
    
    // Xóa người dùng (cho admin)
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // Get users count by month for dashboard
    public function getUsersCountByMonth($yearMonth) {
        $this->db->query('SELECT COUNT(*) as count FROM users WHERE DATE_FORMAT(created_at, "%Y-%m") = :yearMonth');
        $this->db->bind(':yearMonth', $yearMonth);
        $row = $this->db->single();
        return $row->count ?? 0;
    }
}
?>
