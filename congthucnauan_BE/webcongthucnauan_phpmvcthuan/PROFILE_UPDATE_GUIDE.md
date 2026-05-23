# Hướng Dẫn Cập Nhật Hồ Sơ Người Dùng

## 🎯 Các trường đã thêm mới:

1. **Số điện thoại (phone)** - VARCHAR(20), không bắt buộc
2. **Địa chỉ (address)** - TEXT, không bắt buộc  
3. **Ảnh đại diện (avatar)** - VARCHAR(255), mặc định: `default-avatar.png`

---

## 📝 Các bước đã thực hiện:

### 1. ✅ Migration Database
File: `user_profile_fields_migration.sql`

**Chạy SQL này trong phpMyAdmin:**
```sql
-- Thêm 3 cột mới vào bảng users
ALTER TABLE `users` 
ADD COLUMN `phone` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Số điện thoại người dùng' AFTER `email`;

ALTER TABLE `users` 
ADD COLUMN `address` TEXT NULL DEFAULT NULL COMMENT 'Địa chỉ người dùng' AFTER `phone`;

ALTER TABLE `users` 
ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT 'default-avatar.png' COMMENT 'Ảnh đại diện người dùng' AFTER `address`;

ALTER TABLE `users` 
ADD INDEX `idx_phone` (`phone`);
```

### 2. ✅ Cập nhật Model (`app/models/User.php`)
- Thêm method mới: `updateProfileExtended()` để xử lý cập nhật phone, address, avatar

### 3. ✅ Cập nhật Controller (`app/controllers/UsersController.php`)
- Method `profile()` đã được cập nhật để:
  - Xử lý upload avatar
  - Validate số điện thoại (10-11 số)
  - Validate kích thước ảnh (max 5MB)
  - Validate định dạng ảnh (JPG, JPEG, PNG, GIF)
  - Tự động xóa avatar cũ khi upload avatar mới
  - Tạo thư mục `public/uploads/avatars/` nếu chưa có

### 4. ✅ Cập nhật View (`app/views/users/profile.php`)
- Hiển thị preview avatar
- Form upload avatar với preview real-time
- Input số điện thoại với auto-format
- Textarea địa chỉ
- Icons đẹp mắt cho từng trường
- JavaScript validation
- CSS styling hiện đại

### 5. ✅ Tạo Avatar mặc định
- File: `public/img/default-avatar.svg`
- Ảnh mặc định sẽ được hiển thị nếu user chưa upload avatar

---

## 📂 Cấu trúc thư mục:

```
public/
├── img/
│   ├── default-avatar.svg    ← Avatar mặc định (SVG)
│   └── default-avatar.png    ← Avatar mặc định (PNG) - Tải về nếu cần
└── uploads/
    └── avatars/              ← Lưu avatar của users
        ├── avatar_1_1234567890.jpg
        ├── avatar_5_1234567891.png
        └── ...
```

---

## 🎨 Tính năng giao diện:

1. **Preview Avatar**: Xem trước ảnh ngay khi chọn file
2. **Validation Real-time**: 
   - Số điện thoại chỉ cho phép 10-11 số
   - Kích thước ảnh max 5MB
   - Định dạng: JPG, JPEG, PNG, GIF
3. **Responsive Design**: Hiển thị tốt trên mọi thiết bị
4. **Icons**: Sử dụng Font Awesome cho UX tốt hơn
5. **Hover Effects**: Animation khi hover vào avatar

---

## 🔒 Bảo mật:

- ✅ Validate định dạng file trước khi upload
- ✅ Kiểm tra kích thước file
- ✅ Tên file được đổi thành unique (tránh conflict)
- ✅ Xóa file cũ khi upload mới
- ✅ Sanitize input với `filter_input_array`
- ✅ Regex validation cho số điện thoại

---

## 🚀 Test các tính năng:

### Test 1: Upload Avatar
1. Đăng nhập vào `/users/profile`
2. Click "Thay đổi ảnh đại diện"
3. Chọn 1 file ảnh (JPG, PNG, GIF)
4. Xem preview ngay lập tức
5. Click "Cập nhật thông tin"
6. Avatar mới sẽ được lưu vào `public/uploads/avatars/`

### Test 2: Validation
1. Thử upload file > 5MB → Báo lỗi
2. Thử upload file .exe, .pdf → Báo lỗi
3. Nhập SĐT sai format (có chữ) → Tự động xóa chữ
4. Nhập SĐT > 11 số → Tự động giới hạn 11 số

### Test 3: Các trường không bắt buộc
1. Để trống Phone và Address → Vẫn update được
2. Chỉ nhập Phone → OK
3. Chỉ nhập Address → OK

---

## 📌 Lưu ý quan trọng:

1. **Avatar mặc định**: 
   - File SVG đã được tạo tại `public/img/default-avatar.svg`
   - Nếu cần file PNG, bạn có thể:
     - Tải ảnh avatar mặc định từ internet
     - Hoặc convert SVG sang PNG bằng tool online
     - Đặt tên file: `default-avatar.png`

2. **Permissions**:
   - Đảm bảo thư mục `public/uploads/avatars/` có quyền ghi (777 hoặc 755)
   
3. **Database**:
   - **QUAN TRỌNG**: Phải chạy file SQL migration trước khi test!
   - Nếu không có các cột mới, code sẽ báo lỗi

---

## 🐛 Troubleshooting:

### Lỗi: "Cannot upload avatar"
- Kiểm tra quyền thư mục `public/uploads/avatars/`
- Đảm bảo PHP có quyền ghi file
- Kiểm tra `upload_max_filesize` và `post_max_size` trong `php.ini`

### Lỗi: "Column not found"
- Chưa chạy migration SQL
- Chạy lại file `user_profile_fields_migration.sql`

### Avatar không hiển thị
- Kiểm tra đường dẫn file
- Đảm bảo có file `default-avatar.png` hoặc `.svg`
- Clear cache trình duyệt

---

## ✨ Tính năng tiếp theo có thể thêm:

- [ ] Crop avatar trước khi upload
- [ ] Upload nhiều ảnh cùng lúc
- [ ] Avatar gallery
- [ ] Thêm trường: Giới tính, Ngày sinh, Bio
- [ ] Social links (Facebook, Instagram, Twitter)
- [ ] Email verification
- [ ] Two-factor authentication (2FA)

---

**Hoàn thành!** 🎉

Tất cả các tính năng đã sẵn sàng để sử dụng. Nhớ chạy SQL migration trước khi test nhé!

