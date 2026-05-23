<?php
class ForumTag {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả tags
    public function getAllTags() {
        $this->db->query('SELECT * FROM forum_tags ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Lấy tag theo ID
    public function getTagById($id) {
        $this->db->query('SELECT * FROM forum_tags WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Lấy tag theo slug
    public function getTagBySlug($slug) {
        $this->db->query('SELECT * FROM forum_tags WHERE slug = :slug');
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    // Tạo tag mới
    public function createTag($name, $slug) {
        $this->db->query('INSERT INTO forum_tags (name, slug) VALUES (:name, :slug)');
        $this->db->bind(':name', $name);
        $this->db->bind(':slug', $slug);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Tạo slug từ tên (helper function)
    public function createSlug($string) {
        // Convert Vietnamese to ASCII
        $string = $this->removeVietnameseTones($string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }

    // Remove Vietnamese tones
    private function removeVietnameseTones($str) {
        $vietnamese = [
            'á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
            'đ',
            'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
            'í', 'ì', 'ỉ', 'ĩ', 'ị',
            'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
            'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
            'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',
        ];
        $noVietnamese = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'd',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
        ];
        return str_replace($vietnamese, $noVietnamese, $str);
    }

    // Lấy tags phổ biến (có nhiều bài viết nhất)
    public function getPopularTags($limit = 10) {
        $this->db->query('SELECT ft.*, COUNT(fpt.post_id) as post_count
                         FROM forum_tags ft
                         LEFT JOIN forum_post_tags fpt ON ft.id = fpt.tag_id
                         GROUP BY ft.id
                         ORDER BY post_count DESC
                         LIMIT :limit');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
?>

