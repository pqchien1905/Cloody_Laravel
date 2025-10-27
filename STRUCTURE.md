# 📋 DANH SÁCH ROUTES VÀ CẤU TRÚC

## 🌐 ROUTES HIỆN CÓ

### Web Routes (routes/web.php)
```php
GET  /              → redirect to /dashboard
GET  /dashboard     → DashboardController@index (name: 'dashboard')
GET  /files         → FileController@index (name: 'files')
POST /logout        → logout function (name: 'logout')
```

---

## 📂 CẤU TRÚC VIEWS

### Layouts
```
resources/views/layouts/
└── app.blade.php              # Master layout với sidebar, topnav, footer
```

### Partials (Components)
```
resources/views/partials/
├── sidebar.blade.php          # Left sidebar navigation
├── topnav.blade.php           # Top navigation bar
└── footer.blade.php           # Footer
```

### Pages
```
resources/views/pages/
├── dashboard.blade.php        # Dashboard page (statistics & recent files)
└── files.blade.php           # Files listing page
```

---

## 🎯 CONTROLLERS

### DashboardController
```php
Namespace: App\Http\Controllers
File: app/Http/Controllers/DashboardController.php

Methods:
- index()  → return view('pages.dashboard')
```

### FileController
```php
Namespace: App\Http\Controllers
File: app/Http/Controllers/FileController.php

Methods:
- index()  → return view('pages.files')
```

---

## 🎨 ASSETS STRUCTURE

```
public/assets/
├── css/
│   ├── backend.css                    # Main CSS
│   └── backend-plugin.min.css         # Plugins CSS
├── js/
│   ├── app.js                         # Main JS
│   └── backend-bundle.min.js          # Bundle JS
├── images/
│   ├── logo.png                       # Main logo
│   ├── favicon.ico                    # Favicon
│   ├── icon/                          # Icons
│   ├── user/                          # User avatars
│   └── page-img/                      # Page images
└── vendor/
    ├── @fortawesome/                  # Font Awesome icons
    ├── line-awesome/                  # Line Awesome icons
    ├── remixicon/                     # Remix icons
    └── doc-viewer/                    # Document viewer plugin
```

---

## 🔗 CÁCH SỬ DỤNG ROUTES

### Trong Blade Templates:
```blade
<!-- Dùng route name (Recommended) -->
<a href="{{ route('dashboard') }}">Dashboard</a>
<a href="{{ route('files') }}">Files</a>

<!-- Check active route -->
<li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

<!-- Dùng trực tiếp URL (Không khuyến khích) -->
<a href="/dashboard">Dashboard</a>
```

### Trong Controllers:
```php
// Redirect to route
return redirect()->route('dashboard');

// Redirect with message
return redirect()->route('files')->with('success', 'File uploaded!');
```

---

## 📝 BLADE DIRECTIVES SỬ DỤNG

### Layouts
```blade
@extends('layouts.app')              # Kế thừa layout
@section('title', 'Page Title')      # Đặt title
@section('content')                  # Bắt đầu section content
@endsection                          # Kết thúc section
```

### Partials
```blade
@include('partials.sidebar')         # Include partial
```

### Assets
```blade
{{ asset('assets/css/backend.css') }}  # Public assets
```

### Stacks (Scripts & Styles)
```blade
@push('styles')                      # Thêm CSS
    <link rel="stylesheet" href="...">
@endpush

@push('scripts')                     # Thêm JS
    <script src="..."></script>
@endpush

@stack('styles')                     # Render CSS stack
@stack('scripts')                    # Render JS stack
```

### Helpers
```blade
{{ route('name') }}                  # Route URL
{{ asset('path') }}                  # Asset URL
{{ csrf_token() }}                   # CSRF token
@csrf                                # CSRF field
```

---

## 🎨 CLASSES BOOTSTRAP & CUSTOM

### Cards
```html
<div class="card">
    <div class="card-header">...</div>
    <div class="card-body">...</div>
</div>
```

### Buttons
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
```

### Tables
```html
<table class="table table-borderless">
    <thead>...</thead>
    <tbody>...</tbody>
</table>
```

### Icons
```html
<i class="las la-home"></i>          <!-- Line Awesome -->
<i class="ri-home-line"></i>          <!-- Remix Icon -->
<i class="fas fa-home"></i>           <!-- Font Awesome -->
```

---

## 🔄 WORKFLOW TẠO TRANG MỚI

### 1. Tạo Route
```php
// routes/web.php
Route::get('/my-page', [MyPageController::class, 'index'])->name('my-page');
```

### 2. Tạo Controller
```bash
php artisan make:controller MyPageController
```

```php
// app/Http/Controllers/MyPageController.php
public function index()
{
    return view('pages.my-page');
}
```

### 3. Tạo View
```blade
<!-- resources/views/pages/my-page.blade.php -->
@extends('layouts.app')

@section('title', 'My Page')

@section('content')
<div class="container-fluid">
    <!-- Content here -->
</div>
@endsection
```

### 4. Thêm vào Sidebar
```blade
<!-- resources/views/partials/sidebar.blade.php -->
<li class="{{ request()->routeIs('my-page') ? 'active' : '' }}">
    <a href="{{ route('my-page') }}">
        <i class="las la-icon"></i><span>My Page</span>
    </a>
</li>
```

---

## 🎯 NAMING CONVENTIONS

### Routes
```php
Route::get('/user-profile', ...)->name('user.profile');  # user.profile
Route::get('/files/upload', ...)->name('files.upload');  # files.upload
```

### Controllers
```
UserProfileController    # PascalCase
FileUploadController    # PascalCase
```

### Views
```
resources/views/pages/user-profile.blade.php    # kebab-case
resources/views/pages/file-upload.blade.php     # kebab-case
```

### Variables
```php
$userName       # camelCase
$fileSize       # camelCase
```

---

## 📦 PACKAGES ĐÃ CÓ SẴN

- Laravel Framework 12.x
- Bootstrap 4.x (trong template)
- jQuery (trong template)
- Font Awesome Icons
- Line Awesome Icons
- Remix Icons
- Doc Viewer Plugin (PDF, Word, Excel viewer)

---

## 🚀 LỆNH ARTISAN HỮU ÍCH

```bash
# Tạo mới
php artisan make:controller NameController
php artisan make:model Name -m
php artisan make:middleware NameMiddleware
php artisan make:request NameRequest

# Database
php artisan migrate
php artisan migrate:rollback
php artisan db:seed

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Routes
php artisan route:list          # Xem tất cả routes
php artisan route:cache         # Cache routes

# Server
php artisan serve               # Start dev server
```

---

## 📚 TÀI LIỆU THAM KHẢO

- Laravel Routing: https://laravel.com/docs/12.x/routing
- Blade Templates: https://laravel.com/docs/12.x/blade
- Controllers: https://laravel.com/docs/12.x/controllers
- Requests: https://laravel.com/docs/12.x/requests
- Responses: https://laravel.com/docs/12.x/responses

---

*Cập nhật: {{ date('Y-m-d') }}*
