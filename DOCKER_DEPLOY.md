# 🐳 Docker Deployment Guide - Cloody Laravel

## ✅ Đã chuẩn bị:
- ✅ Dockerfile
- ✅ docker-compose.yml
- ✅ .dockerignore
- ✅ Nginx configuration
- ✅ Supervisor configuration
- ✅ Đã xóa thư mục/file không cần thiết

---

## 📋 Yêu cầu:
- Docker Desktop (Windows/Mac) hoặc Docker Engine (Linux)
- Docker Compose v2.0+

**Cài đặt Docker Desktop:**
- Windows/Mac: https://www.docker.com/products/docker-desktop

---

## 🚀 Deploy Local với Docker:

### **Bước 1: Chuẩn bị file .env**

```bash
# Copy .env.example
cp .env.example .env

# Hoặc trên Windows
copy .env.example .env
```

Sửa `.env`:
```env
APP_NAME=Cloody
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE

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
```

### **Bước 2: Generate APP_KEY**

```bash
# Nếu chưa có APP_KEY trong .env
docker run --rm -v ${PWD}:/app composer/composer:latest \
  bash -c "cd /app && php artisan key:generate --show"

# Hoặc local
php artisan key:generate --show
```

Copy key vào `.env`

### **Bước 3: Build và Start Containers**

```bash
# Build images
docker-compose build

# Start all services
docker-compose up -d

# Xem logs
docker-compose logs -f
```

**Services sẽ chạy:**
- `app` - Laravel application (port 8000)
- `mysql` - MySQL database (port 3306)
- `redis` - Redis cache (port 6379)
- `queue` - Queue worker

### **Bước 4: Run Migrations**

```bash
# Run migrations
docker-compose exec app php artisan migrate --force

# Seed storage plans (optional)
docker-compose exec app php artisan db:seed --class=StoragePlanSeeder

# Create admin user
docker-compose exec app php artisan tinker
```

Trong tinker:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@cloody.com';
$user->password = Hash::make('password123');
$user->is_admin = true;
$user->save();
exit
```

### **Bước 5: Truy cập Application**

Mở trình duyệt: **http://localhost:8000**

---

## 🛠️ Các lệnh hữu ích:

```bash
# Xem status containers
docker-compose ps

# Xem logs
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f queue

# Vào container
docker-compose exec app sh
docker-compose exec mysql mysql -u cloudbox_user -p

# Restart services
docker-compose restart

# Stop services
docker-compose stop

# Stop và xóa containers
docker-compose down

# Stop và xóa cả volumes (CẢNH BÁO: mất data)
docker-compose down -v

# Rebuild image
docker-compose build --no-cache app

# Clear Laravel cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Run artisan commands
docker-compose exec app php artisan [command]

# Update code and rebuild
git pull
docker-compose build app
docker-compose up -d
docker-compose exec app php artisan migrate --force
```

---

## 🌐 Deploy lên Production Server:

### **1. Chuẩn bị VPS (Ubuntu 22.04)**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker compose version
```

### **2. Clone và Deploy**

```bash
# Clone repository
cd /var/www
git clone https://github.com/pqchien1905/Cloody_Laravel.git cloudbox
cd cloudbox

# Setup .env
cp .env.example .env
nano .env

# Update .env với production values:
# - APP_URL=https://yourdomain.com
# - APP_DEBUG=false
# - Mật khẩu database mạnh
# - Mail configuration
# - etc.

# Build và start
docker compose build
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```

### **3. Setup Nginx Reverse Proxy với SSL**

```bash
# Install Nginx
sudo apt install nginx certbot python3-certbot-nginx -y

# Create nginx config
sudo nano /etc/nginx/sites-available/cloudbox
```

Nội dung:
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

Enable và SSL:
```bash
sudo ln -s /etc/nginx/sites-available/cloudbox /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### **4. Auto-restart containers**

```bash
# Edit docker-compose.yml và đảm bảo có:
# restart: unless-stopped

# Start on boot
sudo systemctl enable docker
```

---

## 📊 Monitoring:

```bash
# Resource usage
docker stats

# Disk usage
docker system df

# Container health
docker-compose ps

# Logs
docker-compose logs --tail=100 -f
```

---

## 🔄 Update Application:

```bash
cd /var/www/cloudbox

# Pull latest code
git pull origin main

# Rebuild và restart
docker-compose build app
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear cache
docker-compose exec app php artisan optimize
```

---

## 💾 Backup:

```bash
# Backup database
docker-compose exec mysql mysqldump -u cloudbox_user -pcloudbox_password cloudbox_db > backup_$(date +%Y%m%d).sql

# Backup storage
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/

# Restore database
cat backup.sql | docker-compose exec -T mysql mysql -u cloudbox_user -pcloudbox_password cloudbox_db
```

---

## 🐛 Troubleshooting:

### Container không start:
```bash
docker-compose logs app
docker-compose ps
```

### Database connection lỗi:
```bash
# Kiểm tra MySQL running
docker-compose ps mysql

# Test connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Permission errors:
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Xóa và reset hoàn toàn:
```bash
docker-compose down -v
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
docker-compose up -d
```

---

## 📦 Production Optimization:

Sửa `docker-compose.yml` cho production:

```yaml
services:
  app:
    # ... existing config
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - LOG_LEVEL=error
    deploy:
      resources:
        limits:
          cpus: '1'
          memory: 1024M
        reservations:
          cpus: '0.5'
          memory: 512M
```

---

**🎉 Xong! Application đã chạy trên Docker!**

**Access:**
- Application: http://localhost:8000
- MySQL: localhost:3306
- Redis: localhost:6379

**Next Steps:**
1. Setup domain và SSL
2. Configure backup automation
3. Setup monitoring (Prometheus + Grafana)
4. Configure log rotation
