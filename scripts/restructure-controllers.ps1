# Script Tái Cấu Trúc Controllers - CloudBox Laravel
# Ngày tạo: 8 tháng 1, 2026
# Mục đích: Tách Controllers thành Admin và User folders

Write-Host "=== CloudBox Laravel - Tái Cấu Trúc Controllers ===" -ForegroundColor Cyan
Write-Host ""

# Bước 1: Tạo thư mục
Write-Host "Bước 1: Tạo thư mục Admin và User..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "app\Http\Controllers\Admin" | Out-Null
New-Item -ItemType Directory -Force -Path "app\Http\Controllers\User" | Out-Null
Write-Host "✓ Đã tạo thư mục thành công!" -ForegroundColor Green
Write-Host ""

# Bước 2: Di chuyển Admin Controllers
Write-Host "Bước 2: Di chuyển Admin Controllers..." -ForegroundColor Yellow
$adminFiles = @(
    "AdminCategoriesController.php", 
    "AdminController.php", 
    "AdminFavoritesController.php", 
    "AdminFilesController.php", 
    "AdminFoldersController.php", 
    "AdminGroupsController.php", 
    "AdminReportsController.php", 
    "AdminSharesController.php", 
    "AdminStoragePlansController.php", 
    "AdminTrashController.php", 
    "AdminUsersController.php"
)

$movedAdmin = 0
foreach($file in $adminFiles) {
    $sourcePath = "app\Http\Controllers\$file"
    $destPath = "app\Http\Controllers\Admin\$file"
    
    if (Test-Path $sourcePath) {
        Move-Item -Path $sourcePath -Destination $destPath -Force
        $movedAdmin++
        Write-Host "  → Đã di chuyển: $file" -ForegroundColor Gray
    }
}
Write-Host "✓ Đã di chuyển $movedAdmin Admin Controllers!" -ForegroundColor Green
Write-Host ""

# Bước 3: Di chuyển User Controllers
Write-Host "Bước 3: Di chuyển User Controllers..." -ForegroundColor Yellow
$userFiles = @(
    "AIChatController.php", 
    "AvatarController.php", 
    "DashboardController.php", 
    "FileController.php", 
    "FileShareController.php", 
    "FileUploadController.php", 
    "FolderController.php", 
    "FolderShareController.php", 
    "GroupController.php", 
    "LocaleController.php", 
    "PaymentController.php", 
    "ProfileController.php", 
    "StoragePlansController.php", 
    "UserProfileController.php"
)

$movedUser = 0
foreach($file in $userFiles) {
    $sourcePath = "app\Http\Controllers\$file"
    $destPath = "app\Http\Controllers\User\$file"
    
    if (Test-Path $sourcePath) {
        Move-Item -Path $sourcePath -Destination $destPath -Force
        $movedUser++
        Write-Host "  → Đã di chuyển: $file" -ForegroundColor Gray
    }
}
Write-Host "✓ Đã di chuyển $movedUser User Controllers!" -ForegroundColor Green
Write-Host ""

# Bước 4: Cập nhật namespace Admin Controllers
Write-Host "Bước 4: Cập nhật namespace Admin Controllers..." -ForegroundColor Yellow
$updatedAdmin = 0
Get-ChildItem "app\Http\Controllers\Admin\*.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $content = $content -replace "namespace App\\Http\\Controllers;", "namespace App\Http\Controllers\Admin;"
    Set-Content $_.FullName -Value $content -NoNewline
    $updatedAdmin++
}
Write-Host "✓ Đã cập nhật namespace cho $updatedAdmin Admin Controllers!" -ForegroundColor Green
Write-Host ""

# Bước 5: Cập nhật namespace User Controllers
Write-Host "Bước 5: Cập nhật namespace User Controllers..." -ForegroundColor Yellow
$updatedUser = 0
Get-ChildItem "app\Http\Controllers\User\*.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $content = $content -replace "namespace App\\Http\\Controllers;", "namespace App\Http\Controllers\User;"
    Set-Content $_.FullName -Value $content -NoNewline
    $updatedUser++
}
Write-Host "✓ Đã cập nhật namespace cho $updatedUser User Controllers!" -ForegroundColor Green
Write-Host ""

# Bước 6: Thêm import Controller cho User Controllers
Write-Host "Bước 6: Thêm import Controller cho User Controllers..." -ForegroundColor Yellow
$importedUser = 0
Get-ChildItem "app\Http\Controllers\User\*.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    if ($content -notmatch "use App\\Http\\Controllers\\Controller;") {
        $content = $content -replace "(namespace App\\Http\\Controllers\\User;)", "`$1`r`n`r`nuse App\Http\Controllers\Controller;"
        Set-Content $_.FullName -Value $content -NoNewline
        $importedUser++
    }
}
Write-Host "✓ Đã thêm import cho $importedUser User Controllers!" -ForegroundColor Green
Write-Host ""

# Bước 7: Thêm import Controller cho Admin Controllers
Write-Host "Bước 7: Thêm import Controller cho Admin Controllers..." -ForegroundColor Yellow
$importedAdmin = 0
Get-ChildItem "app\Http\Controllers\Admin\*.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    if ($content -notmatch "use App\\Http\\Controllers\\Controller;") {
        $content = $content -replace "(namespace App\\Http\\Controllers\\Admin;)", "`$1`r`n`r`nuse App\Http\Controllers\Controller;"
        Set-Content $_.FullName -Value $content -NoNewline
        $importedAdmin++
    }
}
Write-Host "✓ Đã thêm import cho $importedAdmin Admin Controllers!" -ForegroundColor Green
Write-Host ""

# Bước 8: Xóa cache Laravel
Write-Host "Bước 8: Xóa cache Laravel..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
Write-Host "✓ Đã xóa cache thành công!" -ForegroundColor Green
Write-Host ""

# Tổng kết
Write-Host "=== KẾT QUẢ TÁI CẤU TRÚC ===" -ForegroundColor Cyan
Write-Host "Admin Controllers: $movedAdmin file đã di chuyển, $updatedAdmin đã cập nhật" -ForegroundColor White
Write-Host "User Controllers: $movedUser file đã di chuyển, $updatedUser đã cập nhật" -ForegroundColor White
Write-Host ""
Write-Host "HOÀN TẤT! Vui lòng cập nhật routes/web.php theo tài liệu." -ForegroundColor Green
Write-Host ""
Write-Host "Xem thêm: docs/controllers-restructure.md" -ForegroundColor Cyan
