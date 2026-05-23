<?php
class ForumPost {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Add forwarding methods
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

    // Lấy tất cả bài viết (active) với thông tin user, like count, comment count
    public function getAllPosts($limit = 20, $offset = 0) {
        $this->db->query('SELECT 
                fp.*,
                u.name as author_name,
                u.avatar as author_avatar,
                r.title as recipe_title,
                (SELECT COUNT(*) FROM forum_likes WHERE post_id = fp.id) as likes_count,
                (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as comments_count
            FROM forum_posts fp
            INNER JOIN users u ON fp.user_id = u.id
            LEFT JOIN recipes r ON fp.recipe_id = r.id
            WHERE fp.status = :status
            ORDER BY fp.is_pinned DESC, fp.created_at DESC
            LIMIT :limit OFFSET :offset');
        
        $this->db->bind(':status', 'active');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }

    // Lấy bài viết theo ID
    public function getPostById($id) {
        $this->db->query('SELECT 
                fp.*,
                u.name as author_name,
                u.avatar as author_avatar,
                u.email as author_email,
                r.title as recipe_title,
                r.image as recipe_image,
                (SELECT COUNT(*) FROM forum_likes WHERE post_id = fp.id) as likes_count,
                (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as comments_count
            FROM forum_posts fp
            INNER JOIN users u ON fp.user_id = u.id
            LEFT JOIN recipes r ON fp.recipe_id = r.id
            WHERE fp.id = :id AND fp.status = :status');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':status', 'active');
        
        return $this->db->single();
    }

    // Tạo bài viết mới
    public function createPost($data) {
        $this->db->query('INSERT INTO forum_posts (user_id, title, content, image, video_url, recipe_id, status) 
                         VALUES (:user_id, :title, :content, :image, :video_url, :recipe_id, :status)');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':image', $data['image'] ?? null);
        $this->db->bind(':video_url', $data['video_url'] ?? null);
        $this->db->bind(':recipe_id', $data['recipe_id'] ?? null);
        $this->db->bind(':status', 'active');
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Cập nhật bài viết
    public function updatePost($data) {
        $this->db->query('UPDATE forum_posts 
                         SET title = :title, content = :content, image = :image, 
                             video_url = :video_url, recipe_id = :recipe_id, updated_at = NOW()
                         WHERE id = :id AND user_id = :user_id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':image', $data['image'] ?? null);
        $this->db->bind(':video_url', $data['video_url'] ?? null);
        $this->db->bind(':recipe_id', $data['recipe_id'] ?? null);
        
        return $this->db->execute();
    }

    // Xóa bài viết (soft delete)
    public function deletePost($id, $user_id) {
        $this->db->query('UPDATE forum_posts SET status = :status WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':status', 'deleted');
        
        return $this->db->execute();
    }

    // Tăng views
    public function incrementViews($id) {
        $this->db->query('UPDATE forum_posts SET views = views + 1 WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Tìm kiếm bài viết
    public function searchPosts($keyword) {
        $this->db->query('SELECT 
                fp.*,
                u.name as author_name,
                u.avatar as author_avatar,
                (SELECT COUNT(*) FROM forum_likes WHERE post_id = fp.id) as likes_count,
                (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as comments_count
            FROM forum_posts fp
            INNER JOIN users u ON fp.user_id = u.id
            WHERE fp.status = :status 
            AND (fp.title LIKE :keyword OR fp.content LIKE :keyword)
            ORDER BY fp.created_at DESC');
        
        $this->db->bind(':status', 'active');
        $this->db->bind(':keyword', '%' . $keyword . '%');
        
        return $this->db->resultSet();
    }

    // Lấy bài viết theo tag
    public function getPostsByTag($tag_id) {
        $this->db->query('SELECT 
                fp.*,
                u.name as author_name,
                u.avatar as author_avatar,
                (SELECT COUNT(*) FROM forum_likes WHERE post_id = fp.id) as likes_count,
                (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as comments_count
            FROM forum_posts fp
            INNER JOIN users u ON fp.user_id = u.id
            INNER JOIN forum_post_tags fpt ON fp.id = fpt.post_id
            WHERE fp.status = :status AND fpt.tag_id = :tag_id
            ORDER BY fp.created_at DESC');
        
        $this->db->bind(':status', 'active');
        $this->db->bind(':tag_id', $tag_id);
        
        return $this->db->resultSet();
    }

    // Lấy tags của 1 bài viết
    public function getPostTags($post_id) {
        $this->db->query('SELECT ft.* 
                         FROM forum_tags ft
                         INNER JOIN forum_post_tags fpt ON ft.id = fpt.tag_id
                         WHERE fpt.post_id = :post_id');
        $this->db->bind(':post_id', $post_id);
        return $this->db->resultSet();
    }

    // Thêm tag cho bài viết
    public function addPostTag($post_id, $tag_id) {
        $this->db->query('INSERT IGNORE INTO forum_post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)');
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':tag_id', $tag_id);
        return $this->db->execute();
    }

    // Đếm tổng số bài viết
    public function getTotalPosts() {
        $this->db->query('SELECT COUNT(*) as total FROM forum_posts WHERE status = :status');
        $this->db->bind(':status', 'active');
        $row = $this->db->single();
        return $row->total ?? 0;
    }
}
?>

