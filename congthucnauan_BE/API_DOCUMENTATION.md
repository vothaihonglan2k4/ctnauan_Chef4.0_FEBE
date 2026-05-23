# API Documentation - Web Công Thức Nấu Ăn

Base URL: `http://localhost:8000/api/v1`

## Authentication

### Register
```http
POST /register
Content-Type: application/json

{
  "username": "testuser",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "fullname": "Test User",
  "phone": "0123456789",
  "address": "123 Test Street"
}

Response: 201 Created
{
  "message": "Đăng ký thành công",
  "user": { ... },
  "token": "1|xxxxx..."
}
```

### Login
```http
POST /login
Content-Type: application/json

{
  "username": "testuser",
  "password": "password123"
}

Response: 200 OK
{
  "message": "Đăng nhập thành công",
  "user": { ... },
  "token": "2|xxxxx..."
}
```

### Logout
```http
POST /logout
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Đăng xuất thành công"
}
```

### Get Current User
```http
GET /me
Authorization: Bearer {token}

Response: 200 OK
{
  "user": { ... }
}
```

### Update Profile
```http
PUT /profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "fullname": "Updated Name",
  "email": "updated@example.com",
  "phone": "0987654321",
  "address": "New Address",
  "bio": "My bio"
}
```

### Change Password
```http
POST /change-password
Authorization: Bearer {token}
Content-Type: application/json

{
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

## Recipes

### Get All Recipes
```http
GET /recipes?category_id=1&search=chicken&difficulty=easy&sort=rating&order=desc&per_page=12&page=1

Response: 200 OK
{
  "recipes": [...],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 50
  }
}
```

### Get Single Recipe
```http
GET /recipes/{id}

Response: 200 OK
{
  "recipe": {
    "id": 1,
    "title": "...",
    "description": "...",
    "ingredients": "...",
    "instructions": "...",
    "cooking_time": 30,
    "servings": 4,
    "difficulty": "easy",
    "image": "...",
    "video_url": "...",
    "views": 100,
    "average_rating": 4.5,
    "total_ratings": 10,
    "user": {...},
    "category": {...},
    "ratings": [...]
  }
}
```

### Create Recipe
```http
POST /recipes
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "Recipe Title",
  "description": "Recipe description",
  "ingredients": "Ingredient list",
  "instructions": "Step by step",
  "category_id": 1,
  "cooking_time": 30,
  "servings": 4,
  "difficulty": "easy",
  "image": [file],
  "video_url": "https://youtube.com/..."
}

Response: 201 Created
{
  "message": "Công thức đã được tạo và đang chờ duyệt",
  "recipe": { ... }
}
```

### Update Recipe
```http
PUT /recipes/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "Updated Title",
  ...
}
```

### Delete Recipe
```http
DELETE /recipes/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Công thức đã được xóa"
}
```

### Get My Recipes
```http
GET /my-recipes
Authorization: Bearer {token}

Response: 200 OK
{
  "recipes": [...],
  "pagination": {...}
}
```

### Get Categories
```http
GET /categories

Response: 200 OK
{
  "categories": [
    {
      "id": 1,
      "name": "Món Việt",
      "recipes_count": 10
    },
    ...
  ]
}
```

---

## Ratings

### Add/Update Rating
```http
POST /recipes/{recipeId}/ratings
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 5,
  "comment": "Great recipe!"
}

Response: 201 Created
{
  "message": "Đánh giá đã được thêm",
  "rating": { ... }
}
```

### Get Recipe Ratings
```http
GET /recipes/{recipeId}/ratings

Response: 200 OK
{
  "average_rating": 4.5,
  "total_ratings": 10,
  "rating_distribution": {
    "5": 6,
    "4": 3,
    "3": 1,
    "2": 0,
    "1": 0
  },
  "ratings": [...]
}
```

### Get My Rating for Recipe
```http
GET /recipes/{recipeId}/my-rating
Authorization: Bearer {token}

Response: 200 OK
{
  "rating": {
    "id": 1,
    "rating": 5,
    "comment": "Great!",
    "created_at": "..."
  }
}
```

### Delete Rating
```http
DELETE /recipes/{recipeId}/ratings/{ratingId}
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Đánh giá đã được xóa"
}
```

---

## Courses

### Get All Courses
```http
GET /courses?level=beginner&search=cooking&sort=created_at&order=desc&per_page=12

Response: 200 OK
{
  "courses": [...],
  "pagination": {...}
}
```

### Get Single Course
```http
GET /courses/{id}

