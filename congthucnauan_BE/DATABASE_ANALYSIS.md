# Phân Tích Database - Công Thức Nấu Ăn

## 📊 Tổng quan Database

Database gồm **14 bảng** chính với các module:
- **Users & Authentication** (users)
- **Recipes Management** (recipes, categories, ratings)
- **Courses System** (courses, classrooms, course_lessons, course_enrollments, course_lesson_completion)
- **Forum** (forum_posts, forum_comments, forum_likes, forum_tags, forum_post_tags)
- **Payments** (payments, payment_gateway_config, payment_logs)
- **Others** (contacts)

---

## 🗂️ Chi tiết các bảng

### 1. **users** - Quản lý người dùng
```sql
- id (PK)
- name
- email (UNIQUE)
- phone
- address
- avatar (default: 'default-avatar.png')
- password (bcrypt)
- role (enum: 'user', 'manager', 'admin')
- created_at
```
**Vai trò:**
- `user`: Người dùng thường
- `manager`: Quản lý nội dung
- `admin`: Quản trị viên

---

### 2. **categories** - Danh mục công thức
```sql
- id (PK)
- name (UNIQUE)
- created_at
```
**Dữ liệu mẫu:** Món Việt Nam, Món Á, Món Âu, Món chay, Bánh ngọt, Đồ uống, Ăn vặt, Salad, Món tráng miệng

---

### 3. **recipes** - Công thức nấu ăn
```sql
- id (PK)
- title
- description
- ingredients (TEXT)
- instructions (TEXT)
- image (default: 'no-image.jpg')
- video_url
- category_id (FK -> categories)
- user_id (FK -> users)
- status (enum: 'pending', 'approved', 'rejected')
- created_at
- updated_at
```

---

### 4. **ratings** - Đánh giá công thức
```sql
- id (PK)
- recipe_id (FK -> recipes)
- user_id (FK -> users)
- rating (1-5)
- comment (TEXT)
- created_at
```
**Constraint:** UNIQUE(recipe_id, user_id) - Mỗi user chỉ đánh giá 1 lần/recipe

---

### 5. **classrooms** - Phòng học (cho courses)
```sql
- id (PK)
- name
- description
- capacity (default: 30)
- location
- active (boolean, default: 1)
- created_at
```

---

### 6. **courses** - Khóa học
```sql
- id (PK)
- title
- description
- price (decimal 10,2)
- duration (minutes)
- level (enum: 'beginner', 'intermediate', 'advanced')
- classroom_id (FK -> classrooms)
- user_id (FK -> users) - Người tạo
- image (default: 'no-image.jpg')
- status (enum: 'draft', 'published', 'archived')
- requirements (TEXT)
- what_will_learn (TEXT)
- created_at
```

---

### 7. **course_lessons** - Bài học trong khóa học
```sql
- id (PK)
- course_id (FK -> courses)
- title
- content (TEXT)
- video_url
- duration_minutes
- sort_order
- is_free (boolean) - Bài học miễn phí
- image (default: 'no-image.jpg')
- summary (TEXT)
- created_at
```

---

### 8. **course_enrollments** - Đăng ký khóa học
```sql
- id (PK)
- user_id (FK -> users)
- course_id (FK -> courses)
- payment_id (FK -> payments)
- enrollment_date
- completion_date
- progress (0-100)
- status (enum: 'active', 'completed', 'cancelled')
```
**Constraint:** UNIQUE(user_id, course_id)

---

### 9. **course_lesson_completion** - Hoàn thành bài học
```sql
- id (PK)
- user_id (FK -> users)
- course_id (FK -> courses)
- lesson_id (FK -> course_lessons)
- completed_at
```
**Constraint:** UNIQUE(user_id, lesson_id)

---

### 10. **forum_posts** - Bài viết diễn đàn
```sql
- id (PK)
- user_id (FK -> users)
- title
- content (TEXT)
- image
- video_url
- recipe_id (FK -> recipes) - Liên kết công thức
- views (default: 0)
- is_pinned (boolean)
- status (enum: 'active', 'hidden', 'deleted')
- created_at
- updated_at
```

---

### 11. **forum_comments** - Bình luận diễn đàn
```sql
- id (PK)
- post_id (FK -> forum_posts)
- user_id (FK -> users)
- parent_id (FK -> forum_comments) - Cho reply
- content (TEXT)
- created_at
```

---

