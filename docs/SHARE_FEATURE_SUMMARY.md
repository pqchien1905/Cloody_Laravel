# Tính Năng Chia Sẻ File/Folder Bằng Link Công Khai Có Thời Hạn

## ✅ Đã Hoàn Thành

### 1. **Backend - Controllers**
- ✅ Cập nhật `FileShareController.php` - hỗ trợ tạo link công khai và chia sẻ với user
- ✅ Cập nhật `FolderShareController.php` - tương tự cho folder
- ✅ Thêm methods: `show()`, `download()`, `listShares()` cho cả file và folder

### 2. **Models**
- ✅ `FileShare` model - đã có sẵn với đầy đủ fields (is_public, expires_at, share_token)
- ✅ `FolderShare` model - đã có sẵn với đầy đủ fields
- ✅ Thêm relationship `shares()` vào `Folder` model

### 3. **Database**
- ✅ Migration đã có sẵn với đầy đủ cấu trúc:
  - `share_token` - unique token cho mỗi link
  - `is_public` - phân biệt link công khai vs chia sẻ riêng
  - `expires_at` - thời hạn của link
  - `permission` - quyền truy cập (view, download)

### 4. **Routes**
- ✅ Routes công khai (không cần login):
  - `GET /shared/file/{token}` - xem file
  - `GET /shared/file/{token}/download` - tải file
  - `GET /shared/folder/{token}` - xem folder
  - `GET /shared/folder/{token}/download` - tải folder (ZIP)
  
- ✅ Routes bảo vệ (cần login):
  - `POST /cloody/files/{id}/share` - tạo share cho file
  - `GET /cloody/files/{id}/shares` - danh sách shares của file
  - `POST /cloody/folders/{id}/share` - tạo share cho folder
  - `GET /cloody/folders/{id}/shares` - danh sách shares của folder
  - `DELETE /cloody/shares/{id}` - thu hồi share

### 5. **Views**
- ✅ `file-shared.blade.php` - hiển thị file được chia sẻ (đã cập nhật)
- ✅ `folder-shared.blade.php` - hiển thị folder được chia sẻ (mới)
- ✅ `share-modals.blade.php` - modal để tạo và quản lý shares (mới)

### 6. **JavaScript**
- ✅ `share-manager.js` - class quản lý tất cả logic chia sẻ:
  - Tạo link công khai
  - Chia sẻ với user
  - Hiển thị danh sách shares
  - Thu hồi shares
  - Copy link vào clipboard

### 7. **Documentation**
- ✅ `public-share-links.md` - tài liệu chi tiết về tính năng

## 🚀 Cách Sử Dụng

### 1. Include Modal và JavaScript trong layout
Thêm vào file layout chính (vd: `app.blade.php`):

```blade
<!-- Trước </body> -->
@include('components.share-modals')
<script src="{{ asset('assets/js/share-manager.js') }}"></script>
```

### 2. Thêm nút Share vào giao diện

**Cho File:**
```html
<button onclick="shareManager.openShareModal({{ $file->id }}, 'file')" class="btn btn-primary">
    <i class="fas fa-share-alt"></i> Chia sẻ
</button>
```

**Cho Folder:**
```html
<button onclick="shareManager.openShareModal({{ $folder->id }}, 'folder')" class="btn btn-primary">
    <i class="fas fa-share-alt"></i> Chia sẻ
</button>
```

### 3. API Endpoints

**Tạo link công khai:**
```javascript
POST /cloody/files/{id}/share
Content-Type: application/json

{
    "share_type": "public",
    "permission": "download",
    "expires_in_days": 7  // optional: 1, 7, 30, 90, 365, hoặc null
}
```

**Chia sẻ với user:**
```javascript
POST /cloody/files/{id}/share
Content-Type: application/json

{
    "share_type": "user",
    "email": "user@example.com",
    "permission": "view"
}
```

**Lấy danh sách shares:**
```javascript
GET /cloody/files/{id}/shares
```

**Thu hồi share:**
```javascript
DELETE /cloody/shares/{shareId}
```

## 📋 Các Quyền Truy Cập

- **view**: Chỉ xem, không tải xuống
- **download**: Xem và tải xuống

## ⏰ Tùy Chọn Thời Hạn

- 1 ngày
- 7 ngày
- 30 ngày
- 90 ngày
- 365 ngày
- Không giới hạn (để trống `expires_in_days`)

## 🔒 Bảo Mật

1. **Token ngẫu nhiên 32 ký tự** - tự động tạo khi tạo share
2. **Kiểm tra hết hạn** - không cho phép truy cập link đã hết hạn
3. **Kiểm tra quyền** - validate permission trước khi download
4. **Xóa cascade** - xóa file/folder sẽ xóa tất cả shares liên quan

## 📝 Response Format

**Thành công:**
```json
{
    "success": true,
    "message": "Link công khai đã được tạo thành công",
    "share_url": "http://yourdomain.com/shared/file/abc123...",
    "expires_at": "2026-01-09 15:30:00"
}
```

**Lỗi:**
```json
{
    "success": false,
    "message": "Email người nhận không tồn tại trong hệ thống"
}
```

## 🎨 UI Features

Modal chia sẻ có 3 tabs:
1. **Chia sẻ với người dùng** - nhập email và chọn quyền
2. **Tạo link công khai** - chọn quyền và thời hạn
3. **Danh sách shares hiện tại** - xem và quản lý các shares đã tạo

## 🔄 Queue & Notifications

- Email thông báo được gửi qua queue (không đồng bộ)
- Sử dụng jobs: `SendFileShareNotification`, `SendFolderShareNotification`
- Cần chạy queue worker: `php artisan queue:work`

## 📦 Dependencies

Không cần cài thêm package mới. Sử dụng:
- Laravel Framework (đã có)
- Bootstrap 5 (cho modal)
- Font Awesome (cho icons)
- ZipArchive (built-in PHP extension - cho download folder)

## 🧪 Testing

```bash
# Test tạo public link
curl -X POST http://localhost/cloody/files/1/share \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: token" \
  -d '{"share_type":"public","permission":"download","expires_in_days":7}'

# Test truy cập link
curl http://localhost/shared/file/{token}

# Test download
curl http://localhost/shared/file/{token}/download
```

## 📖 Xem Thêm

Chi tiết đầy đủ trong: [docs/public-share-links.md](docs/public-share-links.md)
