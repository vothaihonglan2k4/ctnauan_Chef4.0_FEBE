<?php
class Contact {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function addContact($data) {
        try {
            // SQL query to insert contact data
            $query = 'INSERT INTO contacts (name, email, subject, message, status) VALUES(:name, :email, :subject, :message, :status)';
            $this->db->query($query);
            
            // Bind values
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':subject', $data['subject']);
            $this->db->bind(':message', $data['message']);
            $this->db->bind(':status', 'new');

            // Execute query and return result
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getContacts() {
        $this->db->query('SELECT * FROM contacts ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    public function getContactById($id) {
        $this->db->query('SELECT * FROM contacts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateStatus($id, $status) {
        $this->db->query('UPDATE contacts SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }
    
    // Alias for updateStatus to match ManagerController
    public function updateContactStatus($id, $status) {
        return $this->updateStatus($id, $status);
    }
    
    // Get all contacts for manager
    public function getAllContacts() {
        $this->db->query('SELECT * FROM contacts ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    // Get recent contacts for dashboard
    public function getRecentContacts($limit = 5) {
        $this->db->query('SELECT * FROM contacts ORDER BY created_at DESC LIMIT :limit');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
?>