### 12. **forum_likes** - Likes bài viết
```sql
- id (PK)
- post_id (FK -> forum_posts)
- user_id (FK -> users)
- created_at
```
**Constraint:** UNIQUE(post_id, user_id)

---

### 13. **forum_tags** - Tags diễn đàn
```sql
- id (PK)
- name (UNIQUE)
- slug (UNIQUE)
- created_at
```

---

### 14. **forum_post_tags** - Liên kết posts-tags (Many-to-Many)
```sql
- post_id (FK -> forum_posts)
- tag_id (FK -> forum_tags)
```
**PK:** Composite(post_id, tag_id)

---

### 15. **payments** - Thanh toán
```sql
- id (PK)
- user_id (FK -> users)
- amount (decimal 10,2)
- payment_method (varchar: 'credit_card', 'bank_transfer', 'momo', 'vnpay', 'stripe', 'demo')
- status (enum: 'pending', 'completed', 'failed', 'refunded', 'cancelled')
- transaction_id
- gateway_transaction_id
- gateway_response (JSON TEXT)
- created_at
- updated_at
```

---

### 16. **payment_gateway_config** - Cấu hình payment gateways
```sql
- id (PK)
- gateway (varchar: 'momo', 'vnpay', 'stripe')
- config_key
- config_value
- is_encrypted (boolean)
- is_active (boolean)
- created_at
- updated_at
```

---

### 17. **payment_logs** - Logs giao dịch
```sql
- id (PK)
- payment_id (FK -> payments)
- transaction_id
- action (varchar: 'create', 'callback', 'update')
- gateway
- request_data (JSON TEXT)
- response_data (JSON TEXT)
- status
- message
- ip_address
- user_agent
- created_at
```

---

### 18. **contacts** - Liên hệ
```sql
- id (PK)
- name
- email
- subject
- message (TEXT)
- status (enum: 'new', 'read', 'responded')
- created_at
```

---

## 🎯 Kế hoạch Migration sang Laravel

### Phase 1: Core Tables
1. ✅ users (đã có sẵn trong Laravel)
2. ✅ categories
3. ✅ recipes
4. ✅ ratings

### Phase 2: Courses System
5. ✅ classrooms
6. ✅ courses
7. ✅ course_lessons
8. ✅ course_enrollments
9. ✅ course_lesson_completion

### Phase 3: Forum
10. ✅ forum_tags
11. ✅ forum_posts
12. ✅ forum_comments
13. ✅ forum_likes
14. ✅ forum_post_tags

### Phase 4: Payments
15. ✅ payments
16. ✅ payment_gateway_config
17. ✅ payment_logs

### Phase 5: Others
18. ✅ contacts

---

## 🔐 Authentication & Authorization

### Roles & Permissions
- **Admin**: Full access
- **Manager**: Quản lý nội dung (recipes, courses, contacts)
- **User**: Đăng recipe, mua course, tham gia forum

### Laravel Policy cần tạo:
- RecipePolicy
- CoursePolicy
- ForumPostPolicy
- PaymentPolicy

---

## 📝 API Endpoints cần xây dựng

### Auth
- POST /api/register
- POST /api/login
- POST /api/logout
- GET /api/user

### Recipes
- GET /api/recipes
- GET /api/recipes/{id}
- POST /api/recipes
- PUT /api/recipes/{id}
- DELETE /api/recipes/{id}
- POST /api/recipes/{id}/rate

### Categories
- GET /api/categories

### Courses
- GET /api/courses
- GET /api/courses/{id}
- POST /api/courses/{id}/enroll
- GET /api/my-courses

### Forum
- GET /api/forum/posts
- POST /api/forum/posts
- GET /api/forum/posts/{id}
- POST /api/forum/posts/{id}/like
- POST /api/forum/posts/{id}/comment

### Payments
- POST /api/payments/create
- POST /api/payments/callback
- GET /api/payments/history

---

## 🚀 Bước tiếp theo

1. ✅ Tạo Laravel Migrations từ database schema
2. ✅ Tạo Models với relationships
3. ✅ Setup Laravel Sanctum cho authentication
4. ✅ Tạo Controllers & API Routes
5. ✅ Implement Business Logic
6. ✅ Testing API với Postman
7. ✅ Kết nối React Frontend

---

## 💡 Lưu ý quan trọng

- Password đã hash bằng bcrypt ($2y$10$...)
- Cần migrate dữ liệu từ database cũ
- Giữ nguyên IDs để reference không bị sai
- Upload files (images, avatars) cần copy sang storage Laravel