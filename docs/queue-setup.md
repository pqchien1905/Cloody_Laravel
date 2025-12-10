# Hướng Dẫn Setup Queue Worker

## Queue đã được cấu hình

File `.env` đã có:
```env
QUEUE_CONNECTION=database
```

Bảng `jobs` đã được tạo trong database (migration `0001_01_01_000002_create_jobs_table`).

## Chạy Queue Worker

### Development (Local)

Chạy queue worker trong terminal riêng:

```bash
php artisan queue:work
```

Hoặc với các tùy chọn:

```bash
# Với retry và timeout
php artisan queue:work --tries=3 --timeout=60

# Chỉ xử lý queue cụ thể
php artisan queue:work --queue=default

# Xử lý một job rồi dừng (để test)
php artisan queue:work --once
```

### Production

#### Option 1: Supervisor (Khuyến nghị)

Tạo file `/etc/supervisor/conf.d/cloudbox-worker.conf`:

```ini
[program:cloudbox-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/cloudbox-laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/cloudbox-laravel/storage/logs/worker.log
stopwaitsecs=3600
```

Sau đó:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cloudbox-worker:*
```

#### Option 2: Systemd (Linux)

Tạo file `/etc/systemd/system/cloudbox-worker.service`:

```ini
[Unit]
Description=CloudBox Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/cloudbox-laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Sau đó:
```bash
sudo systemctl daemon-reload
sudo systemctl enable cloudbox-worker
sudo systemctl start cloudbox-worker
```

#### Option 3: Windows Service

Sử dụng NSSM (Non-Sucking Service Manager):

1. Download NSSM: https://nssm.cc/download
2. Cài đặt service:
```cmd
nssm install CloudBoxWorker "C:\path\to\php.exe" "C:\path\to\cloudbox-laravel\artisan queue:work"
```

## Kiểm Tra Queue

### Xem jobs đang chờ

```bash
php artisan queue:monitor
```

### Xem failed jobs

```bash
php artisan queue:failed
```

### Retry failed jobs

```bash
# Retry tất cả
php artisan queue:retry all

# Retry job cụ thể
php artisan queue:retry {job-id}
```

### Xóa failed jobs

```bash
# Xóa tất cả
php artisan queue:flush

# Xóa job cụ thể
php artisan queue:forget {job-id}
```

## Test Queue

1. Chia sẻ một file với user khác
2. Kiểm tra bảng `jobs` trong database:
```sql
SELECT * FROM jobs;
```
3. Queue worker sẽ xử lý và gửi email

## Logs

Queue worker logs sẽ được ghi vào:
- `storage/logs/laravel.log` (nếu dùng log driver)
- Hoặc file log được cấu hình trong supervisor/systemd

## Troubleshooting

### Queue không chạy

1. Kiểm tra `QUEUE_CONNECTION` trong `.env`
2. Kiểm tra database connection
3. Kiểm tra bảng `jobs` đã tồn tại chưa:
```bash
php artisan migrate:status
```

### Jobs bị stuck

1. Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
```

2. Restart queue worker

3. Kiểm tra failed jobs:
```bash
php artisan queue:failed
```

### Email không được gửi

1. Kiểm tra cấu hình email trong `.env`
2. Kiểm tra queue worker đang chạy
3. Kiểm tra logs:
```bash
tail -f storage/logs/laravel.log
```

## Lưu Ý

- Queue worker cần chạy liên tục để xử lý jobs
- Trong development, có thể chạy `php artisan queue:work` trong terminal riêng
- Trong production, nên dùng supervisor hoặc systemd để tự động restart khi crash
- Nên có ít nhất 2 worker processes để xử lý song song

