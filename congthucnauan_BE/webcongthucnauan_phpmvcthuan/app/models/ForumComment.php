<?php
class ForumComment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả comments của 1 bài viết (kèm thông tin user)
    public function getCommentsByPost($post_id) {
        $this->db->query('SELECT 
                fc.*,
                u.name as user_name,
                u.avatar as user_avatar
            FROM forum_comments fc
            INNER JOIN users u ON fc.user_id = u.id
            WHERE fc.post_id = :post_id
            ORDER BY fc.created_at ASC');
        
        $this->db->bind(':post_id', $post_id);
        return $this->db->resultSet();
    }

    // Thêm comment mới
    public function addComment($data) {
        $this->db->query('INSERT INTO forum_comments (post_id, user_id, parent_id, content) 
                         VALUES (:post_id, :user_id, :parent_id, :content)');
        
        $this->db->bind(':post_id', $data['post_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':content', $data['content']);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Xóa comment
    public function deleteComment($id, $user_id) {
        $this->db->query('DELETE FROM forum_comments WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Đếm số comment của 1 bài viết
    public function getCommentCount($post_id) {
        $this->db->query('SELECT COUNT(*) as total FROM forum_comments WHERE post_id = :post_id');
        $this->db->bind(':post_id', $post_id);
        $row = $this->db->single();
        return $row->total ?? 0;
    }

    // Lấy replies (comment con) của 1 comment
    public function getReplies($parent_id) {
        $this->db->query('SELECT 
                fc.*,
                u.name as user_name,
                u.avatar as user_avatar
            FROM forum_comments fc
            INNER JOIN users u ON fc.user_id = u.id
            WHERE fc.parent_id = :parent_id
            ORDER BY fc.created_at ASC');
        
        $this->db->bind(':parent_id', $parent_id);
        return $this->db->resultSet();
    }
}
?>

