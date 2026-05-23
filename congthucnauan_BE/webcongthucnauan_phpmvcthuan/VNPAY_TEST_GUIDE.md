# 🏦 HƯỚNG DẪN TEST VNPAY SANDBOX

## 📋 Thông tin tài khoản VNPay Sandbox

### Thông tin cấu hình (Đã tích hợp vào code)
- **Terminal ID / Mã Website**: `TGQ78RUP`
- **Secret Key**: `1D4XCMDC467EABY7KFV8MH8OYOY2HN4Y`
- **URL thanh toán**: https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

### Thông tin đăng nhập Merchant Admin
- **Địa chỉ**: https://sandbox.vnpayment.vn/merchantv2/
- **Tên đăng nhập**: volan@vothaihonglan.net
- **Mật khẩu**: (Mật khẩu bạn đã đăng ký)

### Kịch bản test (SIT)
- **Địa chỉ**: https://sandbox.vnpayment.vn/vnpaygw-sit-testing/user/login
- **Tên đăng nhập**: volan@vothaihonglan.net
- **Mật khẩu**: (Mật khẩu bạn đã đăng ký)

---

## 💳 Thẻ test

### Ngân hàng NCB
- **Số thẻ**: `9704198526191432198`
- **Tên chủ thẻ**: `NGUYEN VAN A`
- **Ngày phát hành**: `07/15`
- **Mật khẩu OTP**: `123456`

---

## 🚀 Cách test thanh toán

### Bước 1: Đăng ký URL Return
1. Đăng nhập vào: https://sandbox.vnpayment.vn/merchantv2/
2. Vào phần **Cấu hình** → **Cấu hình Return URL**
3. Thêm URL: `http://localhost/webcongthucnauan/payments/vnpay_return`
4. Lưu cấu hình

### Bước 2: Test thanh toán
1. Truy cập: http://localhost/webcongthucnauan/payments/checkout
2. Chọn số tiền (50,000₫ / 100,000₫ / 200,000₫)
3. Chọn phương thức thanh toán: **VNPay**
4. Nhấn **Xác nhận thanh toán**
5. Tại trang VNPay:
   - Chọn ngân hàng: **NCB**
   - Nhập số thẻ: `9704198526191432198`
   - Nhập tên: `NGUYEN VAN A`
   - Nhập ngày phát hành: `07/15`
   - Nhập OTP: `123456`
6. Xác nhận thanh toán
7. Hệ thống sẽ tự động quay về trang web

---

## 📚 Tài liệu tham khảo

- **Hướng dẫn tích hợp**: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
- **Code demo**: https://sandbox.vnpayment.vn/apis/vnpay-demo/code-demo-tích-hợp

---

## ⚠️ Lưu ý quan trọng

1. **Môi trường Sandbox**: Đây là môi trường test, KHÔNG sử dụng cho khách hàng thanh toán thật
2. **IPN URL**: Cần tạo và gửi cho VNPay để cập nhật trạng thái thanh toán (server to server)
3. **Return URL**: Phải đăng ký chính xác trong Merchant Admin, nếu không sẽ bị lỗi code=72
4. **Production**: Khi lên production, cần thay thông tin thực tế từ VNPay

---

## 🔧 Troubleshooting

### Lỗi code=72: Không tìm thấy website
- **Nguyên nhân**: URL return chưa được đăng ký trong VNPay
- **Giải pháp**: Đăng nhập Merchant Admin và đăng ký URL return

### Lỗi checksum không hợp lệ
- **Nguyên nhân**: Secret Key không đúng
- **Giải pháp**: Kiểm tra lại Secret Key trong code

### Không redirect về website
- **Nguyên nhân**: URL return không đúng hoặc chưa xử lý callback
- **Giải pháp**: Kiểm tra route `/payments/vnpay_return` đã được tạo chưa
