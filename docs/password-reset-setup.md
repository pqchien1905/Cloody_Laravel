# Hướng dẫn cấu hình Quên mật khẩu qua Email

## Tổng quan

Chức năng quên mật khẩu đã được tích hợp sẵn trong ứng dụng Laravel. Để sử dụng, bạn cần cấu hình mail server.

## Cấu hình Mail

### 1. Cấu hình trong file `.env`

Thêm các cấu hình sau vào file `.env` của bạn:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# App URL (Quan trọng cho reset password link)
APP_URL=http://localhost:8000
```

### 2. Các Mailer phổ biến

#### Gmail (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # Sử dụng App Password, không phải mật khẩu thường
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Cloody"
```

**Lưu ý**: Với Gmail, bạn cần:
1. Bật 2-Factor Authentication
2. Tạo App Password tại: https://myaccount.google.com/apppasswords

#### Mailtrap (Testing - Khuyến nghị cho development)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@cloody.com
MAIL_FROM_NAME="Cloody"
```

Đăng ký miễn phí tại: https://mailtrap.io

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Cloody"
```

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Cloody"
```

### 3. Development - Sử dụng Log Mailer

Nếu bạn đang ở môi trường development và chưa muốn cấu hình SMTP, có thể sử dụng log mailer:

```env
MAIL_MAILER=log
```

Email sẽ được ghi vào file `storage/logs/laravel.log` thay vì gửi thực sự. Link reset password vẫn có thể lấy từ log.

### 4. Test Mail Configuration

Sau khi cấu hình, test bằng lệnh:

```bash
php artisan tinker
```

Trong tinker:
```php
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});
```

## Sử dụng tính năng Quên mật khẩu

### 1. Người dùng

1. Vào trang đăng nhập: `/login`
2. Click vào link "Quên mật khẩu?"
3. Nhập email của tài khoản
4. Click "Gửi liên kết đặt lại"
5. Kiểm tra email và click vào link reset password
6. Nhập mật khẩu mới
7. Đăng nhập với mật khẩu mới

### 2. Routes

- `/forgot-password` - Form yêu cầu reset password
- `/reset-password/{token}` - Form đặt lại mật khẩu mới

### 3. Kiểm tra Database

Laravel lưu token reset password trong bảng `password_reset_tokens`. Bạn có thể kiểm tra:

```sql
SELECT * FROM password_reset_tokens;
```

## Troubleshooting

### Email không được gửi

1. Kiểm tra cấu hình `.env`
2. Kiểm tra log: `storage/logs/laravel.log`
3. Test mail connection với `php artisan tinker`
4. Kiểm tra firewall/antivirus có chặn port 587/465 không

### Link reset password không hoạt động

1. Kiểm tra `APP_URL` trong `.env` phải đúng với domain của bạn
2. Token có thể đã hết hạn (mặc định 60 phút)
3. Kiểm tra trong database xem token có tồn tại không

### Lỗi 419 (CSRF Token Mismatch)

1. Clear cache: `php artisan config:clear`
2. Clear cache: `php artisan cache:clear`
3. Restart server

## Tùy chỉnh Email Template

Email reset password sử dụng template mặc định của Laravel. Để tùy chỉnh:

1. Tạo notification class:
```bash
php artisan make:notification CustomResetPassword
```

2. Trong `User` model, thêm method:
```php
public function sendPasswordResetNotification($token)
{
    $this->notify(new CustomResetPassword($token));
}
```

## Bảo mật

1. Token reset password có thời hạn (mặc định 60 phút)
2. Token chỉ được sử dụng 1 lần
3. Rate limiting được áp dụng để tránh spam
4. Sử dụng HTTPS trong production

