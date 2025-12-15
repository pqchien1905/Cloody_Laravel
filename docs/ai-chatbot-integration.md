# Hướng dẫn tích hợp AI Chatbot với Google Gemini

## Tổng quan

Cloody đã được tích hợp một AI chatbot thông minh sử dụng Google Gemini AI. Chatbot sẽ xuất hiện ở góc phải dưới màn hình và có thể trả lời các câu hỏi về:
- Cách sử dụng Cloody
- Hướng dẫn tính năng
- Giải đáp thắc mắc về lưu trữ, thanh toán
- Gợi ý và mẹo sử dụng

## Các file đã tạo

### 1. Frontend Components
- `resources/views/components/ai-chat-widget.blade.php` - UI component cho chat widget
- `public/assets/js/ai-chat.js` - JavaScript xử lý chat interaction

### 2. Backend
- `app/Http/Controllers/AIChatController.php` - Controller xử lý API chat
- Route mới trong `routes/web.php`: `POST /ai-chat`

### 3. Layout Integration
- Đã thêm chat widget vào `resources/views/layouts/app.blade.php`

## Cách cấu hình

### Bước 1: Lấy API Key từ Google AI Studio

1. Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Đăng nhập bằng tài khoản Google
3. Click "Create API Key" hoặc "Get API Key"
4. Copy API key vừa tạo

### Bước 2: Cấu hình .env

Mở file `.env` trong thư mục gốc của project và thêm dòng sau:

```env
GEMINI_API_KEY=your_api_key_here
```

Thay `your_api_key_here` bằng API key bạn vừa lấy từ Google AI Studio.

Ví dụ:
```env
GEMINI_API_KEY=AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxx
```

### Bước 3: Clear cache Laravel (tùy chọn)

Nếu ứng dụng đang chạy, chạy lệnh sau để clear cache:

```bash
php artisan config:clear
php artisan cache:clear
```

### Bước 4: Khởi động hoặc khởi động lại server

Nếu server chưa chạy:
```bash
php artisan serve
```

Nếu đang dùng Laragon, khởi động lại Apache/Nginx.

## Cách sử dụng

1. Đăng nhập vào Cloody
2. Tìm biểu tượng chat (icon bong bóng chat màu tím) ở góc phải dưới màn hình
3. Click vào icon để mở chat box
4. Nhập câu hỏi và nhấn Enter hoặc click nút gửi
5. AI sẽ trả lời câu hỏi của bạn

## Tính năng

### UI Features
- ✅ Chat button cố định ở góc phải dưới
- ✅ Chat box có thể mở/đóng
- ✅ Hiển thị typing indicator khi AI đang suy nghĩ
- ✅ Hỗ trợ markdown formatting (bold, italic, code)
- ✅ Auto-resize textarea
- ✅ Responsive design (mobile-friendly)
- ✅ Smooth animations

### AI Features
- ✅ Hiểu tiếng Việt tự nhiên
- ✅ Context về Cloody và các tính năng
- ✅ Trả lời nhanh và chính xác
- ✅ Phong cách thân thiện, chuyên nghiệp

## Giới hạn & Lưu ý

### Google Gemini Free Tier
- **60 requests/phút**
- **1,500 requests/ngày**
- Đủ cho hầu hết các ứng dụng nhỏ và vừa

### Bảo mật
- API key được lưu trong `.env` (không commit lên Git)
- Endpoint yêu cầu authentication (middleware `auth`)
- CSRF protection được bật

### Performance
- Timeout: 30 giây cho mỗi request
- Max input: 2000 ký tự
- Max output: 1024 tokens

## Troubleshooting

### Lỗi "API key not configured"
- Kiểm tra file `.env` đã có `GEMINI_API_KEY`
- Chạy `php artisan config:clear`

### Lỗi "Unable to get AI response"
- Kiểm tra API key có hợp lệ không
- Kiểm tra đã vượt quá giới hạn request chưa
- Xem logs tại `storage/logs/laravel.log`

### Chat button không hiển thị
- Đảm bảo đã đăng nhập
- Clear browser cache (Ctrl + Shift + R)
- Kiểm tra console JavaScript có lỗi không

### AI không trả lời bằng tiếng Việt
- Gemini AI đã được cấu hình với system context tiếng Việt
- Thử hỏi bằng tiếng Việt rõ ràng hơn

## Tùy chỉnh

### Thay đổi vị trí chat button
Sửa trong file `resources/views/components/ai-chat-widget.blade.php`:

```css
#ai-chat-widget {
    position: fixed;
    bottom: 20px;  /* Khoảng cách từ đáy */
    right: 20px;   /* Khoảng cách từ bên phải */
}
```

### Thay đổi màu sắc
Tìm các gradient trong CSS:

```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Thay đổi system context
Sửa method `buildSystemContext()` trong `AIChatController.php`

## API Documentation

### Endpoint: POST /ai-chat

**Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "message": "Làm thế nào để upload file?"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "response": "Để upload file vào Cloody, bạn có thể..."
}
```

**Response Error (500):**
```json
{
  "success": false,
  "message": "Error message"
}
```

## Nâng cấp trong tương lai

### Có thể thêm:
- [ ] Lưu lịch sử chat vào database
- [ ] Chat history giữa các session
- [ ] Multiple AI models (GPT-4, Claude, etc.)
- [ ] Voice input/output
- [ ] File upload trong chat
- [ ] Suggested questions
- [ ] Rate limiting per user
- [ ] Analytics và metrics

## Hỗ trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra `storage/logs/laravel.log`
2. Mở Developer Console trong browser (F12)
3. Tạo issue trên GitHub repository

## License

AI Chatbot integration sử dụng Google Gemini API theo [Google Terms of Service](https://ai.google.dev/terms).
