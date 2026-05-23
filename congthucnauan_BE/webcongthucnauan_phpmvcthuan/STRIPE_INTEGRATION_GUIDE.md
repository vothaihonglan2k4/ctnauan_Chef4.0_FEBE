# 🎉 Hướng dẫn tích hợp Stripe Payment Gateway

## ✅ Đã hoàn thành!

Stripe Payment Gateway đã được tích hợp thành công vào hệ thống!

## 🎯 Tính năng

- ✅ Thanh toán qua thẻ Visa/Mastercard/AmEx
- ✅ Test dễ dàng với test cards
- ✅ Hỗ trợ thanh toán gói dịch vụ
- ✅ Hỗ trợ thanh toán khóa học
- ✅ Tự động cập nhật trạng thái
- ✅ Redirect tự động sau thanh toán

## 🚀 Cách test NHANH

### Bước 1: Vào trang thanh toán

**Thanh toán gói:**
```
http://localhost/webcongthucnauan/payments/checkout
```

**Thanh toán khóa học:**
```
http://localhost/webcongthucnauan/courses/show/1
→ Click "Đăng ký khóa học"
```

### Bước 2: Chọn Stripe

- Chọn **"Stripe - Thẻ Visa/Mastercard"**
- Nhấn **"Xác nhận thanh toán"**

### Bước 3: Nhập thẻ test

Tại trang Stripe checkout, nhập:

**✅ Thanh toán thành công:**
```
Số thẻ:  4242 4242 4242 4242
Ngày:    12/34 (bất kỳ tương lai)
CVC:     123 (bất kỳ 3 số)
Tên:     Bất kỳ
ZIP:     Bất kỳ
```

**Nhấn "Pay" → Xong! ✅**

## 💳 Danh sách Test Cards đầy đủ

### ✅ Thẻ thành công

| Loại thẻ | Số thẻ | Mô tả |
|-----------|---------|--------|
| **Visa** | 4242424242424242 | Thành công chuẩn |
| **Visa (debit)** | 4000056655665556 | Visa debit |
| **Mastercard** | 5555555555554444 | Thành công chuẩn |
| **Mastercard (2-series)** | 2223003122003222 | Mastercard mới |
| **Mastercard (debit)** | 5200828282828210 | Mastercard debit |
| **American Express** | 378282246310005 | AmEx |
| **Discover** | 6011111111111117 | Discover |
| **Diners Club** | 3056930009020004 | Diners |
| **JCB** | 3566002020360505 | JCB |

### ❌ Thẻ bị từ chối (để test lỗi)

| Số thẻ | Lỗi |
|---------|------|
| 4000000000000002 | Card declined |
| 4000000000009995 | Insufficient funds |
| 4000000000009987 | Lost card |
| 4000000000009979 | Stolen card |
| 4000000000000069 | Expired card |
| 4000000000000127 | Incorrect CVC |
| 4000000000000119 | Processing error |

### 🔒 Thẻ yêu cầu xác thực 3D Secure

| Số thẻ | Mô tả |
|---------|--------|
| 4000002500003155 | Yêu cầu xác thực - Complete |
| 4000002760003184 | Yêu cầu xác thực - Fail |

Khi test với thẻ 3D Secure:
- Tại trang xác thực, nhấn **"Complete"** để thành công
- Hoặc **"Fail"** để test thất bại

## 🔧 Cấu hình Stripe

### Môi trường Test (Hiện tại)

File: `app/helpers/StripePayment.php`

```php
// Test Keys - Public trên git (an toàn)
$this->publishableKey = "pk_test_51QR...";
$this->secretKey = "sk_test_51QR...";
```

**Lưu ý:** Keys hiện tại là ví dụ. Để test thật:

1. **Đăng ký Stripe account:**
   - Truy cập: https://dashboard.stripe.com/register
   - Đăng ký miễn phí

2. **Lấy API Keys:**
   - Vào Dashboard → Developers → API keys
   - Copy **Publishable key** và **Secret key**
   - Paste vào `app/helpers/StripePayment.php`

3. **Test ngay:**
   - Không cần verify email
   - Không cần thông tin ngân hàng
   - Test miễn phí không giới hạn!

### Chuyển sang Production

1. **Activate account:**
   - Trong Stripe Dashboard
   - Cung cấp thông tin doanh nghiệp
   - Verify identity

2. **Lấy Live Keys:**
   - Toggle từ "Test mode" → "Live mode"
   - Copy live keys

3. **Update code:**
```php
$this->publishableKey = "pk_live_..."; // Live key
$this->secretKey = "sk_live_..."; // Live secret
```

