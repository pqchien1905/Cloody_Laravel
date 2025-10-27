# 🖥️ POWERSHELL COMMANDS CHO LARAVEL

## ⚠️ LƯU Ý QUAN TRỌNG

PowerShell **KHÔNG hỗ trợ** `&&` như Bash/Linux!

### ❌ KHÔNG dùng (Bash style):
```bash
npm install && npm run build
composer install && php artisan migrate
```

### ✅ DÙNG (PowerShell style):
```powershell
npm install; npm run build
composer install; php artisan migrate
```

---

## 📝 CÁC LỆNH POWERSHELL CƠ BẢN

### Cách kết hợp nhiều lệnh:

#### 1. Dùng dấu `;` (chạy lần lượt, bất kể lệnh trước thành công hay thất bại)
```powershell
npm install; npm run build; php artisan serve
```

#### 2. Dùng `&&` trong PowerShell 7+ (chỉ chạy lệnh sau nếu lệnh trước thành công)
```powershell
# Nếu bạn dùng PowerShell 7+
npm install -and npm run build
```

#### 3. Chạy từng lệnh riêng biệt (Khuyến khích cho người mới):
```powershell
npm install
npm run build
php artisan serve
```

---

## 🚀 CÁC LỆNH LARAVEL THƯỜNG DÙNG

### Development Server
```powershell
# Khởi động server
php artisan serve

# Khởi động server với port tùy chỉnh
php artisan serve --port=8080
```

### NPM Commands
```powershell
# Cài đặt packages
npm install

# Build cho production
npm run build

# Build + watch (development)
npm run dev
```

### Artisan Commands
```powershell
# Tạo controller
php artisan make:controller NameController

# Tạo model + migration
php artisan make:model Name -m

# Chạy migrations
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Tạo seeder
php artisan make:seeder NameSeeder

# Chạy seeder
php artisan db:seed
```

### Clear Cache
```powershell
# Clear tất cả cache
php artisan cache:clear; php artisan config:clear; php artisan route:clear; php artisan view:clear

# Hoặc chạy từng lệnh:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Composer Commands
```powershell
# Cài đặt packages
composer install

# Update packages
composer update

# Cài package mới
composer require vendor/package

# Remove package
composer remove vendor/package

# Autoload
composer dump-autoload
```

---

## 🔧 SETUP PROJECT MỚI

### Cài đặt và khởi động:
```powershell
# 1. Cài đặt dependencies
composer install

# 2. Copy .env file
Copy-Item .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Cài npm packages
npm install

# 5. Build assets
npm run build

# 6. Chạy migrations
php artisan migrate

# 7. Khởi động server
php artisan serve
```

### Hoặc chạy một lượt:
```powershell
composer install; Copy-Item .env.example .env; php artisan key:generate; npm install; npm run build; php artisan migrate
```

---

## 📂 FILE & FOLDER OPERATIONS

### Copy file:
```powershell
Copy-Item source.txt destination.txt
```

### Di chuyển file:
```powershell
Move-Item source.txt destination.txt
```

### Xóa file:
```powershell
Remove-Item filename.txt
```

### Xóa folder:
```powershell
Remove-Item -Recurse -Force foldername
```

### Tạo folder:
```powershell
New-Item -ItemType Directory -Path "path/to/folder"
```

### Xem nội dung file:
```powershell
Get-Content filename.txt
```

### Tìm file:
```powershell
Get-ChildItem -Recurse -Filter "*.php"
```

---

## 🎯 GIT COMMANDS

### Basic Git:
```powershell
# Init repo
git init

# Add files
git add .

# Commit
git commit -m "message"

# Push
git push origin main

# Pull
git pull origin main

# Clone
git clone https://github.com/user/repo.git
```

### Git với nhiều lệnh:
```powershell
git add .; git commit -m "update"; git push
```

---

## 🔍 KIỂM TRA & DEBUG

### Kiểm tra phiên bản:
```powershell
# PHP version
php -v

# Composer version
composer -V

# Node version
node -v

# NPM version
npm -v

# Git version
git --version
```

### Kiểm tra Laravel:
```powershell
# Laravel version
php artisan --version

# List routes
php artisan route:list

# List commands
php artisan list
```

### Kiểm tra port đang dùng:
```powershell
# Xem port 8000
netstat -ano | findstr :8000

# Kill process bằng PID
taskkill /PID [PID_NUMBER] /F
```

---

## ⚡ ALIASES HỮU ÍCH

### Tạo aliases trong PowerShell Profile:

```powershell
# Mở PowerShell profile
notepad $PROFILE

# Thêm các aliases:
function artisan { php artisan $args }
function serve { php artisan serve }
function migrate { php artisan migrate }
function tinker { php artisan tinker }

# Sau đó reload:
. $PROFILE
```

### Sử dụng:
```powershell
artisan make:controller TestController
serve
migrate
```

---

## 🐛 TROUBLESHOOTING

### Lỗi: "cannot be loaded because running scripts is disabled"
```powershell
# Chạy PowerShell as Administrator
Set-ExecutionPolicy RemoteSigned

# Hoặc
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Lỗi: Port 8000 đã được dùng
```powershell
# Tìm process đang dùng port
netstat -ano | findstr :8000

# Kill process
taskkill /PID [PID] /F

# Hoặc dùng port khác
php artisan serve --port=8080
```

### Lỗi: npm không tìm thấy
```powershell
# Kiểm tra Node đã cài chưa
node -v

# Cài Node.js từ: https://nodejs.org/
```

---

## 📚 TÀI LIỆU THAM KHẢO

- PowerShell Docs: https://docs.microsoft.com/powershell/
- Laravel Artisan: https://laravel.com/docs/12.x/artisan
- Composer: https://getcomposer.org/doc/
- NPM: https://docs.npmjs.com/

---

## 💡 TIPS

1. **Luôn chạy PowerShell từ thư mục project**
   ```powershell
   cd C:\laragon\www\cloudbox-laravel
   ```

2. **Dùng Tab để auto-complete**
   - Gõ `php art` rồi nhấn `Tab`

3. **Dùng Up/Down arrow để xem lịch sử lệnh**

4. **Ctrl + C để dừng server đang chạy**

5. **Dùng `;` thay vì `&&` trong PowerShell**

---

*Lưu file này để tham khảo khi cần!*
