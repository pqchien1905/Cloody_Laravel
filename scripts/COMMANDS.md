# CLOUDBOX LARAVEL - LỆNH TÁI CẤU TRÚC NHANH

## Tất Cả Các Lệnh PowerShell (Chạy Tuần Tự)

### 1. Tạo Thư Mục
```powershell
cd c:\laragon\www\cloudbox-laravel
New-Item -ItemType Directory -Force -Path "app\Http\Controllers\Admin"
New-Item -ItemType Directory -Force -Path "app\Http\Controllers\User"
```

### 2. Di Chuyển Admin Controllers
```powershell
$adminFiles = @("AdminCategoriesController.php", "AdminController.php", "AdminFavoritesController.php", "AdminFilesController.php", "AdminFoldersController.php", "AdminGroupsController.php", "AdminReportsController.php", "AdminSharesController.php", "AdminStoragePlansController.php", "AdminTrashController.php", "AdminUsersController.php")
foreach($file in $adminFiles) { Move-Item -Path "app\Http\Controllers\$file" -Destination "app\Http\Controllers\Admin\$file" -Force }
```

### 3. Di Chuyển User Controllers
```powershell
$userFiles = @("AIChatController.php", "AvatarController.php", "DashboardController.php", "FileController.php", "FileShareController.php", "FileUploadController.php", "FolderController.php", "FolderShareController.php", "GroupController.php", "LocaleController.php", "PaymentController.php", "ProfileController.php", "StoragePlansController.php", "UserProfileController.php")
foreach($file in $userFiles) { Move-Item -Path "app\Http\Controllers\$file" -Destination "app\Http\Controllers\User\$file" -Force }
```

### 4. Cập Nhật Namespace Admin
```powershell
Get-ChildItem "app\Http\Controllers\Admin\*.php" | ForEach-Object { $content = Get-Content $_.FullName -Raw; $content = $content -replace "namespace App\\Http\\Controllers;", "namespace App\Http\Controllers\Admin;"; Set-Content $_.FullName -Value $content -NoNewline }
```

### 5. Cập Nhật Namespace User
```powershell
Get-ChildItem "app\Http\Controllers\User\*.php" | ForEach-Object { $content = Get-Content $_.FullName -Raw; $content = $content -replace "namespace App\\Http\\Controllers;", "namespace App\Http\Controllers\User;"; Set-Content $_.FullName -Value $content -NoNewline }
```

### 6. Thêm Import Controller (User)
```powershell
Get-ChildItem "app\Http\Controllers\User\*.php" | ForEach-Object { $content = Get-Content $_.FullName -Raw; if ($content -notmatch "use App\\Http\\Controllers\\Controller;") { $content = $content -replace "(namespace App\\Http\\Controllers\\User;)", "`$1`r`n`r`nuse App\Http\Controllers\Controller;"; Set-Content $_.FullName -Value $content -NoNewline } }
```

### 7. Thêm Import Controller (Admin)
```powershell
Get-ChildItem "app\Http\Controllers\Admin\*.php" | ForEach-Object { $content = Get-Content $_.FullName -Raw; if ($content -notmatch "use App\\Http\\Controllers\\Controller;") { $content = $content -replace "(namespace App\\Http\\Controllers\\Admin;)", "`$1`r`n`r`nuse App\Http\Controllers\Controller;"; Set-Content $_.FullName -Value $content -NoNewline } }
```

### 8. Xóa Cache Laravel
```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 9. Kiểm Tra Kết Quả
```powershell
# Xem số lượng controllers
(Get-ChildItem "app\Http\Controllers\Admin\*.php").Count
(Get-ChildItem "app\Http\Controllers\User\*.php").Count

# Xem routes
php artisan route:list --path=admin
php artisan route:list --path=cloody
```

---

## Hoặc Chạy Script Tự Động

```powershell
.\scripts\restructure-controllers.ps1
```

---

## Lưu Ý Quan Trọng

⚠️ **SAU KHI CHẠY XONG**, bạn cần:
1. Cập nhật `routes/web.php` với các namespace mới
2. Thay đổi tất cả `use App\Http\Controllers\...` thành:
   - `use App\Http\Controllers\Admin\...` (cho admin controllers)
   - `use App\Http\Controllers\User\...` (cho user controllers)

📖 Xem chi tiết trong: `docs/controllers-restructure.md`
