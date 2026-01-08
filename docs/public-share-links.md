# Hướng Dẫn Tính Năng Chia Sẻ File/Folder Bằng Link Công Khai Có Thời Hạn

## Tổng Quan

Tính năng mới này cho phép bạn chia sẻ file và folder bằng 2 cách:
1. **Chia sẻ với người dùng cụ thể**: Gửi email thông báo cho người dùng đã đăng ký trong hệ thống
2. **Tạo link công khai**: Tạo link có thể chia sẻ với bất kỳ ai, có hoặc không có thời hạn

## Các Tính Năng Chính

### 1. Chia Sẻ File

#### Chia sẻ với người dùng
```javascript
// POST /cloody/files/{id}/share
{
    "share_type": "user",
    "email": "user@example.com",
    "permission": "view" // hoặc "download"
}
```

#### Tạo link công khai
```javascript
// POST /cloody/files/{id}/share
{
    "share_type": "public",
    "permission": "download",
    "expires_in_days": 7 // Tùy chọn: 1, 7, 30, 90, 365 hoặc null (không giới hạn)
}
```

### 2. Chia Sẻ Folder

Tương tự như file, nhưng sử dụng endpoint `/cloody/folders/{id}/share`

### 3. Quyền Truy Cập

- **view**: Chỉ xem nội dung
- **download**: Xem và tải xuống

### 4. Thời Hạn

- Link có thể có thời hạn từ 1 ngày đến 365 ngày
- Hoặc không giới hạn thời gian
- Link hết hạn sẽ không thể truy cập được nữa

## Cấu Trúc Database

### Bảng `file_shares`
```sql
- id: bigint
- file_id: bigint (foreign key)
- shared_by: bigint (foreign key users)
- shared_with: bigint nullable (foreign key users) - null cho link công khai
- share_token: string unique (token để truy cập)
- permission: enum('view', 'download', 'edit')
- is_public: boolean (true = link công khai)
- expires_at: timestamp nullable
- created_at, updated_at
```

### Bảng `folder_shares`
Cấu trúc tương tự `file_shares`, với `folder_id` thay vì `file_id`

## Routes

### Routes Công Khai (không cần đăng nhập)
```php
// Xem file được chia sẻ
GET /shared/file/{token}

// Tải file được chia sẻ
GET /shared/file/{token}/download

// Xem folder được chia sẻ
GET /shared/folder/{token}

// Tải folder được chia sẻ (ZIP)
GET /shared/folder/{token}/download
```

### Routes Bảo Vệ (cần đăng nhập)
```php
// Tạo share cho file
POST /cloody/files/{id}/share

// Danh sách shares của file
GET /cloody/files/{id}/shares

// Tạo share cho folder
POST /cloody/folders/{id}/share

// Danh sách shares của folder
GET /cloody/folders/{id}/shares

// Thu hồi quyền chia sẻ
DELETE /cloody/shares/{id}
```

## API Response Examples

### Tạo link công khai thành công
```json
{
    "success": true,
    "message": "Link công khai đã được tạo thành công",
    "share_url": "https://yourdomain.com/shared/file/abc123xyz...",
    "expires_at": "2026-01-09 15:30:00"
}
```

### Danh sách shares
```json
{
    "success": true,
    "shares": [
        {
            "id": 1,
            "is_public": true,
            "permission": "download",
            "share_url": "https://yourdomain.com/shared/file/abc123xyz...",
            "expires_at": "2026-01-09 15:30:00",
            "is_expired": false,
            "shared_with": null,
            "created_at": "2026-01-02 10:00:00"
        },
        {
            "id": 2,
            "is_public": false,
            "permission": "view",
            "share_url": "https://yourdomain.com/shared/file/def456uvw...",
            "expires_at": null,
            "is_expired": false,
            "shared_with": {
                "id": 5,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "created_at": "2026-01-01 14:20:00"
        }
    ]
}
```

## Sử Dụng Trong Giao Diện

### 1. Thêm nút Share vào giao diện
```html
<button onclick="shareManager.openShareModal({{ $file->id }}, 'file')" class="btn btn-primary">
    <i class="fas fa-share-alt"></i> Chia sẻ
</button>
```

### 2. Include modal components
```blade
@include('components.share-modals')
```

### 3. Include JavaScript
```html
<script src="{{ asset('assets/js/share-manager.js') }}"></script>
```

## Models

### FileShare Model
```php
// Kiểm tra đã hết hạn chưa
$share->isExpired(); // true/false

// Scope lấy shares còn hiệu lực
FileShare::active()->get();

// Relationships
$share->file; // File được chia sẻ
$share->sharedBy; // Người chia sẻ
$share->sharedWith; // Người nhận (null nếu là public)
```

### FolderShare Model
Tương tự FileShare

## Bảo Mật

1. **Token ngẫu nhiên**: Mỗi share có token ngẫu nhiên 32 ký tự
2. **Kiểm tra hết hạn**: Tự động kiểm tra thời hạn khi truy cập
3. **Kiểm tra quyền**: Validate quyền trước khi cho phép download
4. **Xóa cascade**: Khi xóa file/folder, tất cả shares cũng bị xóa

## Email Notifications

Khi chia sẻ với người dùng cụ thể, hệ thống sẽ:
1. Tạo job `SendFileShareNotification` hoặc `SendFolderShareNotification`
2. Gửi email thông báo qua queue
3. Email chứa link truy cập và thông tin người chia sẻ

## Testing

### Test tạo link công khai
```bash
curl -X POST http://localhost/cloody/files/1/share \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "share_type": "public",
    "permission": "download",
    "expires_in_days": 7
  }'
```

### Test truy cập link được chia sẻ
```bash
curl http://localhost/shared/file/abc123xyz...
```

## Lưu Ý

1. Folder download sẽ được nén thành file ZIP
2. Cần có `ZipArchive` extension trong PHP
3. Link công khai không cần đăng nhập để truy cập
4. Người dùng có thể thu hồi bất kỳ share nào họ đã tạo
5. Admin có thể xóa/quản lý tất cả shares trong admin panel

## Cải Tiến Trong Tương Lai

- [ ] Thêm mật khẩu bảo vệ cho link công khai
- [ ] Giới hạn số lượt truy cập
- [ ] Thống kê lượt xem/tải xuống
- [ ] Chia sẻ nhiều file cùng lúc
- [ ] Tạo album/collection để chia sẻ
- [ ] QR code cho link chia sẻ
- [ ] Watermark cho ảnh được chia sẻ
