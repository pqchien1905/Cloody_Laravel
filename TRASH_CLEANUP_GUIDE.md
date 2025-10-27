# CloudBOX - Hướng Dẫn Tự Động Xóa Trash

## Tính năng

Hệ thống tự động xóa files và folders trong Trash sau **30 ngày**:

- ✅ Files/folders khi xóa sẽ chuyển vào **Trash**
- ✅ Hiển thị số ngày còn lại trước khi bị xóa vĩnh viễn
- ✅ Có thể **Restore** (khôi phục) từ Trash
- ✅ Có thể **Delete Permanently** (xóa vĩnh viễn) thủ công
- ✅ **Tự động xóa** sau 30 ngày bằng scheduled task

## Cách hoạt động

### 1. Xóa File/Folder
Khi xóa file hoặc folder:
- Không bị xóa ngay lập tức
- Được đánh dấu `is_trash = true` 
- Lưu thời gian xóa vào `trashed_at`
- Chuyển vào trang **Trash**

### 2. Hiển thị trong Trash
Trang Trash (`/cloudbox/trash`) hiển thị:
- Danh sách folders đã xóa
- Danh sách files đã xóa
- Ngày xóa
- **Số ngày còn lại** trước khi bị xóa vĩnh viễn:
  - 🟢 Xanh: > 14 ngày
  - 🟡 Vàng: 8-14 ngày
  - 🔴 Đỏ: ≤ 7 ngày

### 3. Restore (Khôi phục)
- Click nút **Restore** để khôi phục file/folder
- File/folder sẽ quay lại vị trí ban đầu
- Đặt lại `is_trash = false` và `trashed_at = null`

### 4. Delete Permanently (Xóa vĩnh viễn)
- Click nút **Delete Permanently** để xóa ngay
- File vật lý sẽ bị xóa khỏi storage
- Record trong database bị xóa
- **Không thể khôi phục**

### 5. Tự động xóa sau 30 ngày
Command `trash:cleanup` chạy tự động:
- **Thời gian**: Mỗi ngày lúc **2:00 AM**
- **Chức năng**: Tìm và xóa vĩnh viễn items có `trashed_at` > 30 ngày
- **Xử lý**:
  - Xóa file vật lý khỏi storage
  - Xóa record khỏi database
  - Xóa đệ quy cho folders (cả subfolder và files bên trong)

## Cách chạy Scheduler

### Development (Local)

**Option 1: Chạy thủ công mỗi phút**
```bash
php artisan schedule:work
```

**Option 2: Test ngay command**
```bash
php artisan trash:cleanup
```

### Production (Server)

**1. Thêm Cron Job trên Linux/Mac:**
```bash
crontab -e
```

Thêm dòng này:
```bash
* * * * * cd /path/to/cloudbox-laravel && php artisan schedule:run >> /dev/null 2>&1
```

**2. Trên Windows Server:**
Tạo Task Scheduler:
- Command: `php`
- Arguments: `C:\path\to\cloudbox-laravel\artisan schedule:run`
- Schedule: Chạy mỗi phút

**3. Sử dụng Supervisor (Recommended):**

Tạo file `/etc/supervisor/conf.d/cloudbox-scheduler.conf`:
```ini
[program:cloudbox-scheduler]
process_name=%(program_name)s
command=php /path/to/cloudbox-laravel/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/cloudbox-scheduler.log
```

Reload Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cloudbox-scheduler
```

## Kiểm tra Scheduler

**Xem danh sách scheduled tasks:**
```bash
php artisan schedule:list
```

**Test chạy thủ công:**
```bash
php artisan trash:cleanup
```

Output:
```
Starting trash cleanup...
Cleanup completed!
Deleted 5 files and 2 folders.
```

## Database Schema

### Files Table
```sql
is_trash BOOLEAN DEFAULT FALSE
trashed_at TIMESTAMP NULL
```

### Folders Table
```sql
is_trash BOOLEAN DEFAULT FALSE
trashed_at TIMESTAMP NULL
```

## Routes

```php
// Trash
GET  /cloudbox/trash                    - Xem trash
POST /cloudbox/files/{id}/restore       - Restore file
POST /cloudbox/folders/{id}/restore     - Restore folder
DELETE /cloudbox/files/{id}/force       - Xóa vĩnh viễn file
DELETE /cloudbox/folders/{id}/force     - Xóa vĩnh viễn folder
```

## Logic Xóa Đệ Quy (Folders)

Khi xóa folder:
1. Xóa tất cả **files** trong folder → trash
2. Xóa tất cả **subfolders** đệ quy → trash
3. Xóa tất cả **files trong subfolders** → trash
4. Đánh dấu folder chính → trash

Khi restore folder:
1. Restore folder chính
2. Restore tất cả subfolders đệ quy
3. Restore tất cả files trong folder và subfolders

Khi force delete folder:
1. Xóa file vật lý của tất cả files
2. Xóa records của tất cả files
3. Xóa tất cả subfolders đệ quy
4. Xóa record của folder chính

## Lưu ý

⚠️ **Quan trọng:**
- Scheduler cần chạy liên tục để tự động xóa
- Trên production, **bắt buộc** phải setup cron job hoặc supervisor
- Backup database định kỳ để tránh mất dữ liệu
- Test kỹ trên local trước khi deploy production

💡 **Tips:**
- Người dùng nên được thông báo trước khi items bị xóa vĩnh viễn (có thể gửi email)
- Có thể tùy chỉnh số ngày từ 30 sang giá trị khác trong Command
- Có thể thêm tính năng "Empty Trash" để xóa tất cả ngay

## Tùy chỉnh số ngày

Để thay đổi từ 30 ngày sang giá trị khác:

**File:** `app/Console/Commands/CleanupOldTrashItems.php`
```php
// Thay đổi dòng này:
$thirtyDaysAgo = now()->subDays(30);  // Đổi 30 thành số ngày mong muốn
```

**File:** `resources/views/pages/trash.blade.php`
```php
// Thay đổi công thức tính ngày còn lại:
$daysRemaining = $folder->trashed_at ? 30 - $folder->trashed_at->diffInDays(now()) : 30;
```

## Support

Nếu có vấn đề:
1. Check logs: `storage/logs/laravel.log`
2. Test command thủ công: `php artisan trash:cleanup`
3. Verify scheduler: `php artisan schedule:list`
4. Check database: Xem records có `is_trash = true` và `trashed_at` cũ hơn 30 ngày
