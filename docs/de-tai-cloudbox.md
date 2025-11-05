# CloudBox — Giới thiệu và Phân tích đề tài

## 1. Giới thiệu về đề tài

CloudBox là một ứng dụng web giúp người dùng lưu trữ, quản lý và chia sẻ tệp/thư mục trên nền tảng đám mây, tương tự các dịch vụ như Google Drive hay Dropbox nhưng phù hợp để triển khai nội bộ hoặc tuỳ biến theo nhu cầu doanh nghiệp.

- Bối cảnh: Nhu cầu làm việc từ xa và cộng tác tăng cao, dữ liệu cần được tổ chức khoa học, an toàn và dễ chia sẻ.
- Mục tiêu: Xây dựng hệ thống quản lý tệp/thư mục thân thiện, bảo mật, hỗ trợ phân quyền chia sẻ, đánh dấu yêu thích, xoá mềm (thùng rác) và cấu hình riêng tư theo thư mục.
- Phạm vi: Ứng dụng web dựa trên Laravel (PHP), frontend tích hợp Vite/Tailwind; lưu trữ tệp theo chuẩn Laravel Filesystem.
- Đối tượng: Cá nhân, nhóm, doanh nghiệp muốn tự chủ hạ tầng lưu trữ và chia sẻ tài liệu.
- Giá trị: Chủ động dữ liệu, tích hợp linh hoạt với hệ thống hiện hữu, tối ưu chi phí và tính tuỳ biến.

## 2. Phân tích đề tài

### 2.1. Yêu cầu chức năng chính

1) Xác thực và hồ sơ người dùng
- Đăng ký/đăng nhập, bảo vệ phiên, đổi mật khẩu.
- Hồ sơ người dùng với các trường mở rộng (ví dụ: thông tin cá nhân, địa chỉ, ảnh đại diện theo migrations/profile fields trong dự án).
- Phân quyền quản trị (is_admin) cho tác vụ hệ thống.

2) Quản lý thư mục (Folders)
- Tạo/sửa/đổi tên/di chuyển thư mục theo cấu trúc cây (parent_id).
- Xoá mềm và khôi phục (trashed_at) để đảm bảo an toàn dữ liệu.
- Đánh dấu yêu thích (is_favorite) để truy cập nhanh.
- Cài đặt riêng tư/riêng tư có liên kết/công khai theo trường privacy settings ở thư mục.

3) Quản lý tệp (Files)
- Tải lên, đổi tên, di chuyển giữa thư mục; tải xuống.
- Xoá mềm và khôi phục tương tự thư mục.
- Lưu trữ vật lý qua Laravel Filesystem (public/storage) theo cấu hình `config/filesystems.php`.

4) Chia sẻ (Sharing)
- Chia sẻ tệp/thư mục theo người dùng hoặc liên kết (FileShare/FolderShare).
- Thiết lập phạm vi truy cập tối thiểu: xem/tải; có thể mở rộng thêm quyền sửa/xoá trong giai đoạn sau.

5) Phân loại (Categories)
- Gắn tệp vào danh mục để lọc/tra cứu nhanh.

6) Tìm kiếm và lọc
- Tìm theo tên, loại tệp, danh mục; lọc theo yêu thích/đã xóa/riêng tư.

7) Quản trị hệ thống
- Quản lý người dùng, theo dõi dung lượng sử dụng, cấu hình giới hạn tải lên.

### 2.2. Mô hình dữ liệu (khái quát)

Các thực thể chính (tham chiếu từ `app/Models` và `database/migrations` trong repo):
- User: thông tin đăng nhập, hồ sơ mở rộng, cờ `is_admin`, địa chỉ.
- Folder: thuộc về User, có thể có `parent_id`, hỗ trợ `trashed_at`, `is_favorite`, và trường thiết lập riêng tư.
- File: thuộc về User và một Folder; lưu meta cơ bản của tệp (tên, kích thước, loại…), lưu đường dẫn qua Filesystem.
- Category: danh mục để gắn tệp.
- FileShare / FolderShare: bản ghi chia sẻ kèm đối tượng nhận và phạm vi quyền truy cập.

Quan hệ (định hướng):
- User 1—N Folders, User 1—N Files
- Folder 1—N Files, Folder N—1 Folder (cha)
- File/Folder N—N User thông qua FileShare/FolderShare
- File N—N Category (có thể trực tiếp hoặc thông qua bảng trung gian tuỳ thiết kế)

Lưu ý: Trường cụ thể tuân theo các migration trong thư mục `database/migrations` (ví dụ: thêm `trashed_at`, `privacy_settings`, `is_favorite`, `is_admin`, `address`, các profile fields…).

### 2.3. Kiến trúc & công nghệ

