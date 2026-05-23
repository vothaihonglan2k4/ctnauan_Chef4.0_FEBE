# 🎯 HƯỚNG DẪN SỬ DỤNG DIỄN ĐÀN CHIA SẺ CÔNG THỨC NẤU ĂN

## ✨ **HOÀN TẤT! Diễn đàn đã sẵn sàng**

---

## 🚀 **3 BƯỚC BẮT ĐẦU:**

### **BƯỚC 1: Chạy SQL Migration** ⚠️ **BẮT BUỘC**
```bash
# Mở phpMyAdmin → Database: congthucnauan → Tab SQL
# Copy toàn bộ nội dung file: forum_migration.sql
# Paste và click GO
```

### **BƯỚC 2: Truy cập Diễn đàn**
```
URL: http://localhost/webcongthucnauan/forum
```

### **BƯỚC 3: Test thử!**
1. Đăng nhập (nếu chưa có tài khoản thì đăng ký)
2. Click "Tạo bài viết mới"
3. Viết bài, upload ảnh, chọn tags
4. Đăng bài và xem kết quả!

---

## 📋 **DANH SÁCH FILES ĐÃ TẠO:**

### ✅ **1. Database (SQL)**
- `forum_migration.sql` - Tạo 5 tables + dữ liệu mẫu

### ✅ **2. Models (app/models/)**
- `ForumPost.php` - Quản lý bài viết
- `ForumComment.php` - Quản lý bình luận
- `ForumLike.php` - Quản lý like
- `ForumTag.php` - Quản lý tags

### ✅ **3. Controller (app/controllers/)**
- `ForumController.php` - Xử lý logic diễn đàn

### ✅ **4. Views (app/views/forum/)**
- `index.php` - Trang chủ diễn đàn (danh sách bài viết)
- `show.php` - Chi tiết bài viết + comments
- `create.php` - Form tạo bài viết mới
- `search.php` - Kết quả tìm kiếm
- `tag.php` - Bài viết theo tag

### ✅ **5. Thư mục uploads**
- `public/uploads/forum/` - Lưu ảnh bài viết

---

## 🗄️ **CẤU TRÚC DATABASE:**

```sql
forum_posts              -- Bảng bài viết diễn đàn
├── id                   -- ID bài viết
├── user_id              -- ID người đăng
├── title                -- Tiêu đề
├── content              -- Nội dung
├── image                -- Ảnh đính kèm
├── video_url            -- Link YouTube
├── recipe_id            -- Liên kết công thức
├── views                -- Số lượt xem
├── is_pinned            -- Ghim bài (0/1)
├── status               -- active/hidden/deleted
├── created_at           -- Ngày tạo
└── updated_at           -- Ngày cập nhật

forum_comments           -- Bảng bình luận
├── id
├── post_id              -- ID bài viết
├── user_id              -- ID người comment
├── parent_id            -- ID comment cha (reply)
├── content              -- Nội dung
└── created_at

forum_likes              -- Bảng like
├── id
├── post_id
├── user_id
└── created_at

forum_tags               -- Bảng tags
├── id
├── name                 -- Tên tag
├── slug                 -- Slug cho URL
└── created_at

forum_post_tags          -- Bảng liên kết posts-tags
├── post_id
└── tag_id
```

---

## 🎯 **CHỨC NĂNG DIỄN ĐÀN:**

### **1. Xem bài viết** (Tất cả user)
- ✅ Danh sách bài viết với pagination
- ✅ Xem chi tiết bài viết
- ✅ Xem comments
- ✅ Đếm views, likes, comments
- ✅ Lọc theo tags
- ✅ Tìm kiếm bài viết

### **2. Tạo bài viết** (User đã đăng nhập)
- ✅ Viết tiêu đề + nội dung
- ✅ Upload ảnh (JPG, PNG, GIF - max 5MB)
- ✅ Nhúng video YouTube
- ✅ Liên kết đến công thức đã đăng
- ✅ Chọn tags (tối đa 3)
- ✅ Preview ảnh trước khi đăng

### **3. Tương tác** (User đã đăng nhập)
- ✅ Like/Unlike bài viết (AJAX)
- ✅ Comment bài viết (AJAX)
- ✅ Xóa comment của mình
- ✅ Xóa bài viết của mình

### **4. Quản lý** (Admin/Manager)
- ✅ Ghim bài viết quan trọng
- ✅ Ẩn/Xóa bài vi phạm
- ✅ Xóa comment không phù hợp

