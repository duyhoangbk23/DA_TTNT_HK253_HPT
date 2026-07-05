# app/Http/ — HTTP Request/Response handlers

Xử lý tất cả HTTP requests từ browser, bao gồm Controllers, Middleware, Requests, Resources.

## 📁 Cấu trúc

| Folder/File | Tác dụng |
|-------------|---------|
| **Controllers/** | Xử lý logic từng trang (fetch dữ liệu từ MockData, truyền vào View) |
| **Middleware/** | Xử lý trước/sau request (nếu có) |

## 📌 Controllers (12 file)

| Controller | Tác dụng | Route |
|------------|---------|-------|
| `AuthController` | Xử lý đăng nhập (demo, không validate DB) | `/login` |
| `DashboardController` | Fetch KPI, chart data cho dashboard | `/dashboard`, `/` |
| `ProductController` | Danh sách sản phẩm | `/products` |
| `InventoryController` | Kho thiết bị (stock) | `/inventory` |
| `BatchController` | Danh sách & chi tiết lô hàng | `/batches`, `/batches/{id}` |
| `CustomerController` | Danh sách & chi tiết khách hàng | `/customers`, `/customers/{id}` |
| `ContractController` | Quản lý hợp đồng | `/contracts` |
| `DeviceController` | Danh sách & chi tiết thiết bị + chart sensor | `/devices`, `/devices/{id}` |
| `EmployeeController` | Danh sách nhân viên | `/employees` |
| `ActivityController` | Lịch sử hoạt động | `/activities` |
| `ProfileController` | Hồ sơ cá nhân người dùng | `/profile` |

## 🎯 Pattern chung

Tất cả Controllers **chỉ làm 3 việc**:
1. Gọi `MockData::{method}()` để lấy dữ liệu giả lập
2. Xử lý filter/search nếu cần
3. Truyền dữ liệu vào View via `return view(...)`

**Không có**: Database queries, API calls, Business Logic, Authentication thực
