<?php
class Category {
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

    // Get all categories
    public function getCategories() {
        $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get category by ID
    public function getCategoryById($id) {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Add category
    public function addCategory($name) {
        $this->db->query('INSERT INTO categories (name) VALUES (:name)');
        // Bind values
        $this->db->bind(':name', $name);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update category
    public function updateCategory($id, $name) {
        $this->db->query('UPDATE categories SET name = :name WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete category
    public function deleteCategory($id) {
        $this->db->query('DELETE FROM categories WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Lấy số lượng danh mục
    public function getCategoriesCount() {
        $this->db->query("SELECT COUNT(*) as count FROM categories");
        $row = $this->db->single();
        return $row->count;
    }
}
?>
