<?php
class Rating {
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

    // Add rating
    public function addRating($data) {
        $this->db->query('INSERT INTO ratings (recipe_id, user_id, rating, comment) 
                        VALUES (:recipe_id, :user_id, :rating, :comment)');
        // Bind values
        $this->db->bind(':recipe_id', $data['recipe_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get ratings for recipe
    public function getRatingsForRecipe($recipe_id) {
        $this->db->query('SELECT ratings.*, users.name as user_name 
                        FROM ratings 
                        INNER JOIN users ON ratings.user_id = users.id 
                        WHERE ratings.recipe_id = :recipe_id 
                        ORDER BY ratings.created_at DESC');
        $this->db->bind(':recipe_id', $recipe_id);

        $results = $this->db->resultSet();

        return $results;
    }

    // Get average rating for recipe
    public function getAverageRating($recipe_id) {
        $this->db->query('SELECT AVG(rating) as avg_rating FROM ratings WHERE recipe_id = :recipe_id');
        $this->db->bind(':recipe_id', $recipe_id);

        $row = $this->db->single();

        return $row->avg_rating ? $row->avg_rating : 0;
    }

    // Delete rating
    public function deleteRating($id) {
        $this->db->query('DELETE FROM ratings WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Check if user has already rated a recipe
    public function checkUserRating($recipe_id, $user_id) {
        $this->db->query('SELECT * FROM ratings WHERE recipe_id = :recipe_id AND user_id = :user_id');
        $this->db->bind(':recipe_id', $recipe_id);
        $this->db->bind(':user_id', $user_id);

        $row = $this->db->single();

        // Check row
        return $this->db->rowCount() > 0;
    }

    // --- Methods for Admin Comment Management ---

    // Get rating by ID (for admin checks)
    public function getRatingById($id) {
        $this->db->query('SELECT * FROM ratings WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get all ratings that HAVE a comment
    public function getAllRatingsWithComments($limit = 0, $offset = 0) {
        $sql = 'SELECT r.*, u.name as user_name, rec.title as recipe_title 
                FROM ratings r
                JOIN users u ON r.user_id = u.id 
                JOIN recipes rec ON r.recipe_id = rec.id 
                WHERE r.comment IS NOT NULL AND r.comment != \'\' 
                ORDER BY r.created_at DESC';
        
        if ($limit > 0) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }

        $this->db->query($sql);

        if ($limit > 0) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }

    // Get total count of ratings that HAVE a comment
    public function getTotalRatingsWithComments() {
        $this->db->query('SELECT COUNT(*) as count FROM ratings WHERE comment IS NOT NULL AND comment != \'\'');
        $row = $this->db->single();
        return $row->count ?? 0;
    }
}
?>
