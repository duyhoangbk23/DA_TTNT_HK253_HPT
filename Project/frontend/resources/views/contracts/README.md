# resources/views/contracts/ — Contract management

Quản lý hợp đồng bảo trì.

## 📌 index.blade.php

**Route:** `/contracts`
**Controller:** ContractController@index

### DataTable

- Columns: Contract ID, Customer, Product, Start Date, End Date, Type, Status
- Search enabled
- Filter by type (Monthly, Annual, Quarterly)
- Filter by status (active, expired, pending, cancelled)

### Data

8 hợp đồng từ MockData bao gồm:
- Maintenance contracts
- Service contracts
- Warranty contracts

---

## 📋 Contract Fields

| Field | Type | Ví dụ |
|-------|------|-------|
| Contract ID | String | CTR-001 |
| Customer | String | Công ty ABC |
| Product | String | AquaPure RO 50 |
| Start Date | Date | 2024-01-15 |
| End Date | Date | 2024-12-31 |
| Type | badge | monthly, annual, quarterly |
| Status | badge | active, expired, pending, cancelled |

---

## 🔗 Related Data

- **Customers:** Liên kết tới customer detail page
- **Devices:** Thiết bị liên quan tới hợp đồng
- **Maintenance:** Bảo trì liên quan

---

## ⚠️ Features

✅ Danh sách hợp đồng với search/filter
❌ Chi tiết hợp đồng detail page (chưa có)
❌ Tạo/sửa/xóa hợp đồng (buttons chưa functional)
