# 📚 HƯỚNG DẪN TÍCH HỢP TEMPLATE CLOUDBOX VÀO LARAVEL 12

## ✅ Đã hoàn thành tự động:

### 1. **Di chuyển Assets**
✓ Đã copy toàn bộ thư mục `html/assets` vào `public/assets`
- CSS files (backend.css, plugins, bootstrap...)
- JavaScript files (app.js, backend-bundle.min.js...)
- Images (logos, icons, backgrounds...)
- Vendor libraries (FontAwesome, RemixIcon, Doc Viewer...)

### 2. **Cấu trúc Views**
✓ Đã tạo cấu trúc thư mục views:
```
resources/views/
├── layouts/
│   └── app.blade.php           # Master layout
├── partials/
│   ├── sidebar.blade.php       # Sidebar navigation
│   ├── topnav.blade.php        # Top navigation bar
│   └── footer.blade.php        # Footer
└── pages/
    ├── dashboard.blade.php     # Dashboard page
    └── files.blade.php         # Files listing page
```

### 3. **Controllers & Routes**
✓ Đã tạo controllers:
- `DashboardController` - Xử lý trang dashboard
- `FileController` - Xử lý trang files

✓ Đã cấu hình routes trong `routes/web.php`:
- `/` - Redirect đến dashboard
- `/dashboard` - Trang dashboard chính
- `/files` - Trang danh sách files

---

## 🚀 CÁC BƯỚC TIẾP THEO BẠN CẦN LÀM:

### BƯỚC 1: Khởi động server
```bash
# Trong terminal PowerShell
cd c:\laragon\www\cloudbox-laravel
php artisan serve
```

Mở trình duyệt và truy cập: `http://localhost:8000`

### BƯỚC 2: Kiểm tra và test
- Kiểm tra trang Dashboard: `http://localhost:8000/dashboard`
- Kiểm tra trang Files: `http://localhost:8000/files`
- Kiểm tra responsive design trên mobile
- Kiểm tra menu sidebar và navigation

### BƯỚC 3: Tùy chỉnh template theo dự án

#### 3.1. Thay đổi logo và branding:
```
public/assets/images/logo.png       # Logo chính
public/assets/images/favicon.ico    # Favicon
```

#### 3.2. Chỉnh sửa sidebar menu:
Mở file: `resources/views/partials/sidebar.blade.php`
```blade
<li class="{{ request()->routeIs('your-route') ? 'active' : '' }}">
    <a href="{{ route('your-route') }}">
        <i class="las la-icon"></i><span>Menu Name</span>
    </a>
</li>
```

#### 3.3. Tùy chỉnh màu sắc và style:
- CSS chính: `public/assets/css/backend.css`
- Thêm custom CSS vào: `resources/css/app.css`

### BƯỚC 4: Thêm Authentication (Laravel Breeze/UI)

#### Cài đặt Laravel Breeze:
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
php artisan migrate
```

#### Sau khi cài đặt, cập nhật routes:
```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/files', [FileController::class, 'index'])->name('files');
});
```

### BƯỚC 5: Kết nối Database

#### 5.1. Cấu hình `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cloudbox_laravel
DB_USERNAME=root
DB_PASSWORD=
```

#### 5.2. Tạo database trong phpMyAdmin:
- Mở: `http://localhost/phpmyadmin`
- Tạo database mới: `cloudbox_laravel`

#### 5.3. Chạy migrations:
```bash
php artisan migrate
```

### BƯỚC 6: Tạo Models và Migrations cho File Management

#### 6.1. Tạo File Model:
```bash
php artisan make:model File -m
```

