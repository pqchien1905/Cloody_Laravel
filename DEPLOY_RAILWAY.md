# 🚀 Hướng dẫn Deploy Cloody Laravel lên Railway.app (FREE)

## ✅ Đã chuẩn bị sẵn:
- ✅ Procfile
- ✅ nixpacks.toml  
- ✅ .railway.json
- ✅ Code đã push lên GitHub

---

## 📋 Các bước Deploy:

### **Bước 1: Tạo tài khoản Railway.app**

1. Truy cập: https://railway.app
2. Click **"Login"** → Chọn **"Login with GitHub"**
3. Authorize Railway truy cập GitHub của bạn

**Free Tier:** 500 giờ/tháng, $5 credit miễn phí

---

### **Bước 2: Tạo Project mới**

1. Click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. Chọn repository: **`pqchien1905/Cloody_Laravel`**
4. Click **"Deploy Now"**

Railway sẽ tự động:
- Detect Nixpacks
- Install dependencies (composer, npm)
- Build assets (npm run build)
- Deploy application

---

### **Bước 3: Thêm Database MySQL**

1. Click vào project vừa tạo
2. Click **"+ New"** → **"Database"** → **"Add MySQL"**
3. Đợi MySQL provision xong (~30 giây)

Railway tự động tạo các biến môi trường:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

---

### **Bước 4: Cấu hình Environment Variables**

Click vào **service "cloody-laravel"** → Tab **"Variables"** → Add các biến:

```bash
# Application
APP_NAME=Cloody
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE

# Database (Railway tự động inject MYSQL*)
DB_CONNECTION=mysql
DB_HOST=${MYSQLHOST}
DB_PORT=${MYSQLPORT}
DB_DATABASE=${MYSQLDATABASE}
DB_USERNAME=${MYSQLUSER}
DB_PASSWORD=${MYSQLPASSWORD}

# URL (sẽ có sau khi deploy)
APP_URL=https://your-app.up.railway.app

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database

# Mail (Gmail example - optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Storage
FILESYSTEM_DISK=public

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**Lấy APP_KEY:**
```bash
# Chạy local để generate key
php artisan key:generate --show
# Copy key và paste vào Railway
```

---

### **Bước 5: Generate Domain**

1. Click vào service → Tab **"Settings"**
2. Section **"Networking"** → Click **"Generate Domain"**
3. Railway sẽ tạo domain: `https://cloody-laravel-production-xxxx.up.railway.app`
4. Copy domain này và update vào `APP_URL` ở Variables

---

### **Bước 6: Deploy lại**

1. Tab **"Deployments"** → Click **"Deploy"** (hoặc đợi auto-redeploy)
2. Xem logs real-time để kiểm tra:
   - ✅ Build succeeded
   - ✅ Migrations ran
   - ✅ Application started

---

### **Bước 7: Chạy Migrations & Seeders (nếu cần)**

Có 2 cách:

**Cách 1: Qua Railway CLI (Recommended)**
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link to project
railway link

# Run commands
railway run php artisan migrate --force
railway run php artisan db:seed --class=StoragePlanSeeder
railway run php artisan storage:link
```

**Cách 2: Thêm vào Procfile (đã tích hợp sẵn)**
Procfile đã có `php artisan migrate --force` trong web command

---

### **Bước 8: (Optional) Setup Queue Worker**

Để chạy background jobs (email notifications, file sharing):

1. Click **"+ New"** trong project → **"Empty Service"**
2. Rename thành **"worker"**
3. Tab **"Settings"** → **"Source"** → Connect to same repo
4. Tab **"Settings"** → **"Start Command"**:
   ```bash
   php artisan queue:work --verbose --tries=3 --timeout=90
   ```
5. Copy tất cả environment variables từ web service sang worker

---

### **Bước 9: Tạo Admin User đầu tiên**

```bash
# Qua Railway CLI
railway run php artisan tinker

# Trong tinker console:
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@cloody.com';
$user->password = Hash::make('password123');
$user->is_admin = true;
$user->save();
exit
```

Hoặc chạy seeder nếu có:
```bash
railway run php artisan db:seed --class=UserSeeder
```

---

## 🎯 Kiểm tra sau Deploy:

1. ✅ Truy cập URL: `https://your-app.up.railway.app`
2. ✅ Test đăng ký/đăng nhập
3. ✅ Test upload file
4. ✅ Test tạo folder
5. ✅ Kiểm tra logs: Tab **"Deployments"** → Click vào deploy → **"View Logs"**

---

## 🔧 Troubleshooting:

### Lỗi "No application encryption key has been specified"
```bash
railway run php artisan key:generate
# Copy key output và add vào Variables
```

### Lỗi Database connection
- Kiểm tra MySQL service đã running
- Verify environment variables: `DB_HOST`, `DB_PORT`, etc.

### Lỗi Storage/Permissions
```bash
railway run php artisan storage:link
railway run chmod -R 775 storage bootstrap/cache
```

### Lỗi Build timeout
- Check logs để xem stage nào bị stuck
- Có thể do npm install chậm → Thử deploy lại

### Clear cache
```bash
railway run php artisan cache:clear
railway run php artisan config:clear
railway run php artisan route:clear
railway run php artisan view:clear
```

---

## 📊 Monitoring:

**Xem Metrics:**
- Tab **"Metrics"** → CPU, Memory, Network usage
- Tab **"Deployments"** → Build logs, deploy history

**Xem Logs realtime:**
```bash
railway logs
```

---

## 🔄 Auto-Deploy từ GitHub:

Railway tự động deploy khi bạn push code lên GitHub:
```bash
git add .
git commit -m "Update feature"
git push cloody_laravel main
```
→ Railway tự động detect và deploy!

---

## 💰 Giới hạn Free Tier:

- ⏱️ **500 giờ/tháng** ($5 credit)
- 💾 **1GB RAM** per service
- 💿 **1GB Storage** per database
- 🌐 **100GB Bandwidth**

**Ước tính:** Đủ cho testing và demo, khoảng ~20 ngày uptime liên tục

---

## 🚀 Upgrade (Nếu cần):

- **Hobby Plan:** $5/tháng - 500 execution hours
- **Pro Plan:** $20/tháng - Unlimited execution hours

---

## 📝 Custom Domain (Optional):

1. Tab **"Settings"** → **"Domains"**
2. Click **"Custom Domain"**
3. Add your domain (e.g., `cloody.yourdomain.com`)
4. Update DNS records theo hướng dẫn

---

## ✅ Checklist Deploy thành công:

- [ ] Project created on Railway
- [ ] MySQL database added
- [ ] Environment variables configured
- [ ] APP_KEY generated
- [ ] Domain generated
- [ ] Migrations ran successfully
- [ ] Storage linked
- [ ] Admin user created
- [ ] Website accessible
- [ ] Queue worker running (optional)
- [ ] Logs showing no errors

---

**🎉 Chúc mừng! Dự án của bạn đã live trên Railway.app!**

**Demo URL:** Sẽ có dạng `https://cloody-laravel-production-xxxx.up.railway.app`

---

## 📞 Support:

- Railway Docs: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- GitHub Issues: https://github.com/pqchien1905/Cloody_Laravel/issues