---

## 📍 **ROUTES (URL):**

| URL | Chức năng |
|-----|-----------|
| `/forum` | Trang chủ diễn đàn |
| `/forum/show/123` | Chi tiết bài viết ID 123 |
| `/forum/create` | Tạo bài viết mới |
| `/forum/search?q=keyword` | Tìm kiếm |
| `/forum/tag/5` | Bài viết theo tag ID 5 |
| `/forum/toggleLike/123` | Like/Unlike (AJAX) |
| `/forum/addComment` | Thêm comment (AJAX) |
| `/forum/deleteComment/10` | Xóa comment ID 10 |
| `/forum/delete/123` | Xóa bài viết ID 123 |

---

## 🖼️ **GIAO DIỆN:**

### **Trang chủ Diễn đàn:**
```
┌────────────────────────────────────────────────┐
│  🗣️ Diễn đàn chia sẻ công thức     [+ Tạo bài] │
├────────────────────────────────────────────────┤
│  [🔍 Tìm kiếm...]                              │
├────────────────────────────────────────────────┤
│  📌 [Ghim] Bí quyết làm phở bò...              │
│     👤 Admin • ⏰ 10/11/2025                    │
│     Nội dung preview...                         │
│     ❤️ 125  💬 45  👁️ 1.2K                     │
├────────────────────────────────────────────────┤
│  📝 Hỏi: Làm sao để sushi không nát?           │
│     👤 User • ⏰ 11/11/2025                     │
│     Nội dung preview...                         │
│     ❤️ 87  💬 23  👁️ 456                       │
├────────────────────────────────────────────────┤
│  [1] [2] [3] ... [10]  ← Pagination           │
└────────────────────────────────────────────────┘

Sidebar:
┌─────────────────┐
│ 🏷️ Tags phổ biến │
│ • Món Việt (12) │
│ • Bí quyết (8)  │
│ • Mẹo hay (5)   │
└─────────────────┘
```

### **Chi tiết bài viết:**
```
┌────────────────────────────────────────────────┐
│  [← Quay lại]                                  │
├────────────────────────────────────────────────┤
│  👤 Admin                                       │
│  ⏰ 10/11/2025 10:30  •  👁️ 1,234 views       │
├────────────────────────────────────────────────┤
│  📝 Bí quyết làm phở bò thơm ngon              │
│  🏷️ Món Việt | Bí quyết | Mẹo hay            │
├────────────────────────────────────────────────┤
│  Nội dung đầy đủ của bài viết...               │
│  [Ảnh minh họa]                                │
│  [Video YouTube nhúng]                         │
│  ✅ Liên kết: Công thức Phở Bò                 │
├────────────────────────────────────────────────┤
│  [❤️ 125 Thích]         💬 45 bình luận        │
├────────────────────────────────────────────────┤
│  💬 BÌNH LUẬN:                                 │
│  ┌──────────────────────────────────────────┐ │
│  │ 👤 User A • 2 giờ trước                  │ │
│  │ Cảm ơn bạn! Rất hữu ích                  │ │
│  └──────────────────────────────────────────┘ │
│  ┌──────────────────────────────────────────┐ │
│  │ 👤 User B • 1 giờ trước                  │ │
│  │ Mình sẽ thử làm theo                     │ │
│  └──────────────────────────────────────────┘ │
│  [Viết bình luận...]  [Gửi]                   │
└────────────────────────────────────────────────┘
```

### **Form tạo bài viết:**
```
┌────────────────────────────────────────────────┐
│  ➕ Tạo bài viết mới              [Hủy]        │
├────────────────────────────────────────────────┤
│  📝 Tiêu đề: [___________________________]    │
│  ✏️ Nội dung:                                  │
│     [________________________________]         │
│     [________________________________]         │
│     [________________________________]         │
│                                                │
│  🖼️ Ảnh: [Chọn file]  [Preview ảnh]           │
│  🎥 Video: [Link YouTube...]                   │
│  🍴 Công thức: [Chọn từ danh sách]             │
│  🏷️ Tags: ☑️ Món Việt  ☐ Món Á  ☑️ Bí quyết  │
│                                                │
│  [Làm lại]              [📤 Đăng bài viết]    │
└────────────────────────────────────────────────┘
```

---

## 🎨 **TÍNH NĂNG NỔI BẬT:**

