# resources/views/inventory/ — Inventory management

Quản lý kho thiết bị.

## 📌 index.blade.php

**Route:** `/inventory`
**Controller:** InventoryController@index

### DataTable

- Columns: Product, Quantity, Reserved, Available, Last Updated
- Shows aggregated stock levels by product
- Search enabled
- Status indicator (ok, low, out)

### Data Structure

**CURRENT (aggregate by product):**
```
Product         | Qty | Reserved | Available
AquaPure RO 50  | 12  | 3        | 9
AquaPure RO 75  | 8   | 2        | 6
...
```

---

## ⚠️ Known Issue

**Specification mismatch:** The spec requires per-device-unit detail view:

**EXPECTED (per-device detail):**
```
Device ID | Serial | Model | Batch | Date In | Status | Location
DEV-001   | SN001  | RO-50 | B001  | 2024-01 | active | A-01
DEV-002   | SN002  | RO-50 | B001  | 2024-01 | active | A-02
...
```

**TODO:** Refactor to show per-unit inventory rows instead of aggregate.

---

## 📋 Fields

| Field | Type | Mô tả |
|-------|------|-------|
| Product | String | Tên sản phẩm |
| Quantity | Number | Tổng số lượng |
| Reserved | Number | Đã đặt chỗ |
| Available | Number | Còn lại |
| Status | badge | ok, low, out |
| Last Updated | Date | Cập nhật lần cuối |

---

## 🔗 Related

- Products (product detail)
- Devices (device inventory location)
- Batches (batch received)
