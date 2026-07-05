# resources/views/ — Giao diện Blade (HTML Templates)

Chứa tất cả view Blade để render HTML. Sử dụng Laravel Blade template engine.

## 📁 Cấu trúc chính

| Folder | Tác dụng |
|--------|---------|
| **layouts/** | Master layout `app.blade.php` — khung chính (header, sidebar, content area) |
| **partials/** | Shared partials — navbar, sidebar, footer |
| **components/** | Blade components reusable (status-badge, kpi-card, panel) |
| **auth/** | Trang đăng nhập |
| **dashboard/** | Dashboard (KPI, chart) |
| **products/** | Danh sách & chi tiết sản phẩm |
| **inventory/** | Kho thiết bị |
| **batch/** | Danh sách & chi tiết lô hàng |
| **customers/** | Danh sách & chi tiết khách hàng |
| **contracts/** | Hợp đồng |
| **devices/** | Danh sách & chi tiết thiết bị (+ sensor charts) |
| **employees/** | Danh sách nhân viên |
| **activities/** | Lịch sử hoạt động |
| **profile/** | Hồ sơ cá nhân |

## 🎨 Pattern chung

Tất cả pages (trừ login) follow pattern này:

```blade
@extends('layouts.app')   <!-- Kế thừa master layout -->

@section('title', 'Tên trang')
@section('page-title', 'Tên trang')
@section('page-subtitle', 'Mô tả ngắn')
@section('breadcrumb')
    <li class="breadcrumb-item active">Tên trang</li>
@endsection

@section('content')
    <!-- Nội dung trang -->
@endsection
```

## 📊 Hiển thị dữ liệu

- **Bảng:** Dùng `x-panel` component + DataTables (JS)
- **Biểu đồ:** ApexCharts (CDN)
- **Form:** Bootstrap form controls
- **Status:** `x-status-badge` component (màu xanh/vàng/đỏ)
- **KPI cards:** `x-kpi-card` component

## 🔗 Liên kết partials & components

Tất cả pages tự động load:
- Navbar (từ View Composer + `partials/navbar.blade.php`)
- Sidebar (từ `partials/sidebar.blade.php`)
- Footer (từ `partials/footer.blade.php`)

Xem thêm:
- [layouts/](layouts/) — Master layout
- [partials/](partials/) — Shared partials
- [components/](components/) — Reusable components
