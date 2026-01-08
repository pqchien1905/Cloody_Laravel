# 🐳 Quick Start - Deploy Cloody với Docker

## ✅ Đã hoàn thành:
- ✅ Xóa thư mục `html/` (template cũ)
- ✅ Xóa các file Railway deployment không cần
- ✅ Tạo Dockerfile với PHP 8.3 + Nginx
- ✅ Tạo docker-compose.yml với MySQL + Redis
- ✅ Tạo .dockerignore
- ✅ Push lên GitHub

---

## 📋 Bước 1: Cài đặt Docker Desktop

### **Windows:**
1. Download: https://www.docker.com/products/docker-desktop/
2. Chạy installer và làm theo hướng dẫn
3. Restart máy tính
4. Mở Docker Desktop và đợi khởi động

### **Mac:**
1. Download: https://www.docker.com/products/docker-desktop/
2. Drag Docker vào Applications
3. Mở Docker Desktop

### **Linux (Ubuntu):**
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
newgrp docker
```

---

## 🚀 Bước 2: Deploy trên máy local (Testing)

### **2.1. Chuẩn bị .env**

```bash
# Copy file .env.example
copy .env.example .env

# Hoặc trên Mac/Linux:
cp .env.example .env
```

**Sửa `.env`:**
```env
APP_NAME=Cloody
APP_ENV=local
APP_DEBUG=true
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=cloudbox_db
DB_USERNAME=cloudbox_user
DB_PASSWORD=cloudbox_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

### **2.2. Generate APP_KEY**

**Cách 1 - Dùng PHP local (nếu có):**
```bash
php artisan key:generate --show
```

**Cách 2 - Generate online:**
```bash
# Truy cập: https://generate-random.org/laravel-key-generator
# Hoặc dùng: https://www.laravelkeygenerate.com/
```

Copy key vào `.env` (phần `APP_KEY=`)

### **2.3. Build và Start**

```bash
# Build Docker images
docker-compose build

# Start all services
docker-compose up -d

# Xem logs
docker-compose logs -f
```

**Đợi ~2-3 phút để build xong!**

### **2.4. Run Migrations**

```bash
# Chạy migrations
docker-compose exec app php artisan migrate --force

# (Optional) Seed storage plans
docker-compose exec app php artisan db:seed --class=StoragePlanSeeder
```

### **2.5. Tạo Admin User**

```bash
docker-compose exec app php artisan tinker
```

Trong tinker console, gõ:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@cloody.com';
$user->password = Hash::make('admin123');
$user->is_admin = true;
$user->save();
exit
```

### **2.6. Truy cập Website**

Mở trình duyệt: **http://localhost:8000**

Login với:
- Email: `admin@cloody.com`
- Password: `admin123`

---

## 🌐 Bước 3: Deploy lên Production VPS

### **3.1. Chuẩn bị VPS**

**Yêu cầu:**
- Ubuntu 22.04 hoặc 20.04
- 2GB RAM minimum (4GB recommended)
- 20GB disk space

**SSH vào VPS:**
```bash
ssh root@your-server-ip
```

### **3.2. Cài Docker trên VPS**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Verify
docker --version
docker compose version
```

### **3.3. Clone và Setup**

```bash
# Clone repository
cd /var/www
git clone https://github.com/pqchien1905/Cloody_Laravel.git cloudbox
cd cloudbox

# Setup .env
cp .env.example .env
nano .env
```

**Sửa `.env` cho production:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_PASSWORD=STRONG_PASSWORD_HERE

# Mail settings (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
```

### **3.4. Sửa docker-compose.yml cho Production**

```bash
nano docker-compose.yml
```

**Thay đổi ports:**
```yaml
services:
  app:
    ports:
      - "8000:80"  # Hoặc đổi thành 80:80 nếu không dùng Nginx proxy
```

**Thay mật khẩu database:**
```yaml
  mysql:
    environment:
      MYSQL_PASSWORD: STRONG_PASSWORD_HERE
      MYSQL_ROOT_PASSWORD: STRONG_ROOT_PASSWORD_HERE
```

### **3.5. Build và Deploy**

```bash
# Build
docker compose build

# Start
docker compose up -d

# Check logs
docker compose logs -f app

# Run migrations
docker compose exec app php artisan migrate --force

# Optimize
docker compose exec app php artisan optimize
```

### **3.6. Setup Nginx Reverse Proxy + SSL**

```bash
# Install Nginx
sudo apt install nginx certbot python3-certbot-nginx -y

# Create config
sudo nano /etc/nginx/sites-available/cloudbox
```

**Nội dung:**
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    location / {
        proxy_pass http://localhost:8000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    client_max_body_size 100M;
}
```

**Enable và SSL:**
```bash
sudo ln -s /etc/nginx/sites-available/cloudbox /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### **3.7. Auto-start on boot**

```bash
# Enable Docker service
sudo systemctl enable docker

# Restart Policy đã có trong docker-compose.yml:
# restart: unless-stopped
```

---

## 🔄 Update Application

```bash
cd /var/www/cloudbox

# Pull latest code
git pull origin main

# Rebuild
docker compose build app

# Restart
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force

# Clear cache
docker compose exec app php artisan optimize
```

---

## 🛠️ Các lệnh hữu ích

```bash
# Xem status
docker compose ps

# Xem logs
docker compose logs -f app
docker compose logs -f mysql

# Vào container
docker compose exec app sh
docker compose exec mysql mysql -u cloudbox_user -p

# Restart services
docker compose restart

# Stop services
docker compose stop

# Stop và xóa (CẢNH BÁO: mất data)
docker compose down -v

# Backup database
docker compose exec mysql mysqldump -u cloudbox_user -pcloudbox_password cloudbox_db > backup.sql

# Restore database
cat backup.sql | docker compose exec -T mysql mysql -u cloudbox_user -pcloudbox_password cloudbox_db

# Clear Laravel cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

---

## 🎯 Checklist Deploy thành công

- [ ] Docker Desktop đã cài và chạy
- [ ] Clone code từ GitHub
- [ ] Copy và config .env
- [ ] Generate APP_KEY
- [ ] `docker-compose build` thành công
- [ ] `docker-compose up -d` chạy OK
- [ ] Migrations chạy thành công
- [ ] Tạo admin user
- [ ] Truy cập http://localhost:8000
- [ ] Login thành công
- [ ] Upload file test
- [ ] Create folder test

---

## 🐛 Troubleshooting

### Docker Desktop không start (Windows)
- Enable WSL 2: `wsl --install`
- Enable Virtualization trong BIOS
- Restart máy

### Port 8000 bị chiếm
```bash
# Đổi port trong docker-compose.yml
ports:
  - "8080:80"  # Thay 8000 thành 8080
```

### Database connection lỗi
```bash
# Kiểm tra MySQL running
docker compose ps mysql

# Restart MySQL
docker compose restart mysql
```

### Permission errors
```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Clear all và restart
```bash
docker compose down -v
docker system prune -a
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
docker compose up -d --build
```

---

**🎉 Done! Website đã chạy trên Docker!**

**Ports:**
- Application: http://localhost:8000
- MySQL: localhost:3306
- Redis: localhost:6379

**Next steps:**
1. Point domain to VPS IP
2. Setup SSL với Certbot
3. Configure backup automation
4. Setup monitoring
