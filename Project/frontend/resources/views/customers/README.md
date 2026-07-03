# resources/views/customers/ — Customer management

Quản lý khách hàng.

## 📌 index.blade.php

**Route:** `/customers`
**Controller:** CustomerController@index

### DataTable

- Columns: Name, Phone, Email, Address, Status, Created Date
- Search & filter by status
- 5 customers in mock data

---

## 📌 show.blade.php

**Route:** `/customers/{id}`
**Controller:** CustomerController@show

Chi tiết khách hàng.

### Bao gồm

1. **Customer Card:**
   - Avatar
   - Name
   - Phone, Email
   - Address
   - Status badge
   - Member since date

2. **Contact Information:**
   - Email
   - Phone
   - Address
   - City
   - Contact person

3. **Active Contracts Table:**
   - Contract ID
   - Product
   - Start Date
   - End Date
   - Type (monthly, annual)
   - Status

4. **Installed Devices Table:**
   - Device serial
   - Product name
   - Installation date
   - Status

### Dữ liệu

```php
$customer = MockData::customers()->find($id);
$contracts = MockData::contracts()
    ->where('customer_id', $id);
$devices = MockData::devices()
    ->where('customer_id', $id);
```

---

## 📋 Customer Fields

| Field | Type | Ví dụ |
|-------|------|-------|
| Name | String | Công ty ABC |
| Phone | String | 0902-123-456 |
| Email | String | info@abc.com |
| Address | String | 123 Nguyễn Huệ, HCM |
| Status | badge | active, inactive, pending |

