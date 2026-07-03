# public/js/ — Custom JavaScript files

Chứa JavaScript helpers, initialization, và UI interactions.

## 📌 app.js (~180 dòng)

**Công dụng:** Core UI script — sidebar toggle, DataTables initialization, ApexCharts helpers.

Load **tự động** trong `layouts/app.blade.php` (sau jQuery, DataTables, ApexCharts CDN).

### 3 Chức năng chính

---

## 1️⃣ Sidebar Toggle (Lines 8-31)

**Công dụng:** Xử lý collapse/expand sidebar trên desktop & mobile.

### Logic

**Desktop (>= 992px):**
- Click sidebar toggle → toggle class `sidebar-collapsed`
- Lưu trạng thái vào `localStorage` (key: `sw-sidebar`)
- Reload trang → restore trạng thái từ localStorage

**Mobile (< 992px):**
- Click sidebar toggle → toggle class `sidebar-open` (overlay mode)
- Click backdrop (nền mờ) → đóng sidebar

### Event Listeners

```javascript
[data-toggle-sidebar]     // Button to toggle sidebar
.sidebar-backdrop         // Click to close sidebar (mobile)
```

### Data Persisted

- `localStorage.sw-sidebar` = "1" (collapsed) hoặc "0" (expanded)

---

## 2️⃣ DataTables Initialization (Lines 33-69)

**Công dụng:** Auto-init DataTables trên tất cả bảng có `data-datatable`.

### Vietnamese Language

Định nghĩa `viLang` object với các labels tiếng Việt:
- "Tìm kiếm..."
- "Hiển thị _MENU_"
- "Không có dữ liệu"

### Auto-Initialization

```blade
<!-- Table sẽ auto-init DataTables -->
<table id="tblProducts" data-datatable>
    <thead>...</thead>
    <tbody>...</tbody>
</table>
```

### Data Attributes

| Attribute | Ví dụ | Tác dụng |
|-----------|-------|---------|
| `data-datatable` | - | Enable DataTables |
| `data-page-length` | "25" | Rows per page (default 10) |
| `data-dom` | Custom DOM | Customize table layout |
| `data-no-sort` | "0,1" | Disable sort on columns 0, 1 |

### Custom Search & Filter

**External search input:**
```html
<input data-dt-search="#tblProducts" />
```

**Column filter dropdown:**
```html
<select data-dt-filter="#tblProducts" data-dt-column="4">
    <option value="">All</option>
    <option value="active">Active</option>
</select>
```

Tìm kiếm & filter sẽ tự động apply vào table.

---

## 3️⃣ ApexCharts Helpers (Lines 71-?)

**Công dụng:** Helper functions để tạo chart dễ hơn.

### Cấu hình chung

```javascript
window.SW = {}  // Global namespace
BLUE = '#1668e3'  // Truliva blue
CYAN = '#17b6d6'  // Cyan accent
```

### Helper Functions

| Function | Tác dụng |
|----------|---------|
| `SW.areaChart()` | Tạo Area chart (đường mịn + fill) |
| `SW.lineChart()` | Tạo Line chart (đường không fill) |
| `SW.barChart()` | Tạo Bar chart (cột) |
| `SW.donutChart()` | Tạo Donut/Pie chart |

### Ví dụ sử dụng

```javascript
// Area chart
const el = document.getElementById('chartSales');
SW.areaChart(el, 'Sales', ['Jan', 'Feb', 'Mar'], [1000, 1500, 2000], '#1668e3');

// Bar chart
const el2 = document.getElementById('chartMaintenance');
SW.barChart(el2, 'Tasks', ['Preventive', 'Repair', 'Upgrade'], [5, 3, 2]);

// Donut chart
const el3 = document.getElementById('chartStatus');
SW.donutChart(el3, ['Active', 'Inactive'], [80, 20]);
```

### Chart Options

- **Font:** Inherit từ app (Inter, Segoe UI)
- **Toolbar:** Hidden (show: false)
- **Grid:** Light gray, dashed border
- **Legend:** Bottom position
- **Tooltip:** Light theme
- **Animation:** Smooth curves

---

## 🎯 Cách sử dụng trong Blade

### 1. Sidebar Toggle Button

```blade
<button data-toggle-sidebar>
    <i class="bi bi-list"></i>
</button>
```

### 2. DataTable

```blade
<table id="myTable" data-datatable data-page-length="15">
    <thead><tr><th>Name</th><th>Status</th></tr></thead>
    <tbody>
        @foreach ($items as $item)
            <tr><td>{{ $item['name'] }}</td><td>{{ $item['status'] }}</td></tr>
        @endforeach
    </tbody>
</table>

<!-- Custom search (outside table) -->
<input type="search" data-dt-search="#myTable" placeholder="Tìm kiếm..." />

<!-- Filter by column -->
<select data-dt-filter="#myTable" data-dt-column="1">
    <option value="">Tất cả</option>
    <option value="active">Hoạt động</option>
    <option value="inactive">Ngưng</option>
</select>
```

### 3. Charts

```blade
<div id="chartArea"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        SW.areaChart(
            document.getElementById('chartArea'),
            'Monthly Revenue',
            ['Jan', 'Feb', 'Mar', 'Apr'],
            [1000, 2000, 1500, 3000],
            '#1668e3'
        );
    });
</script>
```

---

## ⚙️ Configuration

### Sidebar

```javascript
desktopQuery = window.matchMedia('(min-width: 992px)')  // Desktop breakpoint
localStorage key: 'sw-sidebar'
collapsed value: '1'
expanded value: '0'
```

### DataTables

```javascript
pageLength: 10 (default, override via data-page-length)
lengthChange: false (không hiển thị "Entries per page")
orderingDisabled: via data-no-sort attribute
```

### ApexCharts

```javascript
Colors: BLUE (#1668e3), CYAN (#17b6d6)
Height: 300px (default, override via data-height)
Toolbar: hidden
Zoom: disabled
```

---

## 📝 Thêm helper mới

```javascript
// app.js
SW.myChart = function (el, name, labels, series) {
    if (!el) return;
    new ApexCharts(el, {
        ...baseOptions,
        chart: { type: 'scatter', height: 300 },
        series: [{ name, data: series }],
        // ...
    }).render();
};
```

Rồi sử dụng:
```javascript
SW.myChart(el, 'My Data', ['A', 'B'], [10, 20]);
```

---

## 🔗 Dependencies

- **jQuery** — CDN (DataTables needs)
- **DataTables** — CDN (table management)
- **ApexCharts** — CDN (charting)
- **Bootstrap Icons** — CDN (sidebar icons)

Tất cả load trước app.js trong `layouts/app.blade.php`.

---

## ⚠️ Common Issues

❌ **Chart không hiển thị?**
- Check nếu ApexCharts CDN loaded (`window.ApexCharts` exists)
- Verify element ID matches
- Check console for JS errors

❌ **DataTable filter không work?**
- Verify `data-dt-filter` selector matches table id
- Check `data-dt-column` index (0-based)
- Ensure jQuery loaded before app.js

❌ **Sidebar toggle không lưu?**
- Check nếu `localStorage` available
- Inspect `localStorage.sw-sidebar` value
- Check responsive breakpoint (992px)
