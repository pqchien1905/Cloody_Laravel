# Hướng dẫn cấu hình VNPay

## Vấn đề: Lỗi "Không tìm thấy website" (Mã 72)

Lỗi này xảy ra khi VNPay không thể truy cập được URL callback của bạn. VNPay không thể truy cập `localhost` hoặc `127.0.0.1` từ server của họ.

## Giải pháp

### 1. Sử dụng Ngrok để test (Khuyến nghị cho development)

1. **Cài đặt Ngrok:**
   - Tải từ: https://ngrok.com/download
   - Hoặc dùng: `choco install ngrok` (Windows)

2. **Chạy Ngrok:**
   ```bash
   ngrok http 8000
   ```

3. **Lấy URL public từ Ngrok:**
   - Sẽ có dạng: `https://xxxx-xx-xx-xx-xx.ngrok-free.app`
   - Copy URL này

4. **Cập nhật file `.env`:**
   
   Mở file `.env` và cập nhật các dòng sau:
   ```env
   APP_URL=https://1fbf17e79203.ngrok-free.app
   VNPAY_RETURN_URL=https://1fbf17e79203.ngrok-free.app/cloody/payment/callback
   VNPAY_IPN_URL=https://1fbf17e79203.ngrok-free.app/cloody/payment/ipn
   ```
   
   **Lưu ý:** URL Ngrok sẽ thay đổi mỗi lần bạn restart ngrok (trừ khi dùng plan có trả phí). Nếu restart ngrok, bạn cần cập nhật lại URL trong `.env` và chạy `php artisan config:clear`.

5. **Cấu hình trong VNPay Merchant Portal:**
   - Đăng nhập vào https://sandbox.vnpayment.vn/merchantv2/
   - Vào phần cấu hình
   - Thêm URL callback: `https://1fbf17e79203.ngrok-free.app/cloody/payment/callback`
   - Thêm IPN URL: `https://1fbf17e79203.ngrok-free.app/cloody/payment/ipn`
   
   **Lưu ý:** Nếu restart ngrok và có URL mới, bạn cũng cần cập nhật lại trong VNPay Merchant Portal.

### 2. Cấu hình VNPay Sandbox

1. **Đăng ký tài khoản VNPay Sandbox:**
   - Truy cập: https://sandbox.vnpayment.vn/
   - Đăng ký tài khoản merchant

2. **Lấy thông tin:**
   - `TMN Code`: Mã merchant
   - `Hash Secret`: Mã bảo mật

3. **Cập nhật file `.env`:**
   ```env
   VNPAY_TMN_CODE=K0IW5148
   VNPAY_HASH_SECRET=8O8JKVI95D7SAB36CHGTSSV16ULY7JHN
   VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
   ```
   
   **Thông tin đã nhận từ VNPay:**
   - TMN Code: `K0IW5148`
   - Hash Secret: `8O8JKVI95D7SAB36CHGTSSV16ULY7JHN` (đã cập nhật mới)
   - URL: `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html`
   
   **Lưu ý:** Nếu VNPay cấp Hash Secret mới, bạn cần cập nhật lại trong `.env` và chạy `php artisan config:clear`.

### 3. Cấu hình cho Production

Khi deploy lên server thật:

1. **Cập nhật `.env`:**
   ```env
   APP_URL=https://yourdomain.com
   VNPAY_URL=https://www.vnpayment.vn/paymentv2/vpcpay.html
   VNPAY_RETURN_URL=https://yourdomain.com/cloody/payment/callback
   VNPAY_IPN_URL=https://yourdomain.com/cloody/payment/ipn
   ```

2. **Cấu hình trong VNPay Production Portal:**
   - Đăng nhập vào https://merchant.vnpayment.vn/
   - Thêm domain và URL callback vào whitelist

## Kiểm tra cấu hình

Sau khi cấu hình, kiểm tra:

1. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

2. **Kiểm tra log:**
   - Xem file `storage/logs/laravel.log` để xem URL được tạo
   - Đảm bảo URL không có trailing slash
   - Đảm bảo URL là HTTPS (VNPay yêu cầu HTTPS cho production)

## Lưu ý quan trọng

- **Localhost không hoạt động:** VNPay không thể truy cập `localhost` hoặc `127.0.0.1`
- **HTTPS bắt buộc:** Production phải dùng HTTPS
- **URL phải public:** URL callback phải có thể truy cập từ internet
- **Không có trailing slash:** URL không được kết thúc bằng `/`
- **Cấu hình trong VNPay Portal:** Phải thêm URL vào whitelist trong merchant portal

## Test với thẻ test

VNPay cung cấp thẻ test:
- **Ngân hàng:** NCB
- **Số thẻ:** 9704198526191432198
- **Tên chủ thẻ:** NGUYEN VAN A
- **Ngày phát hành:** 07/15
- **Mật khẩu OTP:** 123456

## Thông tin Merchant Admin

- **Địa chỉ:** https://sandbox.vnpayment.vn/merchantv2/
- **Tên đăng nhập:** pqchien1905@gmail.com
- **Mật khẩu:** (Mật khẩu bạn đã đăng ký)

## Test Case (SIT)

- **Địa chỉ:** https://sandbox.vnpayment.vn/vnpaygw-sit-testing/user/login
- **Tên đăng nhập:** pqchien1905@gmail.com
- **Mật khẩu:** (Mật khẩu bạn đã đăng ký)

## Tài liệu tham khảo

- **Tài liệu tích hợp:** https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
- **Code demo:** https://sandbox.vnpayment.vn/apis/vnpay-demo/code-demo-tích-hợp
- **Demo trải nghiệm:** https://sandbox.vnpayment.vn/apis/vnpay-demo/

