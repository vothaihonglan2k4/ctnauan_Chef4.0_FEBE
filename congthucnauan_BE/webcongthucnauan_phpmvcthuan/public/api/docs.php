<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Website Công Thức Nấu Ăn</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 8px;
        }
        
        .header h1 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .header p {
            text-align: center;
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .nav {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        
        .nav a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .nav a:hover {
            background-color: #667eea;
            color: white;
        }
        
        .section {
            background: white;
            margin-bottom: 2rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-header {
            background: #667eea;
            color: white;
            padding: 1rem 1.5rem;
            font-size: 1.3em;
            font-weight: 600;
        }
        
        .section-content {
            padding: 1.5rem;
        }
        
        .endpoint {
            margin-bottom: 2rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .endpoint-header {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .method {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 0.9em;
            margin-right: 10px;
        }
        
        .method.get { background-color: #28a745; }
        .method.post { background-color: #007bff; }
        .method.put { background-color: #ffc107; color: #212529; }
        .method.delete { background-color: #dc3545; }
        
        .url {
            font-family: 'Courier New', monospace;
            font-size: 1.1em;
            color: #495057;
        }
        
        .endpoint-body {
            padding: 1rem;
        }
        
        .description {
            margin-bottom: 1rem;
            color: #666;
        }
        
        .params, .response {
            margin-bottom: 1rem;
        }
        
        .params h4, .response h4 {
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        .param-table, .response-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }
        
        .param-table th, .param-table td,
        .response-table th, .response-table td {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            text-align: left;
        }
        
        .param-table th, .response-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .code {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
        }
        
        .auth-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 1rem;
            margin: 1rem 0;
            color: #856404;
        }
        
        .auth-info strong {
            display: block;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .nav ul {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍳 API Documentation</h1>
            <p>Tài liệu API cho Website Công Thức Nấu Ăn</p>
        </div>
        
        <div class="nav">
            <ul>
                <li><a href="#overview">Tổng quan</a></li>
                <li><a href="#authentication">Xác thực</a></li>
                <li><a href="#recipes">Công thức</a></li>
                <li><a href="#users">Người dùng</a></li>
                <li><a href="#categories">Danh mục</a></li>
                <li><a href="#courses">Khóa học</a></li>
                <li><a href="#ratings">Đánh giá</a></li>
            </ul>
        </div>
        
        <div id="overview" class="section">
            <div class="section-header">📖 Tổng quan</div>
            <div class="section-content">
                <h3>Base URL</h3>
                <div class="code">https://yourdomain.com/public/api/</div>
                
                <h3>Format phản hồi</h3>
                <p>Tất cả API trả về dữ liệu dạng JSON với format sau:</p>
                <div class="code">{
  "status": "success|error",
  "message": "Thông báo",
  "data": {...} // Chỉ có khi success
}</div>
                
                <h3>Mã trạng thái HTTP</h3>
                <ul>
                    <li><strong>200:</strong> Thành công</li>
                    <li><strong>201:</strong> Tạo thành công</li>
                    <li><strong>400:</strong> Lỗi dữ liệu đầu vào</li>
                    <li><strong>401:</strong> Chưa xác thực</li>
                    <li><strong>403:</strong> Không có quyền</li>
                    <li><strong>404:</strong> Không tìm thấy</li>
                    <li><strong>422:</strong> Lỗi validation</li>
                    <li><strong>500:</strong> Lỗi server</li>
                </ul>
            </div>
        </div>
        
        <div id="authentication" class="section">
            <div class="section-header">🔐 Xác thực</div>
            <div class="section-content">
                <p>Một số API yêu cầu xác thực. Sau khi đăng nhập thành công, bạn sẽ nhận được token. Sử dụng token này trong header:</p>
                <div class="code">Authorization: Bearer YOUR_TOKEN_HERE</div>
                
                <h3>Đăng nhập để lấy token:</h3>
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/users/login</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Đăng nhập và nhận token xác thực</div>
                        <div class="code">{
  "email": "user@example.com",
  "password": "password123"
}</div>
                        <div class="response">
                            <h4>Phản hồi thành công:</h4>
                            <div class="code">{
  "status": "success",
  "message": "Đăng nhập thành công",
  "data": {
    "user": {
      "id": 1,
      "name": "Tên người dùng",
      "email": "user@example.com",
      "role": "user"
    },
    "token": "abc123..."
  }
}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="recipes" class="section">
            <div class="section-header">🍲 API Công thức nấu ăn</div>
            <div class="section-content">
                
                <!-- GET All Recipes -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/recipes</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy danh sách tất cả công thức đã được duyệt</div>
                        <div class="params">
                            <h4>Query Parameters:</h4>
                            <table class="param-table">
                                <tr><th>Tham số</th><th>Kiểu</th><th>Bắt buộc</th><th>Mô tả</th></tr>
                                <tr><td>search</td><td>string</td><td>Không</td><td>Từ khóa tìm kiếm</td></tr>
                                <tr><td>category</td><td>int</td><td>Không</td><td>ID danh mục</td></tr>
                                <tr><td>ingredients</td><td>string</td><td>Không</td><td>Tìm theo nguyên liệu</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- GET Single Recipe -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/recipes/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy chi tiết công thức theo ID</div>
                    </div>
                </div>
                
                <!-- POST Create Recipe -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/recipes</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                            Cần đăng nhập để tạo công thức mới
                        </div>
                        <div class="description">Tạo công thức nấu ăn mới (sẽ ở trạng thái chờ duyệt)</div>
                        <div class="params">
                            <h4>Body Parameters:</h4>
                            <table class="param-table">
                                <tr><th>Tham số</th><th>Kiểu</th><th>Bắt buộc</th><th>Mô tả</th></tr>
                                <tr><td>title</td><td>string</td><td>Có</td><td>Tên công thức</td></tr>
                                <tr><td>description</td><td>string</td><td>Có</td><td>Mô tả</td></tr>
                                <tr><td>ingredients</td><td>string</td><td>Có</td><td>Nguyên liệu</td></tr>
                                <tr><td>instructions</td><td>string</td><td>Có</td><td>Hướng dẫn</td></tr>
                                <tr><td>category_id</td><td>int</td><td>Có</td><td>ID danh mục</td></tr>
                                <tr><td>image</td><td>string</td><td>Không</td><td>Tên file ảnh</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- PUT Update Recipe -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="url">/recipes/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                            Chỉ tác giả có thể cập nhật công thức
                        </div>
                        <div class="description">Cập nhật công thức (chỉ tác giả)</div>
                    </div>
                </div>
                
                <!-- DELETE Recipe -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="url">/recipes/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                            Chỉ tác giả có thể xóa công thức
                        </div>
                        <div class="description">Xóa công thức (chỉ tác giả)</div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="users" class="section">
            <div class="section-header">👤 API Người dùng</div>
            <div class="section-content">
                
                <!-- POST Register -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/users/register</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Đăng ký tài khoản mới</div>
                        <div class="params">
                            <h4>Body Parameters:</h4>
                            <table class="param-table">
                                <tr><th>Tham số</th><th>Kiểu</th><th>Bắt buộc</th><th>Mô tả</th></tr>
                                <tr><td>name</td><td>string</td><td>Có</td><td>Tên người dùng</td></tr>
                                <tr><td>email</td><td>string</td><td>Có</td><td>Email (duy nhất)</td></tr>
                                <tr><td>password</td><td>string</td><td>Có</td><td>Mật khẩu (tối thiểu 6 ký tự)</td></tr>
                                <tr><td>confirm_password</td><td>string</td><td>Có</td><td>Xác nhận mật khẩu</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- POST Login -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/users/login</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Đăng nhập và nhận token</div>
                    </div>
                </div>
                
                <!-- POST Logout -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/users/logout</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                        </div>
                        <div class="description">Đăng xuất và xóa token</div>
                    </div>
                </div>
                
                <!-- GET Profile -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/users/profile</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                        </div>
                        <div class="description">Lấy thông tin profile người dùng hiện tại</div>
                    </div>
                </div>
                
                <!-- PUT Update Profile -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="url">/users/profile</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                        </div>
                        <div class="description">Cập nhật thông tin profile</div>
                    </div>
                </div>
                
                <!-- PUT Change Password -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="url">/users/password</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                        </div>
                        <div class="description">Đổi mật khẩu</div>
                        <div class="params">
                            <h4>Body Parameters:</h4>
                            <table class="param-table">
                                <tr><th>Tham số</th><th>Kiểu</th><th>Bắt buộc</th><th>Mô tả</th></tr>
                                <tr><td>current_password</td><td>string</td><td>Có</td><td>Mật khẩu hiện tại</td></tr>
                                <tr><td>new_password</td><td>string</td><td>Có</td><td>Mật khẩu mới</td></tr>
                                <tr><td>confirm_password</td><td>string</td><td>Có</td><td>Xác nhận mật khẩu mới</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="categories" class="section">
            <div class="section-header">📁 API Danh mục</div>
            <div class="section-content">
                
                <!-- GET Categories -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/categories</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy danh sách tất cả danh mục</div>
                    </div>
                </div>
                
                <!-- GET Single Category -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/categories/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy thông tin danh mục theo ID</div>
                    </div>
                </div>
                
                <!-- POST Create Category -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/categories</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu quyền Admin</strong>
                            Chỉ admin mới có thể tạo danh mục
                        </div>
                        <div class="description">Tạo danh mục mới</div>
                    </div>
                </div>
                
                <!-- PUT Update Category -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="url">/categories/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu quyền Admin</strong>
                        </div>
                        <div class="description">Cập nhật danh mục</div>
                    </div>
                </div>
                
                <!-- DELETE Category -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="url">/categories/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu quyền Admin</strong>
                        </div>
                        <div class="description">Xóa danh mục</div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="courses" class="section">
            <div class="section-header">🎓 API Khóa học</div>
            <div class="section-content">
                
                <!-- GET Courses -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/courses</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy danh sách tất cả khóa học</div>
                    </div>
                </div>
                
                <!-- GET Single Course -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/courses/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy chi tiết khóa học theo ID</div>
                    </div>
                </div>
                
                <!-- GET Course Lessons -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/courses/{id}/lessons</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy danh sách bài học của khóa học</div>
                    </div>
                </div>
                
                <!-- POST Enroll Course -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/courses/{id}/enroll</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                        </div>
                        <div class="description">Đăng ký tham gia khóa học</div>
                    </div>
                </div>
                
                <!-- POST Create Course -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/courses</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu quyền Admin/Instructor</strong>
                        </div>
                        <div class="description">Tạo khóa học mới</div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="ratings" class="section">
            <div class="section-header">⭐ API Đánh giá</div>
            <div class="section-content">
                
                <!-- GET Ratings -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/ratings</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="description">Lấy danh sách đánh giá</div>
                        <div class="params">
                            <h4>Query Parameters:</h4>
                            <table class="param-table">
                                <tr><th>Tham số</th><th>Kiểu</th><th>Bắt buộc</th><th>Mô tả</th></tr>
                                <tr><td>recipe_id</td><td>int</td><td>Không</td><td>Lọc theo ID công thức</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- POST Create Rating -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/ratings</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                        </div>
                        <div class="description">Tạo đánh giá mới cho công thức</div>
                        <div class="params">
                            <h4>Body Parameters:</h4>
                            <table class="param-table">
                                <tr><th>Tham số</th><th>Kiểu</th><th>Bắt buộc</th><th>Mô tả</th></tr>
                                <tr><td>recipe_id</td><td>int</td><td>Có</td><td>ID công thức</td></tr>
                                <tr><td>rating</td><td>int</td><td>Có</td><td>Điểm đánh giá (1-5)</td></tr>
                                <tr><td>comment</td><td>string</td><td>Không</td><td>Bình luận</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- PUT Update Rating -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="url">/ratings/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                            Chỉ tác giả có thể cập nhật đánh giá
                        </div>
                        <div class="description">Cập nhật đánh giá</div>
                    </div>
                </div>
                
                <!-- DELETE Rating -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="url">/ratings/{id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="auth-info">
                            <strong>⚠️ Yêu cầu xác thực</strong>
                            Chỉ tác giả có thể xóa đánh giá
                        </div>
                        <div class="description">Xóa đánh giá</div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="section">
            <div class="section-header">💡 Ví dụ sử dụng</div>
            <div class="section-content">
                <h3>JavaScript/Fetch API</h3>
                <div class="code">// Lấy danh sách công thức
fetch('https://yourdomain.com/public/api/recipes')
  .then(response => response.json())
  .then(data => console.log(data));

// Đăng nhập
fetch('https://yourdomain.com/public/api/users/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password123'
  })
})
.then(response => response.json())
.then(data => {
  if (data.status === 'success') {
    const token = data.data.token;
    localStorage.setItem('token', token);
  }
});

// Tạo công thức mới (cần token)
fetch('https://yourdomain.com/public/api/recipes', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + localStorage.getItem('token')
  },
  body: JSON.stringify({
    title: 'Món ngon',
    description: 'Mô tả món ăn',
    ingredients: 'Nguyên liệu...',
    instructions: 'Cách làm...',
    category_id: 1
  })
})
.then(response => response.json())
.then(data => console.log(data));</div>
                
                <h3>cURL</h3>
                <div class="code"># Lấy danh sách công thức
curl -X GET "https://yourdomain.com/public/api/recipes"

# Đăng nhập
curl -X POST "https://yourdomain.com/public/api/users/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# Tạo công thức mới (với token)
curl -X POST "https://yourdomain.com/public/api/recipes" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"title":"Món ngon","description":"Mô tả","ingredients":"Nguyên liệu","instructions":"Cách làm","category_id":1}'</div>
            </div>
        </div>
        
    </div>
    
    <script>
        // Smooth scrolling cho navigation
        document.querySelectorAll('.nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html> 