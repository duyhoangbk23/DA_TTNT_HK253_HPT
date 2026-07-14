# resources/views/activities/ — Activity log

Lịch sử hoạt động trong hệ thống.

## 📌 index.blade.php

**Route:** `/activities`
**Controller:** ActivityController@index

### DataTable

- Columns: Time, User, Action, Description
- Shows system activity log
- Search enabled
- Timeline-like view with avatars
- 20+ activities in mock data

### Data

```php
$activities = MockData::activities();
```

---

## 📋 Activity Fields

| Field | Type | Ví dụ |
|-------|------|-------|
| Time | DateTime | 2024-07-03 14:30 |
| User | String | Nguyễn Văn A |
| Avatar | Image | User profile image |
| Action | String | Tạo hợp đồng, Bảo trì thiết bị |
| Icon | Icon | bi-wrench (action-specific) |
| Description | String | Chi tiết thao tác |

---

## 📌 Activity Types (Icons)

| Action | Icon | Mô tả |
|--------|------|-------|
| Create | bi-plus-circle | Tạo mới |
| Update | bi-pencil | Cập nhật |
| Delete | bi-trash | Xóa |
| Maintain | bi-wrench | Bảo trì |
| Install | bi-box-seam | Lắp đặt |
| Logout | bi-box-arrow-right | Đăng xuất |

---

## 🎯 Features

✅ Danh sách hoạt động theo thời gian
✅ User avatar + name
✅ Action icon (action-specific)
✅ Full description của hoạt động
✅ Search enabled
❌ Filter by date range (chưa có)
❌ Filter by user (chưa có)
❌ Filter by action type (chưa có)

---

## 🔗 Related

- Users/Employees (who performed the action)
- Records (audit trail for compliance)
- Profile (user's own activities)
