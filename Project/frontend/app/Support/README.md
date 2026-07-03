# app/Support/ — Helper classes & Utilities

Chứa support classes để hỗ trợ ứng dụng.

## 📌 MockData.php (600+ dòng)

**Công dụng:** Nguồn dữ liệu giả lập cho toàn bộ demo frontend.

Thay vì kết nối Database, tất cả dữ liệu được tạo **statically** từ file này.

### Cấu trúc dữ liệu

Bao gồm tất cả entities trong ERD hệ thống:

| Method | Trả về | Dùng ở đâu |
|--------|--------|-----------|
| `roles()` | Danh sách roles (Admin, Manager, Staff) | - |
| `employees()` | 5 nhân viên + avatar | `/employees`, Profile |
| `currentUser()` | User đang đăng nhập | Navbar, Profile |
| `categories()` | 4 danh mục sản phẩm | - |
| `products()` | 10 sản phẩm | `/products`, Inventory |
| `suppliers()` | 4 nhà cung cấp | - |
| `batches()` | 8 lô hàng | `/batches`, `/batches/{id}` |
| `inventories()` | Stock thiết bị | `/inventory` |
| `customers()` | 5 khách hàng | `/customers`, `/customers/{id}` |
| `contracts()` | 8 hợp đồng | `/contracts`, Customer detail |
| `devices()` | 6 thiết bị đã bán | `/devices`, `/devices/{id}` |
| `deviceDashboardData()` | Biểu đồ thiết bị (donut) | Dashboard |
| `maintenanceData()` | Biểu đồ bảo trì (bar) | Dashboard |
| `customers()` | Biểu đồ khách hàng (bar) | Dashboard |
| `deviceSensorData()` | Dữ liệu cảm biến thiết bị (TDS, temp, flow, pH) | Device detail chart |
| `activities()` | Lịch sử hoạt động | `/activities`, Profile timeline |
| `navNotifications()` | Thông báo navbar | Navbar dropdown |

### Cách sử dụng

```php
// Trong Controller
use App\Support\MockData;

$products = MockData::products();           // Collection
$customers = MockData::customers()->take(5); // Get 5 customers
$device = MockData::devices()->find(1);     // Get device by ID
```

### Đặc điểm

✅ **Trả về Collection** — dễ filter/sort/paginate
✅ **Không có DB dependency** — chạy nhanh, không cần migration
✅ **Dữ liệu cố định** — không đổi khi reload trang
❌ **Không lưu data** — nhập/sửa/xóa không có tác dụng

### Cấu trúc mỗi dữ liệu

- **Products:** id, sku, name, category_id, model, capacity, status, image
- **Customers:** id, name, phone, email, address, status, created_at
- **Devices:** id, serial, product_id, customer_id, status, created_at, last_maintenance
- **Contracts:** id, customer_id, start_date, end_date, type, status
- **Batches:** id, product_id, batch_code, quantity, supplier_id, received_date, status
- Etc.

## 📝 Thêm dữ liệu mới

1. Thêm method public static trong `MockData`
2. Return Collection
3. Gọi từ Controller: `MockData::myNewData()`
4. Truyền vào View: `return view('page', ['data' => MockData::myNewData()])`

## ⚠️ Lưu ý quan trọng

- **KHÔNG chỉnh sửa sau khi deploy** — các test/demo sẽ phụ thuộc vào data này
- Khi scale sang thực tế, thay thế các `MockData::*()` calls bằng eloquent queries
- Giữ structure của Collection như hiện tại để có thể dễ swap với DB queries sau
