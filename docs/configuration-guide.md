# Hướng Dẫn Cấu Hình CloudBox

## Cấu Hình File Upload

Thêm các biến sau vào file `.env`:

```env
# File Upload Settings
CLOUDBOX_MAX_FILE_SIZE=102400          # Kích thước file tối đa (KB). Mặc định: 102400 KB = 100MB
CLOUDBOX_MAX_TOTAL_SIZE=512000          # Tổng dung lượng upload trong một request (KB). Mặc định: 512000 KB = 500MB
CLOUDBOX_MAX_FILES_PER_REQUEST=50       # Số lượng file tối đa trong một request. Mặc định: 50
```

## Cấu Hình Storage

```env
# Storage Limits
CLOUDBOX_MAX_STORAGE_PER_USER=0         # Dung lượng lưu trữ tối đa cho mỗi user (MB). 0 = không giới hạn
CLOUDBOX_MAX_STORAGE_TOTAL=0            # Dung lượng lưu trữ tối đa cho toàn hệ thống (GB). 0 = không giới hạn
```

**Ví dụ:**
- Giới hạn mỗi user 5GB: `CLOUDBOX_MAX_STORAGE_PER_USER=5120`
- Giới hạn hệ thống 100GB: `CLOUDBOX_MAX_STORAGE_TOTAL=100`

## Cấu Hình File Validation

### Whitelist (Chỉ cho phép các extension/MIME type này)

```env
# Chỉ cho phép các extension này (phân cách bằng dấu phẩy)
CLOUDBOX_ALLOWED_EXTENSIONS=pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif

# Chỉ cho phép các MIME type này (phân cách bằng dấu phẩy)
CLOUDBOX_ALLOWED_MIME_TYPES=application/pdf,image/jpeg,image/png
```

### Blacklist (Không cho phép các extension/MIME type này)

```env
# Không cho phép các extension này (phân cách bằng dấu phẩy)
CLOUDBOX_BLOCKED_EXTENSIONS=exe,bat,cmd,com,scr,vbs,js,jar,app

# Không cho phép các MIME type này (phân cách bằng dấu phẩy)
CLOUDBOX_BLOCKED_MIME_TYPES=application/x-msdownload,application/x-executable
```

**Lưu ý:**
- Để trống = cho phép tất cả (trừ blacklist)
- Blacklist có ưu tiên cao hơn whitelist
- Mặc định blacklist các file executable (.exe, .bat, .cmd, etc.)

## Cấu Hình Rate Limiting

```env
# Rate Limiting cho Upload
CLOUDBOX_UPLOAD_RATE_LIMIT=10           # Số request upload tối đa trong một phút. Mặc định: 10
CLOUDBOX_UPLOAD_RATE_LIMIT_HOUR=100     # Số request upload tối đa trong một giờ. Mặc định: 100
```

## Cấu Hình Queue (Cho Email Notification)

Để sử dụng queue cho email notification, cấu hình trong `.env`:

```env
QUEUE_CONNECTION=database
```

Sau đó chạy migration và queue worker:

```bash
php artisan migrate
php artisan queue:work
```

Hoặc sử dụng supervisor để chạy queue worker tự động (production).

## Cấu Hình Email

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Ví Dụ Cấu Hình Đầy Đủ

```env
# File Upload
CLOUDBOX_MAX_FILE_SIZE=51200            # 50MB
CLOUDBOX_MAX_TOTAL_SIZE=256000         # 250MB
CLOUDBOX_MAX_FILES_PER_REQUEST=20

# Storage
CLOUDBOX_MAX_STORAGE_PER_USER=10240    # 10GB per user
CLOUDBOX_MAX_STORAGE_TOTAL=500         # 500GB total

# File Validation
CLOUDBOX_BLOCKED_EXTENSIONS=exe,bat,cmd,com,scr,vbs,js,jar,app,sh

# Rate Limiting
CLOUDBOX_UPLOAD_RATE_LIMIT=5
CLOUDBOX_UPLOAD_RATE_LIMIT_HOUR=50

# Queue
QUEUE_CONNECTION=database
```

## Lưu Ý

1. Sau khi thay đổi config, chạy: `php artisan config:clear`
2. Đảm bảo `php.ini` có cấu hình phù hợp:
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   memory_limit = 256M
   ```
3. Rate limiting sử dụng Laravel RateLimiter, lưu trong cache
4. Storage limits được kiểm tra trước khi upload, file sẽ bị từ chối nếu vượt quá

