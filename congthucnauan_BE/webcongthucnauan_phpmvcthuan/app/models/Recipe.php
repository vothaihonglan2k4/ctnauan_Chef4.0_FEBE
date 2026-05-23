<?php
class Recipe {
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

    // Get all APPROVED recipes for public view
    public function getApprovedRecipes() {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.status = :status 
                        ORDER BY recipes.created_at DESC');
        $this->db->bind(':status', 'approved');
        $results = $this->db->resultSet();
        return $results;
    }

    // Get recipe by ID - ONLY fetch if approved
    public function getApprovedRecipeById($id) {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.id = :id AND recipes.status = :status');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', 'approved');
        $row = $this->db->single();
        return $row;
    }

    // Get recipe by ID - For admin or specific checks (bypasses status)
    public function getRecipeByIdForAdmin($id) {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }

    // Get ALL recipes regardless of status (for admin purposes)
    public function getAllRecipes() {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        ORDER BY recipes.created_at DESC');
        return $this->db->resultSet();
    }

    // Add recipe with PENDING status
    public function addRecipe($data) {
        $this->db->query('INSERT INTO recipes (title, description, ingredients, instructions, image, video_url, category_id, user_id, status) 
                        VALUES (:title, :description, :ingredients, :instructions, :image, :video_url, :category_id, :user_id, :status)');
        // Bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':ingredients', $data['ingredients']);
        $this->db->bind(':instructions', $data['instructions']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':video_url', $data['video_url']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':status', 'pending'); // Default to pending

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update recipe
    public function updateRecipe($data) {
        $this->db->query('UPDATE recipes SET title = :title, description = :description, ingredients = :ingredients, 
                        instructions = :instructions, image = :image, video_url = :video_url, category_id = :category_id 
                        WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':ingredients', $data['ingredients']);
        $this->db->bind(':instructions', $data['instructions']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':video_url', $data['video_url']);
        $this->db->bind(':category_id', $data['category_id']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete recipe
    public function deleteRecipe($id) {
        $this->db->query('DELETE FROM recipes WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Search APPROVED recipes
    public function searchRecipes($term) {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.status = :status AND (recipes.title LIKE :term OR recipes.description LIKE :term OR recipes.ingredients LIKE :term) 
                        ORDER BY recipes.created_at DESC');
        $this->db->bind(':status', 'approved');
        $this->db->bind(':term', '%' . $term . '%');

        $results = $this->db->resultSet();

        return $results;
    }

    // Advanced search for APPROVED recipes
    public function searchRecipesAdvanced($data) {
        $sql = 'SELECT recipes.*, categories.name as category_name, users.name as author 
                FROM recipes 
                INNER JOIN categories ON recipes.category_id = categories.id 
                INNER JOIN users ON recipes.user_id = users.id 
                WHERE recipes.status = :status'; // Filter by approved status
        
        $params = [':status' => 'approved'];
        
        // Search by term (title)
        if (!empty($data['term'])) {
            $sql .= ' AND (recipes.title LIKE :term)';
            $params[':term'] = '%' . $data['term'] . '%';
        }
        
        // Search by ingredients
        if (!empty($data['ingredients'])) {
            $sql .= ' AND (recipes.ingredients LIKE :ingredients)';
            $params[':ingredients'] = '%' . $data['ingredients'] . '%';
        }
        
        // Filter by category
        if (!empty($data['category'])) {
            $sql .= ' AND recipes.category_id = :category_id';
            $params[':category_id'] = $data['category'];
        }
        
        $sql .= ' ORDER BY recipes.created_at DESC';
        
        $this->db->query($sql);
        
        // Bind all parameters
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        $results = $this->db->resultSet();
        
        return $results;
    }

    // Get APPROVED recipes by category
    public function getRecipesByCategory($category_id) {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.category_id = :category_id AND recipes.status = :status 
                        ORDER BY recipes.created_at DESC');
        $this->db->bind(':category_id', $category_id);
        $this->db->bind(':status', 'approved');

        $results = $this->db->resultSet();

        return $results;
    }

    // Lấy số lượng công thức (có thể cập nhật để đếm theo status)
    public function getRecipesCount($status = null) {
        $sql = "SELECT COUNT(*) as count FROM recipes";
        $params = [];
        if (!is_null($status)) {
            $sql .= " WHERE status = :status";
            $params[':status'] = $status;
        }
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $row = $this->db->single();
        return $row->count;
    }

    // Get recipes pending approval for admin
    public function getPendingRecipes() {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.status = :status 
                        ORDER BY recipes.created_at ASC'); // Show oldest pending first
        $this->db->bind(':status', 'pending');
        $results = $this->db->resultSet();
        return $results;
    }

    // Update recipe status (approve/reject/pending)
    public function updateRecipeStatus($id, $status) {
        // Validate status
        $allowed_statuses = ['approved', 'rejected', 'pending'];
        if (!in_array($status, $allowed_statuses)) {
            return false; // Invalid status
        }

        $this->db->query('UPDATE recipes SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Get recipes by status for manager dashboard
    public function getRecipesByStatus($status) {
        $this->db->query('SELECT recipes.*, categories.name as category_name, users.name as author 
                        FROM recipes 
                        INNER JOIN categories ON recipes.category_id = categories.id 
                        INNER JOIN users ON recipes.user_id = users.id 
                        WHERE recipes.status = :status 
                        ORDER BY recipes.created_at DESC');
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }
}
?>

