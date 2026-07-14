# resources/views/products/ — Products management

Trang quản lý sản phẩm.

## 📌 index.blade.php

**Route:** `/products`
**Controller:** ProductController@index

### Bao gồm

1. **Page Header:**
   - Title: "Quản lý sản phẩm"
   - Subtitle: "Danh sách tất cả sản phẩm"
   - "Thêm sản phẩm" button (demo, không functional)

2. **DataTable:**
   - Columns: SKU, Name, Category, Model, Capacity, Status
   - Search: Custom search input
   - Filter: Status filter dropdown
   - Pagination: 10 rows per page
   - Responsive: Horizontal scroll on mobile

3. **Data:**
   - 10 sản phẩm từ MockData
   - Status badges (active, maintenance, inactive)
   - Category names

### Cấu trúc Blade

```blade
@extends('layouts.app')

<x-panel title="Danh sách sản phẩm" icon="bi-box">
    <x-slot:actions>
        <input type="search" data-dt-search="#tblProducts" placeholder="Tìm kiếm..." />
        <select data-dt-filter="#tblProducts" data-dt-column="5">
            <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="inactive">Ngưng hoạt động</option>
        </select>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm sản phẩm
        </button>
    </x-slot:actions>

    <table id="tblProducts" data-datatable>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Model</th>
                <th>Dung lượng</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product['sku'] }}</td>
                    <td>{{ $product['name'] }}</td>
                    <!-- ... -->
                </tr>
            @endforeach
        </tbody>
    </table>
</x-panel>
```

---

## 📊 Dữ liệu

| Field | Ví dụ |
|-------|-------|
| SKU | AQ-RO-50 |
| Name | AquaPure RO 50 |
| Category | Máy lọc nước RO |
| Model | APRO-50 |
| Capacity | 50 L/h |
| Status | active, maintenance, inactive |

---

## 🔗 Tương tác

- **Search:** Gõ vào search box → filter bảng
- **Filter:** Chọn status → filter cột Status
- **"Thêm sản phẩm" button:** Dead link (không có modal/form)

---

## ⚠️ Ghi chú

- Không có detail page cho từng sản phẩm
- CRUD buttons (edit, delete) không functional
- "Thêm sản phẩm" button cần kết nối tới modal/form (TODO)
