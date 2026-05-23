<?php
class ForumLike {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Like bài viết
    public function likePost($post_id, $user_id) {
        $this->db->query('INSERT IGNORE INTO forum_likes (post_id, user_id) VALUES (:post_id, :user_id)');
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Unlike bài viết
    public function unlikePost($post_id, $user_id) {
        $this->db->query('DELETE FROM forum_likes WHERE post_id = :post_id AND user_id = :user_id');
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Kiểm tra user đã like bài viết chưa
    public function hasUserLiked($post_id, $user_id) {
        $this->db->query('SELECT * FROM forum_likes WHERE post_id = :post_id AND user_id = :user_id');
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // Đếm số likes của 1 bài viết
    public function getLikeCount($post_id) {
        $this->db->query('SELECT COUNT(*) as total FROM forum_likes WHERE post_id = :post_id');
        $this->db->bind(':post_id', $post_id);
        $row = $this->db->single();
        return $row->total ?? 0;
    }

    // Lấy danh sách users đã like
    public function getUsersWhoLiked($post_id) {
        $this->db->query('SELECT u.id, u.name, u.avatar 
                         FROM forum_likes fl
                         INNER JOIN users u ON fl.user_id = u.id
                         WHERE fl.post_id = :post_id
                         ORDER BY fl.created_at DESC');
        $this->db->bind(':post_id', $post_id);
        return $this->db->resultSet();
    }
}
?>