- Nền tảng: Laravel (PHP) theo mô hình MVC, Eloquent ORM, Migration/Seeder.
- Frontend: Vite + Tailwind CSS (các file `vite.config.js`, `tailwind.config.js`, `postcss.config.js`).
- Lưu trữ tệp: Laravel Filesystem; public assets tại `public/`, liên kết `public/storage` tới `storage/app/public`.
- CSDL: MySQL hoặc tương thích (cấu hình qua `config/database.php`).
- Bảo mật: Middleware xác thực, CSRF, validation form, kiểm soát truy cập theo người dùng/quyền.

### 2.4. Use cases tiêu biểu

- Tạo thư mục: Người dùng tạo thư mục mới trong thư mục gốc hoặc thư mục cha bất kỳ.
- Upload tệp: Chọn thư mục đích, tải lên, lưu meta và đồng bộ Filesystem.
- Chia sẻ thư mục: Chủ sở hữu thiết lập chia sẻ cho người dùng khác hoặc tạo liên kết chia sẻ.
- Đánh dấu yêu thích: Gắn/un-đánh dấu để truy cập nhanh ở mục Yêu thích.
- Xoá mềm và khôi phục: Đưa tệp/thư mục vào Thùng rác, khôi phục khi cần hoặc xoá vĩnh viễn.
- Quản trị: Tài khoản admin truy cập trang quản trị để xem và quản lý người dùng/dung lượng.

### 2.5. Luồng nghiệp vụ chính (tóm tắt)

1) Upload tệp: Chọn thư mục đích → kiểm tra quota → lưu tệp vào storage → ghi metadata vào DB → phản hồi thành công.
2) Chia sẻ: Chủ sở hữu mở màn hình chia sẻ → chọn đối tượng nhận/quyền → lưu FileShare/FolderShare → hệ thống phát sinh đường dẫn/truy cập kiểm soát.
3) Xoá mềm: Người dùng xoá → cập nhật `trashed_at` → ẩn ở danh sách chính → hiển thị tại Thùng rác → khôi phục hoặc xoá vĩnh viễn.

### 2.6. Yêu cầu phi chức năng

- Bảo mật: Mã hoá mật khẩu, kiểm soát quyền truy cập, lọc đầu vào, header bảo mật.
- Hiệu năng: Phân trang, lazy loading, tránh N+1, cache khi phù hợp.
- Khả năng mở rộng: Thiết kế dịch vụ và lớp Repository (nếu cần), tách logic chia sẻ/quyền.
- Tin cậy & an toàn dữ liệu: Xoá mềm, backup định kỳ, kiểm tra tính toàn vẹn tệp.
- Trải nghiệm người dùng: Giao diện nhất quán, phản hồi nhanh, trạng thái tải lên rõ ràng.

### 2.7. Rủi ro & giả định

- Dung lượng lưu trữ: Cần giới hạn dung lượng mỗi người dùng và tổng hệ thống.
- Bảo mật liên kết chia sẻ: Cần thời hạn/thu hồi liên kết, chống dò URL (nếu dùng token).
- Loại tệp nguy hiểm: Cân nhắc chặn thực thi/scan (ngoài phạm vi MVP).
- Tương thích trình duyệt và kích thước tệp lớn: Hỗ trợ chunk upload ở giai đoạn sau nếu cần.

### 2.8. Lộ trình phát triển (đề xuất)

- MVP: Đăng nhập/đăng ký, tạo thư mục, upload/tải xuống, xoá mềm/khôi phục, chia sẻ cơ bản, yêu thích.
- Phase 2: Tìm kiếm nâng cao, phân loại theo Category, trang quản trị, hồ sơ người dùng mở rộng.
- Phase 3: Liên kết chia sẻ có thời hạn, nhật ký hoạt động, chunk upload, phiên bản tệp (versioning).

### 2.9. Tiêu chí nghiệm thu

- Đáp ứng chức năng MVP không lỗi nghiêm trọng (PASS tests cơ bản, thao tác CRUD ổn định).
- Quyền truy cập đảm bảo: người không có quyền không xem/không tải được nội dung riêng tư.
- Upload/tải xuống hoạt động với tệp cỡ vừa (ví dụ ≤ 50MB) trong điều kiện mặc định của máy chủ.

### 2.10. Liên hệ với mã nguồn trong repo

- Models: `app/Models/{User,Folder,File,Category,FileShare,FolderShare}.php`
- Migrations: `database/migrations/*` (bao gồm thêm `trashed_at`, `privacy_settings`, `is_favorite`, `is_admin`, `address`, profile fields…)
- Cấu hình: `config/filesystems.php`, `config/app.php`, `config/auth.php`
- Tuyến: `routes/web.php`, `routes/auth.php`
- Giao diện: `resources/views`, tài nguyên: `resources/js`, `resources/css`

---

Tài liệu này nhằm định hướng tổng thể cho việc triển khai CloudBox trong dự án hiện tại. Có thể bổ sung chi tiết kỹ thuật (API, schema trường cụ thể, ma trận quyền) khi chốt thiết kế cuối.