4. **Cấu hình Webhook (Optional nhưng recommended):**
   - URL: `https://yoursite.com/payments/stripe_webhook`
   - Events: `checkout.session.completed`, `payment_intent.succeeded`

## 📊 Flow thanh toán

```
User vào trang checkout
  ↓
Chọn Stripe + Submit
  ↓
Server tạo Checkout Session
  ↓
Redirect đến Stripe Checkout Page
  ↓
User nhập thẻ test
  ↓
Stripe xử lý thanh toán
  ↓
Redirect về /payments/stripe_success
  ↓
Server verify + update database
  ↓
Redirect về /payments (hoặc /courses/my_courses)
  ↓
✅ Hoàn tất!
```

## 🎨 UI/UX Features

- ✅ Stripe logo đẹp mắt
- ✅ Thông tin test card hiển thị rõ ràng
- ✅ Checkbox mặc định cho Stripe (khuyến nghị)
- ✅ Alert màu xanh với test card info
- ✅ Icon đầy đủ và chuyên nghiệp

## 🔐 Bảo mật

- ✅ **PCI Compliance:** Stripe xử lý tất cả thông tin thẻ
- ✅ **No sensitive data:** Server không lưu số thẻ
- ✅ **Signature verification:** Xác thực callback
- ✅ **HTTPS required:** (trong production)
- ✅ **Session-based:** Lưu trạng thái an toàn

## 🧪 Test Scenarios

### ✅ Test thanh toán thành công

1. Vào `/payments/checkout`
2. Chọn Stripe
3. Nhập: `4242 4242 4242 4242`
4. Submit
5. ✅ Thấy "Thanh toán thành công"

### ❌ Test thanh toán thất bại

1. Vào `/payments/checkout`
2. Chọn Stripe
3. Nhập: `4000 0000 0000 0002`
4. Submit
5. ❌ Thấy "Card declined"

### 🔒 Test 3D Secure

1. Vào `/payments/checkout`
2. Chọn Stripe
3. Nhập: `4000 0025 0000 3155`
4. Submit
5. Trang xác thực hiện ra
6. Nhấn "Complete"
7. ✅ Thanh toán thành công

### 🏃 Test hủy thanh toán

1. Vào `/payments/checkout`
2. Chọn Stripe
3. Tại trang Stripe, nhấn nút "Back" hoặc "Cancel"
4. ⬅️ Quay về trang checkout với thông báo "Đã hủy"

## 📝 Database Changes

Table `payments` đã hỗ trợ:
- `payment_method = 'stripe'`
- `status = 'pending'` khi tạo session
- `status = 'completed'` sau khi thanh toán
- `transaction_id` = Stripe session ID
- `gateway_transaction_id` = Payment Intent ID

## 🎯 So sánh với các gateway khác

| Feature | Stripe | MoMo | VNPay |
|---------|--------|------|-------|
| Test dễ | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| Thẻ quốc tế | ✅ | ❌ | ✅ |
| UI/UX | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| Phí | 3.4% + 10k | ~1% | ~2% |
| Setup | Dễ | Khó | Trung bình |
| Docs | Xuất sắc | Tạm | Tốt |

## 💡 Tips & Best Practices

1. **Test đầy đủ:**
   - Test cả success và decline
   - Test 3D Secure
   - Test cancel flow

2. **Error handling:**
   - Log tất cả errors
   - Show user-friendly messages
   - Retry mechanism

3. **Webhook:**
   - Luôn setup webhook trong production
   - Verify signature
   - Idempotency handling

4. **Security:**
   - Không bao giờ log secret keys
   - Dùng environment variables
   - HTTPS only trong production

## 🐛 Troubleshooting

### Lỗi: "Invalid API Key"

**Nguyên nhân:** Test keys chưa được thay thế

**Giải pháp:**
1. Đăng ký Stripe account
2. Lấy keys từ Dashboard
3. Update vào `StripePayment.php`

### Lỗi: "No such checkout session"

**Nguyên nhân:** Session đã hết hạn (24h)

**Giải pháp:** Tạo giao dịch mới

### Thanh toán thành công nhưng không update DB

**Nguyên nhân:** Callback URL không accessible

**Giải pháp:**
1. Check PHP errors log
2. Verify session active
3. Check database connection

## 📞 Support

- **Stripe Docs:** https://stripe.com/docs
- **Test Cards:** https://stripe.com/docs/testing
- **Dashboard:** https://dashboard.stripe.com

## 🎉 Kết luận

✅ **Tích hợp hoàn tất!**
✅ **Sẵn sàng test!**
✅ **Easy to use!**

**Happy Testing! 🚀**

---

**Cập nhật:** 03/11/2025  
**Phiên bản:** 1.0.0  
**Trạng thái:** ✅ Ready to test

