# resources/views/employees/ — Employee management

Quản lý nhân viên.

## 📌 index.blade.php

**Route:** `/employees`
**Controller:** EmployeeController@index

### DataTable

- Columns: Name, Email, Phone, Position, Department, Status, Hire Date
- Search & filter enabled
- 5 employees in mock data
- Responsive layout

### Data

```php
$employees = MockData::employees();
```

---

## 📋 Employee Fields

| Field | Type | Ví dụ |
|-------|------|-------|
| Name | String | Nguyễn Văn A |
| Email | String | a.nguyen@smartwater.vn |
| Phone | String | 0912-345-678 |
| Position | String | Technician, Manager, Admin |
| Department | String | Engineering, Sales, Support |
| Status | badge | active, on-leave, inactive |
| Hire Date | Date | 2023-01-15 |

---

## 📌 Features

✅ Danh sách nhân viên với search/filter
✅ Avatar image
✅ Status badge
❌ Chi tiết nhân viên page (chưa có)
❌ Tạo/sửa/xóa nhân viên (buttons chưa functional)

---

## 🔗 Related

- Technicians (từ employees, assigned to maintenance tasks)
- Activities (theo dõi hoạt động nhân viên)
