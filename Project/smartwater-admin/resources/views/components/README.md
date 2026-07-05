# resources/views/components/ — Blade Components (Reusable)

Chứa các Blade components — các "khối" UI reusable có thể dùng lại ở nhiều pages.

## 📌 panel.blade.php

**Công dụng:** Card wrapper component — thẻ chứa nội dung với header (title, icon, subtitle, actions).

### Props

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `title` | string | null | Tiêu đề card |
| `subtitle` | string | null | Mô tả nhỏ dưới tiêu đề |
| `icon` | string | null | Bootstrap icon class (e.g., `bi-gear`) |
| `flush` | boolean | false | Nếu true, loại bỏ padding card-body |

### Slots

| Slot | Mô tả |
|------|-------|
| `{{ $slot }}` | Nội dung chính |
| `{{ $actions }}` | Hành động (phải dùng `@slot:actions`) |

### Ví dụ

```blade
<!-- Basic panel -->
<x-panel title="Danh sách sản phẩm" icon="bi-box" subtitle="Quản lý tất cả sản phẩm">
    <table>...</table>
</x-panel>

<!-- Panel with actions -->
<x-panel title="Hóa đơn" icon="bi-receipt">
    <x-slot:actions>
        <button class="btn btn-sm btn-primary">Xuất PDF</button>
    </x-slot:actions>
    <div>Nội dung hóa đơn</div>
</x-panel>

<!-- Flush (no padding) -->
<x-panel flush>
    <table class="table">...</table>
</x-panel>
```

### Styling

- Bootstrap `.card` class
- Header có border-bottom
- Hỗ trợ custom CSS class via `{{ $attributes }}`

---

## 📌 kpi-card.blade.php

**Công dụng:** KPI (Key Performance Indicator) card — hiển thị chỉ số chính với icon, giá trị, xu hướng.

### Props

| Prop | Type | Default | Mô tả |
|------|------|---------|-------|
| `label` | string | '' | Tên chỉ số |
| `value` | string/number | '' | Giá trị hiển thị |
| `icon` | string | `bi-graph-up` | Bootstrap icon |
| `color` | string | `primary` | Màu theme (primary, success, warning, danger) |
| `trend` | string | null | % thay đổi (e.g., `+12.5%`) |
| `up` | boolean | true | Xu hướng tăng (true) / giảm (false) |

### Ví dụ

```blade
<!-- Basic KPI -->
<x-kpi-card label="Tổng khách hàng" value="245" icon="bi-people" />

<!-- With trend -->
<x-kpi-card 
    label="Doanh số tháng này" 
    value="$125,430" 
    icon="bi-currency-dollar"
    color="success"
    trend="+8.5%"
    :up="true"
/>

<!-- Down trend -->
<x-kpi-card 
    label="Tỉ lệ lỗi" 
    value="2.3%" 
    icon="bi-exclamation-triangle"
    color="danger"
    trend="-0.5%"
    :up="false"
/>
```

### Styling

- Card với layout flexbox
- Icon tròn có background color
- Trend arrow (↗ up, ↘ down)
- Responsive, height 100%

---

## 📌 status-badge.blade.php

**Công dụng:** Badge component — hiển thị status/trạng thái với màu sắc phù hợp.

### Props

| Prop | Type | Dùng ở |
|------|------|--------|
| `status` | string | Bắt buộc — status key |

### Status Map

| Status | Hiển thị | Màu sắc |
|--------|---------|---------|
| `active` | Hoạt động | Xanh lá |
| `inactive` | Ngưng hoạt động | Xám |
| `maintenance` | Bảo trì | Vàng |
| `error` | Lỗi | Đỏ |
| `pending` | Chờ lắp đặt | Xanh nhạt |
| `expired` | Hết hạn | Đỏ |
| `cancelled` | Đã hủy | Xám |
| `completed` | Hoàn thành | Xanh lá |
| `low` | Sắp hết hàng | Vàng |
| `out` | Hết hàng | Đỏ |
| `ok` | Còn hàng | Xanh lá |

### Ví dụ

```blade
<!-- Simple status -->
<x-status-badge status="active" />          <!-- Hoạt động -->
<x-status-badge status="maintenance" />     <!-- Bảo trì -->

<!-- In table -->
<td>
    <x-status-badge :status="$product['status']" />
</td>

<!-- With custom status not in map -->
<x-status-badge status="unknown" />         <!-- Hiển thị "Unknown" -->
```

### Styling

- Bootstrap `.badge` class
- Custom `.badge-status` + `.badge-{{ status }}` classes
- Styled trong `public/css/app.css`

---

## 🎯 Cách sử dụng Component

Tất cả components được access qua `<x-component-name>` prefix:

```blade
<!-- Invoke component -->
<x-panel title="My Panel">Content here</x-panel>
<x-kpi-card label="Sales" value="$100K" />
<x-status-badge status="active" />

<!-- With slots -->
<x-panel title="Title">
    <x-slot:actions>
        <button>Action</button>
    </x-slot:actions>
    Panel content
</x-panel>
```

---

## 📝 Thêm component mới

1. Tạo file `resources/views/components/my-component.blade.php`
2. Định nghĩa props với `@props(['prop1', 'prop2'])`
3. Write HTML template
4. Dùng `<x-my-component>` ở bất kỳ view nào

---

## ✅ Best practices

✅ Components reusable — không hard-code dữ liệu
✅ Props mang default values — dễ sử dụng
✅ Styling centralized — CSS ở `public/css/app.css`
✅ Naming — kebab-case (my-component) tương ứng file name
❌ KHÔNG lưu state — components chỉ là dữ liệu truyền vào

---

## 🔗 Reference

- [Laravel Blade Components](https://laravel.com/docs/blade#components)
- Styling: `public/css/app.css` (status colors, card styles)
