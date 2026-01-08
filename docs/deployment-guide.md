# Hướng dẫn Deploy Cloody Laravel

## 1. Deploy lên VPS (DigitalOcean, AWS, Vultr)

### Yêu cầu Server:
- Ubuntu 22.04 LTS
- PHP 8.4
- MySQL 8.0
- Nginx hoặc Apache
- Composer
- Node.js & NPM

### Các bước thực hiện:

#### Bước 1: Kết nối SSH và cài đặt môi trường
```bash
# Cập nhật hệ thống
sudo apt update && sudo apt upgrade -y

# Cài đặt PHP 8.4 và extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.4 php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring \
  php8.4-curl php8.4-zip php8.4-gd php8.4-bcmath php8.4-intl php8.4-redis

# Cài đặt Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Cài đặt Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Cài đặt MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Cài đặt Nginx
sudo apt install -y nginx
```

#### Bước 2: Clone project từ GitHub
```bash
cd /var/www
sudo git clone https://github.com/pqchien1905/Cloody_Laravel.git cloudbox
cd cloudbox
sudo chown -R www-data:www-data /var/www/cloudbox
sudo chmod -R 755 /var/www/cloudbox
```

#### Bước 3: Cấu hình dự án
```bash
# Copy file .env
sudo cp .env.example .env
sudo nano .env
```

Sửa các thông tin trong `.env`:
```env
APP_NAME="Cloody"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cloudbox_db
DB_USERNAME=cloudbox_user
DB_PASSWORD=your_strong_password

# Mail settings (Gmail example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=redis
SESSION_DRIVER=redis
```

#### Bước 4: Cài đặt dependencies
```bash
# Install PHP dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev

# Install Node dependencies và build
sudo npm ci
sudo npm run build

# Generate application key
sudo php artisan key:generate

# Create symbolic link for storage
sudo php artisan storage:link

# Run migrations
sudo php artisan migrate --force

# Seed storage plans (if needed)
sudo php artisan db:seed --class=StoragePlanSeeder

# Optimize Laravel
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo php artisan optimize
```

#### Bước 5: Tạo database và user
```bash
sudo mysql -u root -p
```

Trong MySQL console:
```sql
CREATE DATABASE cloudbox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cloudbox_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON cloudbox_db.* TO 'cloudbox_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Bước 6: Cấu hình Nginx
```bash
sudo nano /etc/nginx/sites-available/cloudbox
```

Nội dung file:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/cloudbox/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/cloudbox /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### Bước 7: Cài đặt SSL với Let's Encrypt
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### Bước 8: Cấu hình Queue Worker (Background Jobs)
```bash
sudo nano /etc/systemd/system/cloudbox-worker.service
```

Nội dung:
```ini
[Unit]
Description=Cloudbox Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/cloudbox/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable và start service:
```bash
sudo systemctl enable cloudbox-worker
sudo systemctl start cloudbox-worker
sudo systemctl status cloudbox-worker
```

#### Bước 9: Cấu hình Cron Job (Scheduled Tasks)
```bash
sudo crontab -e -u www-data
```

Thêm dòng:
```
* * * * * cd /var/www/cloudbox && php artisan schedule:run >> /dev/null 2>&1
```

#### Bước 10: Cấu hình Redis (Optional nhưng recommended)
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

---

## 2. Deploy lên Shared Hosting (Hostinger, cPanel)

### Các bước:

1. **Nén project:**
```bash
# Trên local machine
composer install --no-dev
npm run build
zip -r cloudbox.zip . -x "node_modules/*" -x ".git/*"
```

2. **Upload lên hosting:**
   - Upload file `cloudbox.zip` qua FTP hoặc File Manager
   - Giải nén vào thư mục public_html hoặc thư mục tương tự

3. **Di chuyển file public:**
   - Di chuyển nội dung thư mục `public/` ra ngoài root
   - Sửa file `index.php` để trỏ đúng path

4. **Cấu hình .env:**
   - Tạo database qua cPanel
   - Cập nhật thông tin database trong `.env`

5. **Chạy migration:**
```bash
php artisan migrate --force
```

---

## 3. Deploy lên Railway.app (Free tier)

### Các bước:

1. **Tạo tài khoản tại [Railway.app](https://railway.app)**

2. **Thêm file `Procfile`:**
```
web: php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
worker: php artisan queue:work --verbose --tries=3 --timeout=90
```

3. **Thêm file `nixpacks.toml`:**
```toml
[phases.setup]
nixPkgs = ['php84', 'nodejs-20_x']

[phases.install]
cmds = [
    'composer install --no-dev --optimize-autoloader',
    'npm ci',
    'npm run build'
]

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=$PORT'
```

4. **Deploy từ GitHub:**
   - New Project → Deploy from GitHub
   - Chọn repository `Cloody_Laravel`
   - Add MySQL service
   - Configure environment variables
   - Deploy!

---

## 4. Deploy lên Laravel Forge (Paid - Easiest)

1. **Tạo tài khoản [Laravel Forge](https://forge.laravel.com)**
2. **Connect server** (DigitalOcean, AWS, Linode)
3. **New Site** → nhập domain
4. **Install Repository** → connect GitHub
5. **Deploy Script** tự động được tạo
6. **Enable Quick Deploy** để auto-deploy khi push code

---

## Kiểm tra sau khi deploy:

```bash
# Kiểm tra permissions
sudo chown -R www-data:www-data /var/www/cloudbox
sudo chmod -R 755 /var/www/cloudbox
sudo chmod -R 775 /var/www/cloudbox/storage
sudo chmod -R 775 /var/www/cloudbox/bootstrap/cache

# Clear cache nếu có lỗi
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Kiểm tra logs
tail -f storage/logs/laravel.log
```

## Monitoring và Maintenance:

```bash
# Kiểm tra queue worker
sudo systemctl status cloudbox-worker

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
sudo systemctl restart cloudbox-worker

# Backup database
mysqldump -u cloudbox_user -p cloudbox_db > backup_$(date +%Y%m%d).sql

# Update application
cd /var/www/cloudbox
sudo git pull origin main
sudo composer install --no-dev
sudo npm run build
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo systemctl restart cloudbox-worker
```

---

**Lưu ý bảo mật:**
- ✅ Đặt `APP_DEBUG=false` trong production
- ✅ Dùng mật khẩu mạnh cho database
- ✅ Cài đặt SSL certificate
- ✅ Backup database thường xuyên
- ✅ Giới hạn file upload size phù hợp
- ✅ Cấu hình firewall (UFW)
- ✅ Cập nhật hệ thống thường xuyên
