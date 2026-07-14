# public/css/ — Custom stylesheets

Chứa các custom CSS files cho theme và styling tùy biến.

## 📌 app.css (~650 dòng)

**Công dụng:** Custom theme toàn bộ ứng dụng — định nghĩa màu sắc, layout, components.

### CSS Variables (Theme)

```css
:root {
    --sw-primary:        #1668e3;  /* Xanh Truliva */
    --sw-primary-dark:   #0f4fb3;  /* Xanh đậm */
    --sw-primary-soft:   #e8f0fe;  /* Xanh nhạt (background) */
    --sw-cyan:           #17b6d6;  /* Cyan accent */
    
    --sw-body-bg:        #f4f6fb;  /* Màu nền body */
    --sw-surface:        #ffffff;  /* Màu card/surface */
    --sw-border:         #e7ebf3;  /* Màu border */
    --sw-muted:          #7a879c;  /* Màu text muted */
    --sw-heading:        #1f2a44;  /* Màu heading */
    --sw-text:           #46536b;  /* Màu text chính */
    
    --sw-sidebar-w:      264px;    /* Sidebar width */
    --sw-sidebar-w-collapsed: 78px;
    --sw-navbar-h:       66px;     /* Navbar height */
    --sw-radius:         14px;     /* Border radius */
    --sw-radius-sm:      10px;
    --sw-shadow:         0 6px 24px rgba(28, 44, 84, .06);
}
```

### Cấu trúc CSS

| Section | Nội dung |
|---------|---------|
| **Variables** | CSS custom properties (màu sắc, sizing, shadows) |
| **Buttons** | `.btn-primary`, `.btn-soft-primary`, `.btn-white` |
| **Layout Shell** | `.app-shell`, `.app-layout` (flexbox main grid) |
| **Sidebar** | `.sidebar` (264px wide, scroll, collapse animation) |
| **Navbar** | `.navbar` (66px height, sticky) |
| **Cards & Panels** | `.card`, `.panel`, `.kpi-card` styling |
| **Badges** | `.badge-status` (active, inactive, maintenance, error, etc.) |
| **Tables** | `.table` styling, `.table-avatar` |
| **Forms** | Input, select, checkbox, radio styling |
| **Responsive** | Media queries (mobile, tablet, desktop) |
| **Utilities** | Spacing, text, visibility classes |

### Responsive Breakpoints

```css
/* Mobile first */
@media (max-width: 768px) { /* Tablet */ }
@media (max-width: 576px) { /* Mobile */ }
@media (min-width: 1200px) { /* Desktop */ }
```

### Status Badge Colors

```css
.badge-active       { background: #10b981; } /* Green */
.badge-maintenance  { background: #f59e0b; } /* Yellow */
.badge-error        { background: #ef4444; } /* Red */
.badge-pending      { background: #3b82f6; } /* Blue */
.badge-inactive     { background: #6b7280; } /* Gray */
/* ... etc */
```

### KPI Card Styling

```css
.kpi-card {
    --bs-card-bg: #ffffff;
    border: 1px solid var(--sw-border);
}
.kpi-value { font-size: 1.75rem; font-weight: 700; }
.kpi-label { font-size: 0.85rem; color: var(--sw-muted); }
.kpi-trend { font-weight: 600; }
.kpi-trend.up { color: #10b981; }    /* Green */
.kpi-trend.down { color: #ef4444; }  /* Red */
```

### Sidebar Styling

```css
.sidebar {
    width: var(--sw-sidebar-w);      /* 264px expanded */
    background: linear-gradient(...); /* Blue gradient */
    position: fixed; left: 0;
    transition: all 0.3s ease;
}
.sidebar.collapsed {
    width: var(--sw-sidebar-w-collapsed); /* 78px collapsed */
}
```

---

## 🎨 Cách sử dụng

### Áp dụng CSS classes

```html
<!-- Button variants -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-soft-primary">Soft</button>
<button class="btn btn-white">White</button>

<!-- Status badge -->
<span class="badge badge-status badge-active">Hoạt động</span>
<span class="badge badge-status badge-maintenance">Bảo trì</span>

<!-- KPI Card -->
<div class="card kpi-card">
    <div class="kpi-value">245</div>
    <div class="kpi-label">Khách hàng</div>
</div>

<!-- Table styling -->
<table class="table align-middle">
    <td><img src="..." class="table-avatar" /></td>
</table>
```

### Override theme colors

Thêm custom CSS hoặc chỉnh CSS variables:

```css
:root {
    --sw-primary: #new-color;  /* Đổi primary color */
}
```

---

## 📝 Chỉnh sửa theme

1. **Đổi màu chính:** Sửa `--sw-primary` ở line 9
2. **Đổi sidebar width:** Sửa `--sw-sidebar-w` ở line 22
3. **Đổi font:** Sửa `--bs-body-font-family` ở line 36
4. **Thêm status color:** Thêm `.badge-mystatus { background: #color; }`

⚠️ **Backup trước khi sửa** — changes ảnh hưởng toàn app

---

## 🔗 Reference

- Bootstrap 5 — loaded via CDN (overridden bởi variables ở đây)
- Bootstrap Icons — Bootstrap Icons CSS (dùng `<i class="bi bi-name"></i>`)
- Custom properties — [MDN CSS Variables](https://developer.mozilla.org/en-US/docs/Web/CSS/--*)