#### 6.2. Cập nhật migration (database/migrations/xxxx_create_files_table.php):
```php
Schema::create('files', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('path');
    $table->string('type');
    $table->bigInteger('size');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

#### 6.3. Chạy migration:
```bash
php artisan migrate
```

### BƯỚC 7: Thêm chức năng Upload File

#### 7.1. Cập nhật FileController:
```php
public function store(Request $request)
{
    $request->validate([
        'file' => 'required|file|max:10240', // 10MB max
    ]);

    $file = $request->file('file');
    $path = $file->store('uploads', 'public');

    File::create([
        'name' => $file->getClientOriginalName(),
        'path' => $path,
        'type' => $file->getClientMimeType(),
        'size' => $file->getSize(),
        'user_id' => auth()->id(),
    ]);

    return redirect()->back()->with('success', 'File uploaded successfully!');
}
```

#### 7.2. Thêm route:
```php
Route::post('/files/upload', [FileController::class, 'store'])->name('files.upload');
```

#### 7.3. Tạo symbolic link cho storage:
```bash
php artisan storage:link
```

---

## 📝 CẤU TRÚC DỰ ÁN HIỆN TẠI:

```
cloudbox-laravel/
├── app/
│   └── Http/Controllers/
│       ├── DashboardController.php    ✓ Đã tạo
│       └── FileController.php         ✓ Đã tạo
├── public/
│   └── assets/                        ✓ Đã copy
│       ├── css/
│       ├── js/
│       ├── images/
│       └── vendor/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php          ✓ Đã tạo
│       ├── partials/
│       │   ├── sidebar.blade.php      ✓ Đã tạo
│       │   ├── topnav.blade.php       ✓ Đã tạo
│       │   └── footer.blade.php       ✓ Đã tạo
│       └── pages/
│           ├── dashboard.blade.php    ✓ Đã tạo
│           └── files.blade.php        ✓ Đã tạo
└── routes/
    └── web.php                        ✓ Đã cấu hình
```

---

## 🎨 CUSTOMIZATION TIPS:

### 1. Thay đổi màu chủ đạo:
File: `public/assets/css/backend.css`
```css
:root {
    --iq-primary: #3498db;      /* Màu chính */
    --iq-success: #2ecc71;      /* Màu success */
    --iq-danger: #e74c3c;       /* Màu danger */
}
```

### 2. Thêm trang mới:
```bash
# 1. Tạo view
# Tạo file: resources/views/pages/your-page.blade.php

# 2. Tạo controller
php artisan make:controller YourPageController

# 3. Thêm route
# Trong routes/web.php:
Route::get('/your-page', [YourPageController::class, 'index'])->name('your-page');

# 4. Thêm vào sidebar
# Trong resources/views/partials/sidebar.blade.php
```

### 3. Sử dụng icons:
Template hỗ trợ nhiều bộ icon:
- **Line Awesome**: `<i class="las la-home"></i>`
- **Remix Icons**: `<i class="ri-home-line"></i>`
- **Font Awesome**: `<i class="fas fa-home"></i>`

Tìm icons tại:
- Line Awesome: https://icons8.com/line-awesome
- Remix Icon: https://remixicon.com/
- Font Awesome: https://fontawesome.com/icons

---

## ⚠️ TROUBLESHOOTING:

### Lỗi: Assets không load
```bash
# Chạy lệnh để đảm bảo assets được copy
php artisan storage:link
php artisan config:clear
php artisan cache:clear
```

### Lỗi: CSS/JS không hiển thị đúng
Kiểm tra đường dẫn trong `resources/views/layouts/app.blade.php`:
```blade
{{ asset('assets/css/backend.css') }}
```

### Lỗi: Route not found
```bash
php artisan route:clear
php artisan route:cache
```

---

## 📦 PACKAGES BỔ SUNG NÊN CÀI:

```bash
# File management
composer require intervention/image

# Excel import/export
composer require maatwebsite/excel

# PDF generation
composer require barryvdh/laravel-dompdf

# API development
composer require laravel/sanctum
```

---

## 🔒 BẢO MẬT:

### 1. Middleware cho routes:
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### 2. CSRF Protection đã được bật mặc định
Trong forms, luôn thêm:
```blade
@csrf
```

### 3. Validation cho file uploads:
```php
$request->validate([
    'file' => 'required|mimes:pdf,doc,docx|max:10240',
]);
```

---

## 🎯 NEXT STEPS:

1. ✅ Khởi động server và test (`php artisan serve`)
2. ⬜ Cài đặt authentication (Laravel Breeze)
3. ⬜ Kết nối database
4. ⬜ Tạo models và migrations
5. ⬜ Implement file upload functionality
6. ⬜ Thêm user management
7. ⬜ Thêm file sharing features
8. ⬜ Deploy lên production

---

## 📞 SUPPORT:

Nếu gặp vấn đề, hãy kiểm tra:
- Laravel Docs: https://laravel.com/docs/12.x
- Laravel Forums: https://laracasts.com/discuss
- Stack Overflow: https://stackoverflow.com/questions/tagged/laravel

**Chúc bạn thành công với dự án! 🚀**
