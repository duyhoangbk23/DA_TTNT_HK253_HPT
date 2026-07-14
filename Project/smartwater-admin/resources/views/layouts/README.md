# resources/views/layouts/ — Master Layout

Chứa layout template chính của ứng dụng.

## 📌 app.blade.php

**Công dụng:** Master layout — khung chính của tất cả pages (trừ login).

### Cấu trúc HTML

```
<html>
  <head>
    <!-- Meta, CSS (Bootstrap 5, Bootstrap Icons, DataTables, ApexCharts via CDN) -->
  </head>
  <body>
    <div class="layout">
      <sidebar> (partials/sidebar.blade.php)
      <main>
        <navbar> (partials/navbar.blade.php)
        <breadcrumb> (@yield)
        <content> (@yield)
        <footer> (partials/footer.blade.php)
      </main>
    </div>
  </body>
</html>
```

### Khai báo @yield

| Yield | Dùng ở | Ví dụ |
|-------|--------|-------|
| `@yield('title')` | `<title>` tag | "Danh sách sản phẩm" |
| `@yield('page-title')` | Tiêu đề trang | "Quản lý sản phẩm" |
| `@yield('page-subtitle')` | Mô tả trang | "Danh sách tất cả sản phẩm" |
| `@yield('breadcrumb')` | Breadcrumb navigation | Trang hiện tại |
| `@yield('content')` | Nội dung trang | HTML form, table, chart |

### CDN Libraries

Load từ CDN (không cần npm install):
- **Bootstrap 5** — CSS framework
- **Bootstrap Icons** — Icon library (600+ icons)
- **jQuery** — JavaScript utility
- **DataTables** — Dynamic table management
- **ApexCharts** — Chart library
- **Custom CSS** — `public/css/app.css`
- **Custom JS** — `public/js/app.js`

### CSS & JS bộ túc

1. **public/css/app.css** — Custom theme (Truliva blue, sidebar, navbar, KPI cards)
2. **public/js/app.js** — Sidebar toggle, DataTables init, Chart helpers

## 🎯 Cách sử dụng

Tất cả pages (trừ login) extend layout này:

```blade
@extends('layouts.app')

@section('title', 'Tên trang')
@section('page-title', 'Tiêu đề')
@section('breadcrumb')
    <li class="breadcrumb-item active">Tên trang</li>
@endsection

@section('content')
    <!-- Nội dung trang -->
@endsection
```

## ℹ️ Đặc điểm

✅ Single source of truth cho layout
✅ Tất cả pages có consistent navbar, sidebar, footer
✅ CSS & JS libraries centralized
✅ View Composer tự động inject `currentUser`, `navNotifications`
❌ KHÔNG có dynamic sidebar menu — hard-coded trong `partials/sidebar.blade.php`
