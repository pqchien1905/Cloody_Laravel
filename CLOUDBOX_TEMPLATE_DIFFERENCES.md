# CloudBOX Template - Điểm khác biệt cần điều chỉnh

## Tổng quan
Document này liệt kê các điểm khác biệt giữa Laravel implementation hiện tại và CloudBOX template gốc.

---

## 1. DASHBOARD (index.html vs dashboard.blade.php)

### ✅ Đã có:
- Welcome banner với background image
- Quick Access section
- Documents section với document thumbnails
- Folders grid
- Files table
- Storage card

### ⚠️ Cần thêm/sửa:
1. **Quick Access Icons** (Calendar, Keep, Tasks) - Dropdown bên phải dashboard
   - Location: Sau dropdown "My Drive"
   - Icons: Calendar, Keep (lightbulb), Tasks
   
2. **Image thumbnails cho Quick Access**
   - Thay đổi từ PDF/DOC icons thành folder thumbnails
   - Files: `layouts/mydrive/folder-1.png`, `folder-2.png`

3. **Folder cards icon size**
   - Class hiện tại dùng `ri-folder-line`
   - Template dùng `ri-file-copy-line` với class `icon-small`

---

## 2. SIDEBAR (sidebar.blade.php)

### ✅ Đã có:
- Logo
- Create New dropdown
- Menu structure
- Storage bottom section

### ⚠️ Cần thêm/sửa:
1. **Storage bottom section**
   - Đã có nhưng cần kiểm tra class `sidebar-bottom`
   - Progress bar với `iq-progress` và `progress-1`
   - Button "Buy Storage" với class `view-more`

2. **Menu "Other Page"**
   - Template có full submenu: User Details, UI Elements, Authentication, Pricing, Error, Blank Page, Maintenance
   - Laravel chỉ có Pages submenu đơn giản

---

## 3. FILES PAGE (page-files.html vs files.blade.php)

### ✅ Đã có:
- Statistics cards
- Search và filters
- Files table
- Pagination

### ⚠️ Cần thêm/sửa:
1. **Card styling**
   - Cần class `card-transparent` cho header sections
   - Class `card-block card-stretch card-height`

2. **Table structure**
   - Template dùng `tbl-server-info` class
   - Dropdown actions với more icon

---

## 4. FOLDERS PAGE (page-folders.html vs folders.blade.php)

### ✅ Đã có:
- Folder grid
- Create/Edit modals
- Delete functionality

### ⚠️ Cần thêm/sửa:
1. **Folder card structure**
   - Icon với `icon-small bg-{color}` class
   - Info với clock và file count icons
   - Dropdown menu với 5 actions (View, Delete, Edit, Print, Download)

---

## 5. TOPNAV (topnav.blade.php)

### ✅ Đã có:
- Logo
- Search bar
- Notifications
- Settings
- Profile dropdown

### ⚠️ Cần thêm/sửa:
1. **Advanced search dropdown**
   - Dropdown với file type filters (PDFs, Documents, Spreadsheet, etc.)
   
2. **Profile dropdown structure**
   - Hiện đang dùng custom design
   - Template có multiple accounts display
   - "Add account" button

---

## 6. FAVORITES & TRASH

### ✅ Đã có:
- Basic functionality
- File listing

### ⚠️ Cần thêm/sửa:
1. Matching exact template styling
2. Empty states
3. Action buttons styling

---

## Ưu tiên thực hiện:

### HIGH PRIORITY:
1. ✅ Dashboard Quick Access dropdown (Calendar, Keep, Tasks)
2. ✅ Folder card styling với icon-small
3. ✅ Storage bottom section

### MEDIUM PRIORITY:
4. Advanced search dropdown trong topnav
5. Profile dropdown với multiple accounts
6. "Other Page" menu items

### LOW PRIORITY:
7. Empty states styling
8. Button hover effects
9. Card shadows và spacing fine-tuning

---

## CSS Classes cần chú ý:

```css
/* Card classes */
.card-transparent
.card-block
.card-stretch
.card-height

/* Icon classes */
.icon-small
.iq-icon-box
.iq-icon-box-2

/* Folder classes */
.folder
.iq-thumb
.iq-image-overlay

/* Progress */
.iq-progress-bar
.iq-progress
.progress-1

/* Sidebar */
.sidebar-bottom
.view-more

/* Table */
.tbl-server-info
.files-table
```

---

## Assets cần kiểm tra:

1. ✅ `assets/images/layouts/mydrive/background.png` - Welcome banner
2. ✅ `assets/images/layouts/mydrive/folder-1.png` - Quick Access
3. ✅ `assets/images/layouts/mydrive/folder-2.png` - Quick Access
4. ✅ `assets/images/layouts/page-1/pdf.png` - Document icons
5. ✅ `assets/images/layouts/page-1/doc.png`
6. ✅ `assets/images/layouts/page-1/xlsx.png`
7. ✅ `assets/images/layouts/page-1/ppt.png`

---

## Cập nhật tiếp theo:

Để giao diện giống 100%, cần thực hiện từng điểm ưu tiên trên. Bắt đầu từ HIGH PRIORITY và test từng trang một.

**Lưu ý:** Mọi thay đổi cần giữ nguyên Laravel functionality (routes, auth, database queries).
