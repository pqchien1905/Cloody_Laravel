# 🗄️ HƯỚNG DẪN SETUP DATABASE

## BƯỚC 1: Tạo Database trong phpMyAdmin

### Cách 1: Sử dụng phpMyAdmin (Giao diện web)

1. Mở trình duyệt và truy cập: **http://localhost/phpmyadmin**

2. Đăng nhập với:
   - Username: `root`
   - Password: (để trống nếu dùng Laragon mặc định)

3. Click tab **"Databases"** ở menu trên

4. Trong mục **"Create database"**:
   - Nhập tên database: `cloudbox_db`
   - Chọn Collation: `utf8mb4_unicode_ci`
   - Click nút **"Create"**

### Cách 2: Sử dụng Command Line

```powershell
# Kết nối MySQL
mysql -u root -p

# Tạo database
CREATE DATABASE cloudbox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Xem database đã tạo
SHOW DATABASES;

# Thoát
EXIT;
```

---

## BƯỚC 2: Kiểm tra file .env

File `.env` đã được cấu hình:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cloudbox_db
DB_USERNAME=root
DB_PASSWORD=
```

✅ **Không cần sửa gì thêm!**

---

## BƯỚC 3: Chạy Migrations

### Các bảng sẽ được tạo:

1. **users** - Quản lý người dùng
2. **folders** - Quản lý thư mục
3. **files** - Quản lý files
4. **file_shares** - Chia sẻ files
5. **sessions** - Quản lý phiên đăng nhập
6. **cache** - Cache database
7. **jobs** - Queue jobs

### Chạy migrations:

```powershell
php artisan migrate
```

### Nếu gặp lỗi, chạy lại từ đầu:

```powershell
php artisan migrate:fresh
```

---

## CẤU TRÚC DATABASE

### 📁 Bảng: folders
```
- id
- user_id (FK → users)
- parent_id (FK → folders, nullable)
- name
- color
- description
- is_trash
- created_at
- updated_at
```

### 📄 Bảng: files
```
- id
- user_id (FK → users)
- folder_id (FK → folders, nullable)
- name
- original_name
- path
- mime_type
- extension
- size (bytes)
- is_favorite
- is_trash
- trashed_at
- description
- created_at
- updated_at
```

### 🔗 Bảng: file_shares
```
- id
- file_id (FK → files)
- shared_by (FK → users)
- shared_with (FK → users, nullable)
- share_token (unique)
- permission (view/download/edit)
- is_public
- expires_at
- created_at
- updated_at
```

---

## MODELS & RELATIONSHIPS

### User Model
```php
// Relationships:
$user->files()          // Files owned by user
$user->folders()        // Folders owned by user
$user->sharedFiles()    // Files shared by user
$user->receivedShares() // Files shared with user
```

### File Model
```php
// Relationships:
$file->user()           // File owner
$file->folder()         // Parent folder
$file->shares()         // File shares

// Methods:
$file->formatted_size   // Human readable size (e.g., "2.5 MB")

// Scopes:
File::active()          // Non-trashed files
File::trashed()         // Trashed files
File::favorites()       // Favorite files
```

### Folder Model
```php
// Relationships:
$folder->user()         // Folder owner
$folder->parent()       // Parent folder
$folder->children()     // Sub-folders
$folder->files()        // Files in folder

// Scopes:
Folder::active()        // Non-trashed folders
Folder::root()          // Root folders (no parent)
```

### FileShare Model
```php
// Relationships:
$share->file()          // Shared file
$share->sharedBy()      // User who shared
$share->sharedWith()    // User received share

// Methods:
$share->isExpired()     // Check if expired

// Scopes:
FileShare::active()     // Active shares (not expired)
```

---

## KIỂM TRA DATABASE

### 1. Xem các bảng đã tạo:
```powershell
php artisan db:show
```

### 2. Xem cấu trúc bảng:
```powershell
php artisan db:table files
php artisan db:table folders
php artisan db:table file_shares
```

### 3. Test trong Tinker:
```powershell
php artisan tinker

# Tạo user mẫu
$user = App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password')
]);

# Tạo folder
$folder = App\Models\Folder::create([
    'user_id' => $user->id,
    'name' => 'My Documents',
    'color' => '#3498db'
]);

# Xem folders của user
$user->folders;

# Exit
exit
```

---

## SEEDING DATA (TÙY CHỌN)

### Tạo Seeder:
```powershell
php artisan make:seeder FileSeeder
```

### Chạy seeder:
```powershell
php artisan db:seed
```

---

## BACKUP & RESTORE

### Backup database:
```powershell
# Export từ MySQL
mysqldump -u root cloudbox_db > backup.sql
```

### Restore database:
```powershell
# Import vào MySQL
mysql -u root cloudbox_db < backup.sql
```

---

## TROUBLESHOOTING

### Lỗi: "Access denied for user"
```powershell
# Kiểm tra username/password trong .env
# Thử đổi password thành rỗng hoặc password bạn đặt
```

### Lỗi: "Database does not exist"
```powershell
# Kiểm tra database đã tạo chưa
# Xem lại tên database trong .env
```

### Lỗi: "SQLSTATE[HY000] [2002]"
```powershell
# MySQL chưa chạy
# Mở Laragon → Start All
```

### Reset database:
```powershell
# Xóa tất cả tables và chạy lại
php artisan migrate:fresh

# Xóa + tạo lại + seed data
php artisan migrate:fresh --seed
```

---

## BƯỚC TIẾP THEO

✅ Database đã setup
✅ Models đã tạo
✅ Relationships đã cấu hình

**Tiếp theo:** Tạo Controllers và Views cho chức năng upload files!

Xem file: `FILE_UPLOAD_GUIDE.md`
