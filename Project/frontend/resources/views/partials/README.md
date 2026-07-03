# resources/views/partials/ — Shared partial views

Chứa các "mảnh" (partial) được chia sẻ giữa nhiều pages.

## 📌 navbar.blade.php

**Công dụng:** Navbar header — hiển thị ở trên cùng mỗi trang.

### Bao gồm:
- **Logo/branding** — SmartWater Admin
- **Search bar** — input tìm kiếm
- **Notifications dropdown** — thông báo (từ View Composer)
- **User avatar dropdown** — menu Profile, Settings, Logout
- **Responsive toggle** — collapse menu mobile

### Dữ liệu

Tự động nhận từ View Composer:
- `currentUser` — thông tin user đăng nhập
- `navNotifications` — danh sách thông báo

### Styling

- Bootstrap navbar component + custom CSS (app.css)
- Truliva blue theme (#1668e3)
- Hover effects, badges

---

## 📌 sidebar.blade.php

**Công dụng:** Navigation sidebar — danh sách menu chính.

### Bao gồm:
- **Logo** — SmartWater branding
- **Menu 11 items** — Danh sách trang chính (Dashboard, Products, Inventory, etc.)
- **Menu groups** — Organized by section
- **Active state** — Highlight current page dùng `request()->routeIs()`
- **Collapse/toggle** — Thu gọn sidebar trên desktop/mobile

### Menu Items:
1. Dashboard
2. Quản lý sản phẩm
3. Kho thiết bị
4. Danh sách lô hàng
5. Danh sách khách hàng
6. Hợp đồng
7. Danh sách thiết bị
8. Danh sách nhân viên
9. Lịch sử hoạt động
10. Hồ sơ cá nhân
11. Đăng xuất

### Styling

- Width: 264px (expanded), 78px (collapsed)
- Truliva blue background
- Icon + text labels
- Scroll-friendly (overflow-y: auto)
- Responsive: collapse tự động trên mobile

---

## 📌 footer.blade.php

**Công dụng:** Footer — hiển thị ở dưới cùng pages.

### Bao gồm:
- **Copyright** — © 2024 SmartWater
- **Link footer** — Links to docs, support, etc.
- **Simple static content** — Không có dữ liệu động

---

## 🎯 Cách sử dụng

Các partials tự động include trong `layouts/app.blade.php`:

```blade
<!-- Navbar -->
@include('partials.navbar')

<!-- Sidebar -->
@include('partials.sidebar')

<!-- Content -->
@yield('content')

<!-- Footer -->
@include('partials.footer')
```

## 💡 Chỉnh sửa

- **Thêm menu item:** Sửa `sidebar.blade.php` (thêm `<li>` + route)
- **Thay logo:** Update navbar/sidebar HTML
- **Đổi style:** Sửa `public/css/app.css` (CSS variables, sidebar width, etc.)

⚠️ Thay đổi ở partials sẽ ảnh hưởng **tất cả pages** — test kỹ
