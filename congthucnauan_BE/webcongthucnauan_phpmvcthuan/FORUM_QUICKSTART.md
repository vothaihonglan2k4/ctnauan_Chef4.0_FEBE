# ⚡ HƯỚNG DẪN NHANH - DIỄN ĐÀN

## 🚀 3 BƯỚC DUY NHẤT:

### **BƯỚC 1: Chạy SQL** ⚠️ **BẮT BUỘC**
```bash
# Mở phpMyAdmin
# Database: congthucnauan
# Tab SQL
# Copy toàn bộ nội dung file: forum_migration.sql
# Click GO
```

### **BƯỚC 2: Truy cập**
```
http://localhost/webcongthucnauan/forum
```

### **BƯỚC 3: Test!**
1. Đăng nhập (hoặc đăng ký)
2. Click "Tạo bài viết mới"
3. Viết bài → Đăng bài
4. DONE! 🎉

---

## 📂 CẤU TRÚC FILES:

```
forum_migration.sql               ← CHẠY FILE NÀY TRƯỚC!

app/
├── models/
│   ├── ForumPost.php            ← Quản lý bài viết
│   ├── ForumComment.php         ← Quản lý comment
│   ├── ForumLike.php            ← Quản lý like
│   └── ForumTag.php             ← Quản lý tags
│
├── controllers/
│   └── ForumController.php      ← Logic diễn đàn
│
└── views/
    └── forum/
        ├── index.php            ← Trang chủ
        ├── show.php             ← Chi tiết bài viết
        ├── create.php           ← Form tạo bài
        ├── search.php           ← Tìm kiếm
        └── tag.php              ← Lọc theo tag

public/uploads/forum/             ← Lưu ảnh bài viết
```

---

## ✨ CHỨC NĂNG:

✅ Tạo bài viết (tiêu đề + nội dung + ảnh + video)
✅ Like/Unlike bài viết (AJAX)
✅ Comment bài viết (AJAX)
✅ Tìm kiếm bài viết
✅ Lọc theo tags
✅ Ghim bài quan trọng
✅ Xóa bài viết/comment của mình
✅ Liên kết đến công thức
✅ Đếm views/likes/comments
✅ Avatar + thông tin user

---

## 🗄️ DATABASE:

5 bảng được tạo:
- `forum_posts` - Bài viết
- `forum_comments` - Bình luận
- `forum_likes` - Likes
- `forum_tags` - Tags
- `forum_post_tags` - Liên kết posts-tags

---

## 📍 URLS:

| URL | Mô tả |
|-----|-------|
| `/forum` | Trang chủ |
| `/forum/show/123` | Chi tiết bài 123 |
| `/forum/create` | Tạo bài mới |
| `/forum/search?q=abc` | Tìm kiếm |
| `/forum/tag/5` | Bài theo tag 5 |

---

## 🐛 LỖI THƯỜNG GẶP:

**Lỗi: "Table doesn't exist"**
→ Chưa chạy SQL. Chạy `forum_migration.sql`

**Lỗi: "Cannot upload"**
→ Thư mục `public/uploads/forum/` không có quyền ghi

**404 Error**
→ Kiểm tra `ForumController.php` đã có chưa

---

**XONG! Giờ vào `/forum` và test thử!** 🚀