1. **📌 Ghim bài viết quan trọng** - Admin có thể ghim bài lên đầu
2. **❤️ Like/Unlike realtime** - AJAX không reload trang
3. **💬 Comment realtime** - AJAX thêm comment ngay lập tức
4. **🖼️ Preview ảnh** - Xem trước trước khi upload
5. **🎥 Nhúng YouTube** - Auto convert link thành video player
6. **🏷️ Tags thông minh** - Lọc bài viết theo tags
7. **🔍 Tìm kiếm mạnh mẽ** - Tìm theo tiêu đề và nội dung
8. **📊 Thống kê đầy đủ** - Views, likes, comments
9. **🔗 Liên kết công thức** - Link đến recipe đã đăng
10. **📱 Responsive** - Tối ưu cho mobile

---

## 🔒 **BẢO MẬT & VALIDATE:**

✅ Sanitize input (XSS protection)
✅ Validate file upload (type, size)
✅ CSRF protection (session)
✅ SQL Injection protection (PDO prepared statements)
✅ Permission check (chỉ chủ bài mới xóa được)
✅ Soft delete (không xóa hẳn khỏi DB)

---

## 📝 **DỮ LIỆU MẪU:**

SQL migration đã tạo sẵn:
- ✅ 8 tags mẫu (Món Việt, Món Á, Món Âu, Bí quyết, Mẹo hay, Thảo luận, Hỏi đáp, Chia sẻ)
- ✅ 3 bài viết mẫu
- ✅ 6 comments mẫu
- ✅ 11 likes mẫu

Bạn có thể test ngay sau khi chạy SQL!

---

## 🐛 **TROUBLESHOOTING:**

### **Lỗi: "Table doesn't exist"**
→ Chưa chạy SQL migration. Chạy file `forum_migration.sql`

### **Lỗi: "Cannot upload image"**
→ Kiểm tra quyền thư mục `public/uploads/forum/` (phải có quyền ghi)

### **Lỗi 404: "/forum not found"**
→ Kiểm tra file `ForumController.php` đã tồn tại chưa

### **Like/Comment không hoạt động**
→ Kiểm tra JavaScript console, đảm bảo jQuery đã load

### **Avatar không hiển thị**
→ Đảm bảo đã chạy migration profile fields trước đó

---

## 🎯 **LUỒNG HOẠT ĐỘNG:**

### **1. User truy cập diễn đàn:**
```
Browser → /forum 
       → ForumController::index()
       → ForumPost::getAllPosts()
       → View: forum/index.php
```

### **2. User tạo bài viết:**
```
Browser → /forum/create (GET)
       → ForumController::create()
       → View: forum/create.php
       
       → [User submit form]
       
       → /forum/store (POST)
       → ForumController::store()
       → Upload image (nếu có)
       → ForumPost::createPost()
       → Add tags
       → Redirect: /forum/show/{id}
```

### **3. User like bài viết:**
```
Browser → Click like button
       → AJAX: /forum/toggleLike/{id}
       → ForumController::toggleLike()
       → ForumLike::likePost() / unlikePost()
       → Return JSON
       → Update UI (không reload)
```

---

## 🚀 **TÍNH NĂNG CÓ THỂ MỞ RỘNG:**

- [ ] Edit bài viết
- [ ] Upload nhiều ảnh
- [ ] Rich text editor (TinyMCE, CKEditor)
- [ ] Thông báo realtime (WebSocket)
- [ ] Report bài vi phạm
- [ ] Reputation system (điểm uy tín)
- [ ] Trending posts (thuật toán)
- [ ] Email notification
- [ ] Social share (Facebook, Twitter)
- [ ] Bookmark/Save posts
- [ ] User following
- [ ] Post reactions (không chỉ like)

---

## ✨ **HOÀN TẤT!**

Tất cả tính năng diễn đàn đã sẵn sàng!

**Các bước tiếp theo:**
1. ✅ Chạy SQL migration
2. ✅ Truy cập `/forum`
3. ✅ Đăng nhập
4. ✅ Tạo bài viết đầu tiên
5. ✅ Like, comment, chia sẻ!

**Có vấn đề?** Kiểm tra lại:
- File SQL đã chạy chưa?
- Thư mục uploads có quyền ghi không?
- Controllers/Models đã tồn tại chưa?

**Chúc bạn thành công!** 🎉

