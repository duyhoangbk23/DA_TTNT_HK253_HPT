# resources/views/batch/ — Batch management

Quản lý lô hàng (nhập kho).

## 📌 index.blade.php

**Route:** `/batches`
**Controller:** BatchController@index

Danh sách tất cả lô hàng với bảng DataTable.

### Columns

- Batch Code
- Product
- Supplier
- Quantity
- Received Date
- Status

---

## 📌 show.blade.php

**Route:** `/batches/{id}`
**Controller:** BatchController@show

Chi tiết một lô hàng.

### Bao gồm

1. **Batch Information Panel:**
   - Batch Code
   - Product
   - Supplier
   - Quantity
   - Received Date
   - Batch Notes

2. **Inventory Distribution Table:**
   - Device units từ batch này
   - Columns: Serial, Product, Status, Location, Installed Date

3. **Quality Check Panel:**
   - Status badge (passed, failed, pending)
   - Inspector name
   - Check date

### Dữ liệu

```php
$batch = MockData::batches()->find($id);
$inventory = MockData::inventories()
    ->where('batch_id', $id);
```

---

## 🔗 Navigation

- `/batches` → click một row → `/batches/{id}`
- Breadcrumb: Danh sách lô hàng > Chi tiết lô {code}

---

## 📋 Fields in Detail Page

| Field | Mô tả |
|-------|-------|
| Batch Code | Mã lô hàng |
| Product | Sản phẩm |
| Supplier | Nhà cung cấp |
| Quantity | Số lượng |
| Received Date | Ngày nhập |
| Notes | Ghi chú |
| Quality Status | Đã kiểm tra? |

