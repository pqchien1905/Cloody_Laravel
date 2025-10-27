# 🚀 QUICK REFERENCE - CLOUDBOX LARAVEL

## 📋 ROUTES CHÍNH

```
Dashboard:     http://127.0.0.1:8000/cloudbox
Files:         http://127.0.0.1:8000/cloudbox/files
Folders:       http://127.0.0.1:8000/cloudbox/folders
```

## 🗄️ DATABASE

Database: `cloudbox_db`
Tables: users, folders, files, file_shares, cache, jobs, sessions

## 🎯 API ENDPOINTS

### Files
```php
POST   /cloudbox/files/upload          - Upload file
GET    /cloudbox/files/{id}/download   - Download
DELETE /cloudbox/files/{id}            - Move to trash
POST   /cloudbox/files/{id}/restore    - Restore
DELETE /cloudbox/files/{id}/force      - Delete permanent
POST   /cloudbox/files/{id}/favorite   - Toggle favorite
```

### Folders
```php
GET    /cloudbox/folders        - List all
GET    /cloudbox/folders/{id}   - View folder
POST   /cloudbox/folders        - Create
PUT    /cloudbox/folders/{id}   - Update
DELETE /cloudbox/folders/{id}   - Delete
```

### Sharing
```php
POST   /cloudbox/files/{id}/share  - Share file
GET    /shared/{token}            - View shared (public)
GET    /shared/{token}/download   - Download shared (public)
```

## 💻 POWERSHELL COMMANDS

```powershell
# Khởi động server
php artisan serve

# Migrations
php artisan migrate
php artisan migrate:fresh

# Clear cache
php artisan cache:clear; php artisan config:clear

# Tinker (test database)
php artisan tinker

# NPM
npm install
npm run build
npm run dev
```

## 🔍 TESTING DATABASE

```powershell
php artisan tinker

# Create user
$user = App\Models\User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('password')]);

# Create folder
$folder = App\Models\Folder::create(['user_id' => 1, 'name' => 'My Folder', 'color' => '#3498db']);

# View files
App\Models\File::all();

# Exit
exit
```

## 📂 FILE STRUCTURE

```
Models:       app/Models/{File,Folder,FileShare}.php
Controllers:  app/Http/Controllers/File*.php
Views:        resources/views/pages/
Routes:       routes/web.php
Uploads:      storage/app/public/uploads/
```

## 🎨 HELPER FUNCTIONS

```php
// File size
$file->formatted_size  // "2.5 MB"

// Check if trashed
$file->is_trash

// Check if favorite
$file->is_favorite

// Share expired?
$share->isExpired()
```

## 🔗 RELATIONSHIPS

```php
// User
$user->files;
$user->folders;
$user->sharedFiles;

// File
$file->user;
$file->folder;
$file->shares;

// Folder
$folder->files;
$folder->children;
$folder->parent;
```

## 📖 DOCS

- `DATABASE_SETUP.md` - Database chi tiết
- `DEVELOPMENT_COMPLETE.md` - Tổng quan phát triển
- `QUICK_START_VI.md` - Bắt đầu nhanh
- `POWERSHELL_COMMANDS.md` - Lệnh PowerShell

## ⚡ NEXT STEPS

1. Test upload: http://127.0.0.1:8000/cloudbox/files
2. Tạo views đẹp với modals
3. Thêm authentication
4. Deploy production

---

*Last updated: 2025-10-24*
