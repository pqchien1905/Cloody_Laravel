# Hướng Dẫn Tăng Giới Hạn Upload

Bạn đang gặp lỗi **"Content Too Large"** vì folder upload vượt quá 40MB (giới hạn hiện tại).

## Giải Pháp: Tăng Giới Hạn PHP

### Bước 1: Tìm File php.ini

File php.ini của bạn nằm tại:
```
C:\xampp\php\php.ini
```

Hoặc nếu dùng Laragon:
```
C:\laragon\bin\php\php8.2.12\php.ini
```

### Bước 2: Chỉnh Sửa php.ini

Mở file `php.ini` bằng Notepad++ hoặc text editor, tìm và thay đổi các giá trị sau:

```ini
; Tìm và thay đổi các dòng này:
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

**Giải thích:**
- `upload_max_filesize`: Kích thước tối đa của MỘT file (500MB)
- `post_max_size`: Kích thước tối đa của TOÀN BỘ POST request (500MB)
- `max_execution_time`: Thời gian tối đa script chạy (300 giây = 5 phút)
- `max_input_time`: Thời gian tối đa nhận input (300 giây)
- `memory_limit`: Bộ nhớ tối đa PHP có thể dùng (512MB)

### Bước 3: Khởi Động Lại Server

**Nếu dùng Laragon:**
1. Mở Laragon
2. Click "Stop All"
3. Click "Start All"

**Nếu dùng XAMPP:**
1. Mở XAMPP Control Panel
2. Stop Apache
3. Start Apache

**Nếu dùng `php artisan serve`:**
1. Nhấn `Ctrl+C` để dừng server
2. Chạy lại: `php artisan serve`

### Bước 4: Kiểm Tra

Chạy lệnh sau để kiểm tra cấu hình mới:

```bash
php -i | findstr "post_max_size"
php -i | findstr "upload_max_filesize"
```

Kết quả phải hiển thị 500M.

### Bước 5: Thử Upload Lại

Quay lại trang Folders và thử upload folder của bạn lại.

---

## Nếu Vẫn Bị Lỗi

Nếu vẫn gặp lỗi sau khi tăng giới hạn, có thể do:

1. **Web server (Apache/Nginx) cũng có giới hạn riêng**
   - Apache: Kiểm tra `.htaccess` hoặc `httpd.conf`
   - Nginx: Kiểm tra `client_max_body_size` trong `nginx.conf`

2. **Folder quá lớn**
   - Thử upload folder nhỏ hơn (<500MB)
   - Hoặc chia nhỏ folder thành nhiều phần

3. **Thử upload từng file thay vì cả folder**
   - Sử dụng tính năng "Upload File" thông thường

---

## Giải Pháp Tạm Thời (Nếu Không Thể Sửa php.ini)

Nếu bạn không có quyền sửa `php.ini` (ví dụ trên shared hosting), bạn có thể:

1. **Upload folder nhỏ hơn** (<40MB)
2. **Chia folder thành nhiều phần** và upload từng phần
3. **Upload từng file riêng lẻ** thay vì cả folder

---

**Sau khi thay đổi, nhớ khởi động lại server để áp dụng cấu hình mới!**