Response: 200 OK
{
  "course": {
    "id": 1,
    "title": "...",
    "description": "...",
    "level": "beginner",
    "duration": "4 weeks",
    "price": 500000,
    "discount_price": 400000,
    "total_students": 50,
    "classroom": {...},
    "lessons": [...],
    "is_enrolled": false,
    "progress": 0
  }
}
```

### Enroll in Course
```http
POST /courses/{id}/enroll
Authorization: Bearer {token}

Response: 201 Created (free course)
{
  "message": "Đăng ký khóa học thành công",
  "enrollment": { ... }
}

Response: 402 Payment Required (paid course)
{
  "message": "Vui lòng thanh toán để đăng ký khóa học",
  "requires_payment": true,
  "price": 400000
}
```

### Get My Courses
```http
GET /my-courses
Authorization: Bearer {token}

Response: 200 OK
{
  "enrollments": [
    {
      "id": 1,
      "status": "active",
      "progress": 50,
      "enrolled_at": "...",
      "course": {...}
    }
  ],
  "pagination": {...}
}
```

### Get Lesson Content
```http
GET /courses/{courseId}/lessons/{lessonId}
Authorization: Bearer {token}

Response: 200 OK
{
  "lesson": {
    "id": 1,
    "title": "...",
    "description": "...",
    "content": "...",
    "video_url": "...",
    "duration": 30,
    "order": 1,
    "is_completed": false
  }
}
```

### Complete Lesson
```http
POST /courses/{courseId}/lessons/{lessonId}/complete
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Đã hoàn thành bài học",
  "progress": 25
}
```

---

## Forum

### Get All Posts
```http
GET /forum/posts?tag=tips&search=chicken&per_page=15

Response: 200 OK
{
  "posts": [
    {
      "id": 1,
      "title": "...",
      "content": "...",
      "views": 100,
      "is_pinned": false,
      "comments_count": 5,
      "likes_count": 10,
      "user": {...},
      "tags": ["tips", "beginner"],
      "created_at": "..."
    }
  ],
  "pagination": {...}
}
```

### Get Single Post
```http
GET /forum/posts/{id}

Response: 200 OK
{
  "post": {
    "id": 1,
    "title": "...",
    "content": "...",
    "views": 101,
    "is_pinned": false,
    "user": {...},
    "tags": [...],
    "comments_count": 5,
    "likes_count": 10,
    "has_liked": false,
    "comments": [
      {
        "id": 1,
        "content": "...",
        "user": {...},
        "replies": [...],
        "created_at": "..."
      }
    ]
  }
}
```

### Create Post
```http
POST /forum/posts
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Post Title",
  "content": "Post content...",
  "tags": ["tips", "beginner"]
}

Response: 201 Created
{
  "message": "Bài viết đã được tạo",
  "post": { ... }
}
```

### Update Post
```http
PUT /forum/posts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "content": "Updated content",
  "tags": ["tips"]
}
```

### Delete Post
```http
DELETE /forum/posts/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Bài viết đã được xóa"
}
```

### Add Comment
```http
POST /forum/posts/{id}/comments
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Comment content",
  "parent_id": null  // or comment ID for reply
}

Response: 201 Created
{
  "message": "Bình luận đã được thêm",
  "comment": { ... }
}
```

### Delete Comment
```http
DELETE /forum/posts/{postId}/comments/{commentId}
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Bình luận đã được xóa"
}
```

### Toggle Like
```http
POST /forum/posts/{id}/like
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Đã thích",
  "liked": true
}
```

### Get All Tags
```http
GET /forum/tags

Response: 200 OK
{
  "tags": [
    {
      "id": 1,
      "name": "tips",
      "posts_count": 10
    }
  ]
}
```

### Get Posts by Tag
```http
GET /forum/tags/{tagName}

Response: 200 OK
{
  "tag": {...},
  "posts": [...],
  "pagination": {...}
}
```

---

## Testing với cURL

### Test Register
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "fullname": "Test User"
  }'
```

### Test Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "password": "password123"
  }'
```

### Test Get Recipes (with token)
```bash
curl http://localhost:8000/api/v1/recipes \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Test Get Categories
```bash
curl http://localhost:8000/api/v1/categories
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Bạn không có quyền truy cập"
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

### 500 Server Error
```json
{
  "message": "Server Error"
}
```

---

## Notes

- Tất cả các endpoint có prefix `/api/v1`
- Các endpoint cần authentication phải gửi header: `Authorization: Bearer {token}`
- Token nhận được sau khi login/register
- File upload (recipes) sử dụng `multipart/form-data`
- Pagination mặc định: 12 items/page (recipes), 15 items/page (forum)
- Tất cả datetime trả về theo format ISO 8601

## Next Steps

1. Test các endpoints với Postman hoặc cURL
2. Tích hợp React frontend
3. Implement payment gateway (VNPay, Stripe)
4. Add admin endpoints
5. Implement real-time features (notifications, chat)