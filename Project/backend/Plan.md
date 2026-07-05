# Backend Development Plan

## 0. Phạm vi và nguồn phân tích

Tài liệu này lập kế hoạch phát triển Backend cho hệ thống SmartWater Admin. Giai đoạn này không hiện thực Backend, không tạo Controller/Model/Migration/API.

Nguồn đã đối chiếu theo thứ tự ưu tiên:

1. Frontend Laravel: `Project/frontend/routes/web.php`, `resources/views`, `app/Http/Controllers`, `app/Support/MockData.php`.
2. SRS: `QLDA/Form_report/docs/Web_UI_UX/SRS/chap4_Func_req.tex`, `chap6_UC.tex`, `chap7_DataReq.tex`.
3. ERD: `Project/docs/ERD.png`.
4. README: `Project/frontend/README.md`.

Nguyên tắc xử lý khác biệt:

- Frontend là giao diện chính thức: Backend phải trả đúng dữ liệu đang hiển thị.
- SRS/Use Case dùng để xác định nghiệp vụ và quyền thao tác.
- ERD dùng làm nền schema, nhưng cần bổ sung một số cột để khớp Frontend.
- Không thay đổi Frontend để ép theo Backend.

## 1. Tổng quan hiện trạng Frontend

Frontend là Laravel 12, Blade UI-only, chưa có DB/API/Auth thật. Tất cả dữ liệu lấy từ `App\Support\MockData`. Các route hiện là `GET` trả view:

| Route | Màn hình | Controller hiện tại | MockData |
|---|---|---|---|
| `/login` | Đăng nhập | `AuthController@login` | không xác thực thật |
| `/`, `/dashboard` | Dashboard | `DashboardController@index` | `dashboardKpis`, chart data, activities, maintenance, contracts |
| `/products` | Sản phẩm | `ProductController@index` | `products`, `categories` |
| `/inventory` | Kho thiết bị | `InventoryController@index` | `inventories` |
| `/batches` | Lô hàng | `BatchController@index` | `batches` |
| `/batches/{id}` | Chi tiết lô hàng | `BatchController@show` | `batches`, `batchDetails` |
| `/customers` | Khách hàng | `CustomerController@index` | `customers` |
| `/customers/{id}` | Chi tiết khách hàng | `CustomerController@show` | `customer`, devices, contracts, maintenance |
| `/contracts` | Hợp đồng | `ContractController@index` | `contracts` |
| `/devices` | Thiết bị | `DeviceController@index` | `devices`, counts |
| `/devices/{id}` | Chi tiết thiết bị | `DeviceController@show` | `device`, `telemetry`, maintenance, activities |
| `/employees` | Nhân viên | `EmployeeController@index` | `employees`, `roles` |
| `/activities` | Activity Log | `ActivityController@index` | `activities` |
| `/profile` | Hồ sơ cá nhân | `ProfileController@index` | `currentUser`, activities |

Component dùng chung:

- `layouts.app`: layout chính, CDN Bootstrap, Bootstrap Icons, ApexCharts, DataTables.
- `partials.sidebar`: menu module.
- `components.panel`: khung nội dung.
- `components.kpi-card`: KPI.
- `components.status-badge`: mapping status hiển thị.

## 2. Phân tích module Frontend và dữ liệu

### 2.1 Authentication

Màn hình:

- `/login`: form email, password, remember, link quên mật khẩu.

Thao tác:

- Frontend hiện submit `GET` về dashboard, không có xác thực.
- Backend cần thay bằng `POST /login`, `POST /logout`, session Laravel.

Dữ liệu DB:

- `users`: username/email/password/status/last_login/role_id/employee_id.
- `roles`.

Validation:

- email required, email format.
- password required.
- remember boolean.

### 2.2 Dashboard

Màn hình:

- `/dashboard`: KPI, chart thiết bị theo trạng thái, khách hàng mới theo tháng, lượt bảo trì theo tháng, hợp đồng sắp hết hạn, hoạt động gần đây, bảo trì gần đây.

Dữ liệu hiển thị:

- KPI: tổng khách hàng, tổng sản phẩm, tổng thiết bị, thiết bị hoạt động, thiết bị bảo trì, hợp đồng còn hiệu lực.
- Chart: status breakdown thiết bị, khách hàng mới theo tháng, bảo trì theo tháng.
- List: hợp đồng sắp hết hạn, activity gần đây, maintenance gần đây.

Dữ liệu tính toán:

- Count theo bảng `customers`, `products`, `devices`, `contracts`.
- Count/filter theo `devices.status`, `contracts.status`.
- Hợp đồng sắp hết hạn: `end_date` trong ngưỡng cấu hình, ví dụ 30 ngày.
- Chart tháng: group by tháng trên `customers.created_at`, `maintenance_records.maintenance_date`.

API cần có:

- `GET /api/dashboard/summary`
- `GET /api/dashboard/device-status`
- `GET /api/dashboard/customer-growth`
- `GET /api/dashboard/maintenance-stats`
- `GET /api/dashboard/recent-activities`
- `GET /api/dashboard/recent-maintenance`
- `GET /api/dashboard/expiring-contracts`

### 2.3 Product Management

Màn hình:

- `/products`: danh sách sản phẩm.

Trường hiển thị:

- ảnh/icon, tên sản phẩm, mã sản phẩm, danh mục, model, công suất, trạng thái.

Thao tác Frontend:

- Tìm kiếm DataTables.
- Lọc theo danh mục, trạng thái.
- Nút "Thêm sản phẩm" có trên UI nhưng chưa có form/modal.

MockData:

- `products`: `id`, `code`, `name`, `category_id`, `category`, `model`, `capacity`, `unit`, `price`, `status`, `image`.
- `categories`: `id`, `name`, `description`, `status`.

Dữ liệu DB:

- `products`, `categories`.
- Cần lưu `capacity`, `price`, `image` vì Frontend/mock đang có nhưng ERD chưa thể hiện đầy đủ `capacity`, `price`, `image`.

CRUD:

- Theo SRS FR-02: add, edit, delete, detail, search.
- Frontend hiện chỉ danh sách; Backend vẫn cần API CRUD để bật UI sau.

### 2.4 Inventory Management

Màn hình:

- `/inventory`: KPI tồn kho và danh sách tồn kho.

Trường hiển thị:

- mã/tên sản phẩm, model, số lượng, đã giữ chỗ, có thể xuất, cập nhật, trạng thái.

Thao tác:

- Tìm kiếm, lọc trạng thái.
- Nút "Nhập kho" hướng nghiệp vụ nhập lô hàng/tồn kho.

MockData:

- `inventories`: `product_id`, `product`, `code`, `model`, `quantity`, `reserved`, `available`, `unit_cost`, `last_updated`, `stock_status`.

Dữ liệu DB:

- `inventories`: `product_id`, `quantity`, `reserved_quantity`, `unit_cost`, `last_updated`.
- `available` và `stock_status` là dữ liệu tính toán.

Business rules:

- `available = max(quantity - reserved_quantity, 0)`.
- `stock_status`: `out` nếu quantity = 0, `low` nếu quantity <= ngưỡng tồn thấp, `ok` nếu còn đủ.
- Không cho xuất nếu `available <= 0`.

### 2.5 Batch Management

Màn hình:

- `/batches`: danh sách lô hàng.
- `/batches/{id}`: chi tiết lô hàng và danh sách sản phẩm trong lô.

Trường hiển thị danh sách:

- mã lô, nhà cung cấp, ngày nhập, hạn sử dụng, số lượng, thao tác.

Trường chi tiết:

- nhà cung cấp, ngày nhập, hạn sử dụng, tổng số lượng, ghi chú, sản phẩm, mã, model, số lượng, đơn giá, thành tiền.

MockData:

- `batches`: `code`, `supplier_id`, `supplier`, `import_date`, `expiry_date`, `quantity`, `note`.
- `batchDetails`: `product`, `code`, `model`, `quantity`, `unit_cost`.

Dữ liệu DB:

- `batches`, `batch_details`, `suppliers`, `products`.
- Tổng số lượng và thành tiền là dữ liệu tính toán từ `batch_details`.

Business rules:

- Tạo batch phải có ít nhất một dòng `batch_details`.
- Tổng số lượng batch phải bằng tổng `batch_details.quantity` hoặc được tính động.
- Khi xác nhận nhập kho, cập nhật `inventories.quantity` và `unit_cost`.

### 2.6 Customer Management

Màn hình:

- `/customers`: danh sách khách hàng.
- `/customers/{id}`: thông tin khách hàng, thiết bị đang sử dụng, hợp đồng, lịch sử bảo trì.

Trường hiển thị:

- avatar, tên, email, mã khách hàng, điện thoại, địa chỉ, trạng thái, loại khách hàng, ngày tham gia.
- Thiết bị: mã, model, ngày lắp đặt, trạng thái.
- Hợp đồng: mã, loại, ngày ký, ngày hết hạn, trạng thái.
- Bảo trì: ngày, mã bảo trì, loại, thiết bị, mô tả, kỹ thuật viên.

MockData:

- `customers`: `code`, `name`, `avatar`, `phone`, `email`, `address`, `type`, `status`, `joined`.

Dữ liệu DB:

- `customers`.
- Cần bổ sung `avatar` và `joined_at` hoặc dùng `created_at` cho ngày tham gia.

Business rules:

- Không xóa cứng khách hàng nếu còn thiết bị/hợp đồng; dùng inactive hoặc soft delete.
- Email/phone nên unique nếu được dùng để nhận diện.

### 2.7 Contract Management

Màn hình:

- `/contracts`: danh sách hợp đồng.

Trường hiển thị:

- mã hợp đồng, khách hàng, thiết bị, ngày ký, ngày lắp đặt, chu kỳ bảo trì, trạng thái.

MockData:

- `contracts`: `code`, `customer_id`, `customer`, `device_code`, `type`, `type_label`, `sign_date`, `install_date`, `end_date`, `cycle`, `amount`, `status`, `expiring_soon`.
- `contractServices`: service name, interval, description.

Dữ liệu DB:

- `contracts`, `contract_services`, `customers`, `devices`.
- ERD có `start_date`, `end_date`, `amount`, `status`, `contract_type`; Frontend cần thêm `install_date`, `maintenance_cycle_months` hoặc lấy từ `contract_services.service_interval`.

Business rules:

- Không tạo hợp đồng nếu `customer_id` không tồn tại.
- Nếu hợp đồng gắn thiết bị, thiết bị phải thuộc khách hàng hoặc được tạo/assign trong quy trình hợp đồng.
- Trạng thái: active, expired, cancelled.
- Hợp đồng sắp hết hạn tính theo `end_date`.

### 2.8 Device Management và Device Dashboard

Màn hình:

- `/devices`: danh sách thiết bị, KPI theo trạng thái.
- `/devices/{id}`: thông tin thiết bị, dữ liệu cảm biến, activity, maintenance.

Trường hiển thị danh sách:

- device code, khách hàng, model, firmware, serial, trạng thái.

Trường chi tiết:

- trạng thái, model, serial, firmware, khách hàng, vị trí lắp đặt, ngày lắp đặt.
- Telemetry: TDS, nhiệt độ, lưu lượng nước, pH theo 24h/7d/30d.
- Maintenance: mã, ngày, loại, kỹ thuật viên, trạng thái.

MockData:

- `devices`: `code`, `serial`, `model`, `product`, `firmware`, `customer_id`, `customer`, `batch`, `import_date`, `install_date`, `location`, `status`.
- `telemetry`: `labels`, `tds`, `temperature`, `water_flow`, `ph`.

Dữ liệu DB:

- `devices`, `device_dashboard_data`, `maintenance_records`, `activity_log`.
- ERD có `devices.serial_number`, `install_date`, `location`, `status`, nhưng chưa có `firmware`; cần bổ sung `firmware_version`.
- ERD có `device_dashboard_data.water_usage`, nhưng Frontend gọi `water_flow`; cần thống nhất tên API trả `water_flow` và DB có thể lưu `water_flow` hoặc map từ `water_usage` nếu ý nghĩa đúng.

Business rules:

- Không tạo thiết bị nếu chưa có `product_id`.
- Thiết bị có thể chưa có khách hàng/hợp đồng khi ở trạng thái pending.
- Status Frontend cần: active, maintenance, error, pending. ERD hiện ghi Active/Inactive, cần mở rộng enum/status.
- Telemetry query theo range 24h/7d/30d.

### 2.9 Employee Management

Màn hình:

- `/employees`: danh sách nhân viên.

Trường hiển thị:

- avatar, họ tên, email, mã nhân viên, vai trò, trạng thái.

MockData:

- `employees`: `code`, `name`, `avatar`, `email`, `phone`, `position`, `role_id`, `role`, `hire_date`, `status`.
- `roles`: mock có 5 vai trò; yêu cầu chính thức chỉ gồm Administrator, Employee, Technician.

Dữ liệu DB:

- `employees`, `users`, `roles`.
- Cần bổ sung `avatar`.

Business rules:

- SRS yêu cầu role chính: Administrator, Employee, Technician. Các role mock "Quản lý", "Nhân viên kho", "Chăm sóc khách hàng" chưa thống nhất, nên xử lý bằng permission/sub-role nếu vẫn cần về sau.
- Employee có thể có hoặc không có user account theo ERD.

### 2.10 Activity Log

Màn hình:

- `/activities`: danh sách nhật ký hoạt động.

Trường hiển thị:

- thời gian, người thực hiện, avatar, hành động, mô tả.

MockData:

- `activities`: `time`, `user`, `avatar`, `action`, `module`, `icon`, `description`, `ip`.

Dữ liệu DB:

- `activity_log`: `user_id`, `action`, `module`, `record_id`, `description`, `ip_address`, `created_at`.
- `icon` nên là dữ liệu trình bày ở Backend Resource hoặc Frontend mapping, không bắt buộc lưu DB.

Business rules:

- Ghi log cho thao tác thêm/sửa/xóa dữ liệu quan trọng.
- Chỉ Administrator xem toàn bộ log theo Use Case UC-11.

### 2.11 Profile

Màn hình:

- `/profile`: ảnh đại diện, thông tin cá nhân, hoạt động gần đây, form đổi thông tin, form đổi mật khẩu.

Trường form:

- họ tên, chức vụ disabled, email, số điện thoại, địa chỉ.
- mật khẩu hiện tại, mật khẩu mới, xác nhận mật khẩu.
- đổi ảnh đại diện.

Dữ liệu DB:

- `users`, `employees`.

Business rules:

- User chỉ sửa hồ sơ của chính mình.
- Không cho sửa chức vụ/role từ màn hình profile.
- Đổi mật khẩu phải xác minh mật khẩu hiện tại.

## 3. Phân tích nghiệp vụ theo Use Case

| UC | Module | Actor | Luồng nghiệp vụ chính |
|---|---|---|---|
| UC-01 | Authentication | Administrator, Employee, Technician | nhập email/password, validate, tạo session, cập nhật `last_login`, redirect dashboard |
| UC-02 | Dashboard | tất cả role | tổng hợp dữ liệu từ customer/product/device/contract/maintenance/activity |
| UC-03 | Product | Administrator | quản lý danh mục sản phẩm và sản phẩm |
| UC-04 | Inventory | Administrator, Employee | xem tồn kho, nhập/xuất/giữ chỗ, cảnh báo tồn thấp |
| UC-05 | Batch | Administrator | tạo lô hàng, thêm chi tiết lô, cập nhật tồn kho |
| UC-06 | Customer | Administrator, Employee | tạo/cập nhật khách hàng, xem thiết bị/hợp đồng/bảo trì |
| UC-07 | Contract | Administrator, Employee | tạo/cập nhật hợp đồng, ghi ngày lắp đặt, chu kỳ bảo trì |
| UC-08 | Device | Administrator, Employee, Technician | đăng ký, assign khách hàng, cập nhật trạng thái/firmware/vị trí |
| UC-09 | Device Dashboard | tất cả role | xem telemetry theo thiết bị và khoảng thời gian |
| UC-10 | Employee | Administrator | quản lý nhân viên, gán role/account |
| UC-11 | Activity Log | Administrator | xem, lọc, tìm kiếm nhật ký |
| UC-12 | Profile | tất cả role | xem/sửa hồ sơ cá nhân, đổi mật khẩu/avatar |

## 4. Đánh giá Database

### 4.1 Bảng ERD hiện có

ERD có các bảng:

- `roles`
- `users`
- `employees`
- `customers`
- `categories`
- `products`
- `inventories`
- `suppliers`
- `batches`
- `batch_details`
- `contracts`
- `contract_services`
- `devices`
- `device_dashboard_data`
- `maintenance_records`
- `activity_log`
- `attachments`

### 4.2 Điểm phù hợp

- ERD bao phủ hầu hết module Frontend và SRS.
- Quan hệ chính đúng: role-user, employee-user, category-product, product-inventory, supplier-batch, batch-batch_detail, customer-contract/device, device-maintenance/telemetry.
- Có `attachments` hỗ trợ mở rộng file đính kèm cho customer/contract/device/maintenance.

### 4.3 Điểm thiếu hoặc chưa khớp Frontend

| Vấn đề | Nguồn phát hiện | Đề xuất |
|---|---|---|
| `products` thiếu `capacity`, `price`, `image` | MockData/products view | thêm cột `capacity`, `price`, `image_path` |
| `customers` thiếu `avatar`, `joined` | customers/profile UI | thêm `avatar_path`; dùng `created_at` hoặc thêm `joined_at` nếu cần nhập ngày riêng |
| `employees` thiếu `avatar` | employees/profile UI | thêm `avatar_path` |
| `devices` thiếu `firmware` | devices UI | thêm `firmware_version` |
| `devices.status` ERD chỉ Active/Inactive | Frontend có active/maintenance/error/pending | dùng enum/string status: active, maintenance, error, pending, inactive |
| `contracts` thiếu `install_date`, `cycle` | contracts UI | thêm `install_date`; thêm `maintenance_cycle_months` hoặc liên kết `contract_services` |
| `device_dashboard_data` có `water_usage`, Frontend cần `water_flow` | devices detail chart | chuẩn hóa: lưu `water_flow` nếu là lưu lượng, giữ `water_usage` nếu là tổng lượng nước; API trả đúng key `water_flow` |
| Role mock có 5 role, yêu cầu chính thức có 3 role | SRS/Use Case vs MockData | seed 3 role chính; nếu cần nghiệp vụ phụ, dùng permissions hoặc `position` |
| Thiếu bảng permissions nếu phân quyền chi tiết | yêu cầu authorization theo module | có thể dùng policy theo role trước; thêm `permissions`, `role_permissions` nếu cần cấu hình động |
| Thiếu maintenance schedule riêng | SRS FR-06.7 | dùng `contract_services` cho chu kỳ; nếu cần lịch hẹn cụ thể, thêm `maintenance_schedules` |

### 4.4 Chuẩn hóa và mở rộng

- `available` trong inventory không lưu cố định, tính từ `quantity - reserved_quantity`.
- `stock_status`, `expiring_soon`, KPI/chart không lưu, tính qua query/service.
- Tên hiển thị như customer name/product name không lưu trùng trong contract/device; lấy qua relationship.
- `activity_log.record_id` cần đi kèm `module` hoặc `record_type` để truy vết đa bảng.
- `attachments.related_type`/`related_id` nên triển khai polymorphic.

### 4.5 Index cần thiết

- Unique: `roles.role_name`, `users.username`, `users.email`, `employees.employee_code`, `employees.email`, `customers.customer_code`, `products.product_code`, `batches.batch_code`, `contracts.contract_code`, `devices.device_code`, `devices.serial_number`.
- FK index: `users.role_id`, `users.employee_id`, `products.category_id`, `inventories.product_id`, `batches.supplier_id`, `batch_details.batch_id`, `batch_details.product_id`, `contracts.customer_id`, `devices.customer_id`, `devices.contract_id`, `devices.product_id`, `maintenance_records.device_id`, `maintenance_records.employee_id`, `device_dashboard_data.device_id`, `activity_log.user_id`.
- Search/filter: `customers.customer_name`, `customers.phone`, `customers.email`, `devices.status`, `contracts.status`, `contracts.end_date`, `maintenance_records.maintenance_date`, `activity_log.created_at`.

## 5. Backend Architecture đề xuất

Kiến trúc Laravel phù hợp:

- MVC: Controller nhận request và trả view/API response.
- Service Layer: chứa nghiệp vụ, tính KPI, cập nhật tồn kho, tạo hợp đồng, log hoạt động.
- Repository: chỉ dùng cho module query phức tạp hoặc cần tái sử dụng nhiều như Dashboard, DeviceTelemetry, Inventory. CRUD đơn giản có thể dùng Eloquent trực tiếp trong Service.
- Form Request: validate từng form/API.
- Resource: chuẩn hóa JSON cho API, giữ key khớp Frontend.
- Middleware: `auth`, `role`, `permission` nếu mở rộng, `verified/status.active`.
- Policy: kiểm soát quyền theo model/module.
- Exception Handling: trả lỗi validation 422, not found 404, unauthorized 401/403, business error 409.
- Logging: Laravel log cho lỗi hệ thống; `activity_log` cho thao tác nghiệp vụ.

Lý do:

- Frontend hiện là Blade Laravel, nên Backend Laravel MVC giúp tích hợp nhanh.
- Service tách nghiệp vụ khỏi Controller, dễ test và dùng lại cho API.
- Policy/FormRequest bám đúng yêu cầu phân quyền và validation.

## 6. Thiết kế module Backend

### 6.1 Auth/Profile

- Model: `User`, `Role`, `Employee`.
- Controller: `AuthController`, `ProfileController`.
- Service: `AuthService`, `ProfileService`.
- Form Request: `LoginRequest`, `UpdateProfileRequest`, `ChangePasswordRequest`, `UploadAvatarRequest`.
- Resource: `UserResource`, `ProfileResource`.
- Policy: `UserPolicy`, `ProfilePolicy`.
- Middleware: `auth`, `active_user`, `role`.

### 6.2 Dashboard

- Model: dùng `Customer`, `Product`, `Device`, `Contract`, `MaintenanceRecord`, `ActivityLog`.
- Controller: `DashboardController`, `Api\DashboardController`.
- Service: `DashboardService`.
- Resource: `DashboardSummaryResource`, `ChartResource`, `ActivityLogResource`, `MaintenanceRecordResource`.
- Middleware: `auth`.

### 6.3 Product/Category

- Model: `Product`, `Category`.
- Controller: `ProductController`, `CategoryController`.
- Service: `ProductService`, `CategoryService`.
- Form Request: `StoreProductRequest`, `UpdateProductRequest`, `StoreCategoryRequest`, `UpdateCategoryRequest`.
- Resource: `ProductResource`, `CategoryResource`.
- Migration/Seeder: `categories`, `products`.
- Policy: `ProductPolicy`, `CategoryPolicy`.

### 6.4 Inventory/Batch/Supplier

- Model: `Inventory`, `Supplier`, `Batch`, `BatchDetail`.
- Controller: `InventoryController`, `BatchController`, `SupplierController`.
- Service: `InventoryService`, `BatchService`.
- Form Request: `StoreBatchRequest`, `UpdateBatchRequest`, `AdjustInventoryRequest`, `StoreSupplierRequest`.
- Resource: `InventoryResource`, `BatchResource`, `BatchDetailResource`, `SupplierResource`.
- Migration/Seeder: `suppliers`, `inventories`, `batches`, `batch_details`.
- Policy: `InventoryPolicy`, `BatchPolicy`.

### 6.5 Customer

- Model: `Customer`.
- Controller: `CustomerController`.
- Service: `CustomerService`.
- Form Request: `StoreCustomerRequest`, `UpdateCustomerRequest`.
- Resource: `CustomerResource`, `CustomerDetailResource`.
- Migration/Seeder: `customers`.
- Policy: `CustomerPolicy`.

### 6.6 Contract

- Model: `Contract`, `ContractService`.
- Controller: `ContractController`.
- Service: `ContractService`.
- Form Request: `StoreContractRequest`, `UpdateContractRequest`, `StoreContractServiceRequest`.
- Resource: `ContractResource`, `ContractServiceResource`.
- Migration/Seeder: `contracts`, `contract_services`.
- Policy: `ContractPolicy`.

### 6.7 Device/Telemetry/Maintenance

- Model: `Device`, `DeviceDashboardData`, `MaintenanceRecord`.
- Controller: `DeviceController`, `DeviceTelemetryController`, `MaintenanceController`.
- Service: `DeviceService`, `TelemetryService`, `MaintenanceService`.
- Form Request: `StoreDeviceRequest`, `UpdateDeviceRequest`, `StoreTelemetryRequest`, `StoreMaintenanceRequest`, `UpdateMaintenanceRequest`.
- Resource: `DeviceResource`, `DeviceDetailResource`, `TelemetrySeriesResource`, `MaintenanceRecordResource`.
- Migration/Seeder: `devices`, `device_dashboard_data`, `maintenance_records`.
- Policy: `DevicePolicy`, `MaintenancePolicy`.

### 6.8 Employee

- Model: `Employee`, `User`, `Role`.
- Controller: `EmployeeController`.
- Service: `EmployeeService`.
- Form Request: `StoreEmployeeRequest`, `UpdateEmployeeRequest`, `AssignRoleRequest`.
- Resource: `EmployeeResource`.
- Migration/Seeder: `employees`, `users`, `roles`.
- Policy: `EmployeePolicy`.

### 6.9 Activity Log/Attachment

- Model: `ActivityLog`, `Attachment`.
- Controller: `ActivityLogController`, `AttachmentController`.
- Service: `ActivityLogService`, `AttachmentService`.
- Form Request: `ActivityLogFilterRequest`, `UploadAttachmentRequest`.
- Resource: `ActivityLogResource`, `AttachmentResource`.
- Migration/Seeder: `activity_log`, `attachments`.
- Policy: `ActivityLogPolicy`, `AttachmentPolicy`.

## 7. Authentication và Authorization

### 7.1 Authentication

- Dùng Laravel session guard cho Blade web.
- Password hash bằng Laravel Hash.
- Login bằng email hoặc username. Frontend đang nhập email nên email là chính.
- Sau login: cập nhật `users.last_login`, ghi `activity_log`.
- Logout: invalidate session, regenerate CSRF token.
- User inactive không được login.

### 7.2 Role chính thức

Seed 3 role theo yêu cầu:

- Administrator
- Employee
- Technician

Mapping từ MockData:

- `Quản trị hệ thống` -> Administrator.
- `Kỹ thuật viên` -> Technician.
- Các role còn lại trong mock -> Employee hoặc position nội bộ, không dùng làm role chính.

### 7.3 Ma trận quyền

| Module | Administrator | Employee | Technician |
|---|---|---|---|
| Dashboard | xem | xem | xem |
| Product | CRUD | xem | xem |
| Inventory | CRUD/điều chỉnh | xem, nhập/xuất theo phân công | xem |
| Batch | CRUD/xác nhận nhập kho | xem | xem |
| Customer | CRUD | CRUD | xem khách hàng liên quan thiết bị/bảo trì |
| Contract | CRUD | CRUD | xem |
| Device | CRUD/assign | CRUD/assign | xem, cập nhật trạng thái/bảo trì được giao |
| Device Dashboard | xem/export | xem/export | xem thiết bị được giao |
| Maintenance | CRUD | tạo/xem | cập nhật record được giao |
| Employee | CRUD, gán role | không | không |
| Activity Log | xem/lọc | không | không |
| Profile | xem/sửa của mình | xem/sửa của mình | xem/sửa của mình |
| Attachment | CRUD theo module được phép | CRUD theo module được phép | upload/xem theo maintenance được giao |

## 8. Routing

### 8.1 Web Routes

| Route | Method | Controller | Màn hình |
|---|---|---|---|
| `/login` | GET | `AuthController@showLoginForm` | login |
| `/login` | POST | `AuthController@login` | submit login |
| `/logout` | POST | `AuthController@logout` | logout |
| `/` | GET | redirect/dashboard | dashboard |
| `/dashboard` | GET | `DashboardController@index` | dashboard |
| `/products` | GET | `ProductController@index` | products index |
| `/inventory` | GET | `InventoryController@index` | inventory index |
| `/batches` | GET | `BatchController@index` | batch index |
| `/batches/{batch}` | GET | `BatchController@show` | batch detail |
| `/customers` | GET | `CustomerController@index` | customer index |
| `/customers/{customer}` | GET | `CustomerController@show` | customer detail |
| `/contracts` | GET | `ContractController@index` | contract index |
| `/devices` | GET | `DeviceController@index` | device index |
| `/devices/{device}` | GET | `DeviceController@show` | device detail |
| `/employees` | GET | `EmployeeController@index` | employees index |
| `/activities` | GET | `ActivityLogController@index` | activities index |
| `/profile` | GET | `ProfileController@index` | profile |
| `/profile` | PUT/PATCH | `ProfileController@update` | profile form |
| `/profile/password` | PUT/PATCH | `ProfileController@changePassword` | password form |
| `/profile/avatar` | POST | `ProfileController@uploadAvatar` | avatar |

Các route CRUD create/store/edit/update/destroy nên thêm khi Frontend có form/modal tương ứng.

### 8.2 API Routes

API dùng cho AJAX/mobile/tách UI sau này. Dùng prefix `/api`, middleware `auth:sanctum` nếu cần token; nếu chỉ Blade AJAX cùng session có thể dùng web guard + CSRF.

Resource chính:

- `api/products`, `api/categories`
- `api/inventories`
- `api/batches`, `api/batches/{batch}/details`
- `api/customers`, `api/customers/{customer}/devices`, `api/customers/{customer}/contracts`, `api/customers/{customer}/maintenance`
- `api/contracts`
- `api/devices`, `api/devices/{device}/telemetry`, `api/devices/{device}/maintenance`
- `api/employees`
- `api/maintenance-records`
- `api/activity-logs`
- `api/profile`

## 9. Validation theo form/màn hình

### Login

- `email`: required, email, exists users.email.
- `password`: required, string.
- `remember`: nullable, boolean.
- Business: user active, password đúng.

### Product

- `product_code`: required, string max 50, unique.
- `product_name`: required, string max 150.
- `category_id`: required, exists categories.
- `model`: required, string max 100.
- `capacity`: nullable string max 50.
- `unit`: required string max 20.
- `price`: nullable numeric min 0.
- `status`: required in active, maintenance, inactive.
- `image`: nullable image max cấu hình.

### Inventory

- `product_id`: required, exists products, unique trong inventories nếu mỗi product một dòng tồn.
- `quantity`: required integer min 0.
- `reserved_quantity`: required integer min 0, lte quantity.
- `unit_cost`: nullable numeric min 0.
- Business: không giảm quantity thấp hơn reserved nếu đã có giữ chỗ.

### Batch

- `batch_code`: required, unique, max 50.
- `supplier_id`: nullable, exists suppliers.
- `import_date`: required date.
- `expiry_date`: nullable date after_or_equal import_date.
- `note`: nullable string.
- `details`: required array min 1.
- `details.*.product_id`: required exists products.
- `details.*.quantity`: required integer min 1.
- `details.*.unit_cost`: required numeric min 0.

### Customer

- `customer_code`: required, unique, max 20.
- `customer_name`: required, string max 150.
- `phone`: required, string max 20.
- `email`: nullable, email, unique customers.email.
- `address`: nullable string max 255.
- `type`: required in individual, company.
- `status`: required in active, inactive.
- `avatar`: nullable image.

### Contract

- `contract_code`: required, unique, max 20.
- `customer_id`: required, exists customers.
- `contract_type`: required in install, maintenance, replace.
- `start_date`: required date.
- `install_date`: nullable date after_or_equal start_date.
- `end_date`: required date after start_date.
- `maintenance_cycle_months`: nullable integer in 3,6,12.
- `amount`: nullable numeric min 0.
- `status`: required in active, expired, cancelled.
- Business: customer active; device thuộc customer nếu chọn `device_id`.

### Device

- `device_code`: required, unique, max 20.
- `serial_number`: required, unique, max 100.
- `product_id`: required, exists products.
- `customer_id`: nullable, exists customers.
- `contract_id`: nullable, exists contracts.
- `install_date`: nullable date.
- `firmware_version`: nullable string max 50.
- `location`: nullable string max 255.
- `status`: required in active, maintenance, error, pending, inactive.
- Business: contract/customer phải tương thích nếu cả hai được chọn.

### Telemetry

- `device_id`: required, exists devices.
- `recorded_at`: required date.
- `temperature`: nullable numeric.
- `tds`: nullable numeric min 0.
- `water_flow`: nullable numeric min 0.
- `ph`: nullable numeric between 0 and 14.
- `status`: nullable in good, warning, bad.

### Maintenance

- `device_id`: required, exists devices.
- `employee_id`: required, exists employees.
- `maintenance_date`: required date.
- `maintenance_type`: required in routine, repair, replace.
- `description`: required string.
- `parts_used`: nullable string.
- `cost`: nullable numeric min 0.
- `status`: required in completed, pending.
- Business: employee phải là Technician hoặc được phép bảo trì.

### Employee/User

- `employee_code`: required, unique, max 20.
- `full_name`: required, max 100.
- `position`: required, max 100.
- `phone`: nullable, max 20.
- `email`: required, email, unique employees.email.
- `address`: nullable max 255.
- `hire_date`: nullable date.
- `status`: required in active, inactive.
- `role_id`: required nếu tạo account, exists roles.
- `password`: required khi tạo user, confirmed, min 8.

### Profile

- `full_name`: required max 100.
- `email`: required email unique users/employees except current.
- `phone`: nullable max 20.
- `address`: nullable max 255.
- `avatar`: nullable image.
- `current_password`: required khi đổi password.
- `password`: required, confirmed, min 8.

## 10. Migration Plan

Thứ tự migration đề xuất:

1. `create_roles_table`
2. `create_employees_table`
3. `create_users_table`
4. `create_customers_table`
5. `create_categories_table`
6. `create_products_table`
7. `create_suppliers_table`
8. `create_inventories_table`
9. `create_batches_table`
10. `create_batch_details_table`
11. `create_contracts_table`
12. `create_contract_services_table`
13. `create_devices_table`
14. `create_device_dashboard_data_table`
15. `create_maintenance_records_table`
16. `create_activity_log_table`
17. `create_attachments_table`

Quan hệ chính:

- `roles 1-n users`
- `employees 1-0..1 users`
- `categories 1-n products`
- `products 1-1 inventories`
- `suppliers 1-n batches`
- `batches 1-n batch_details`
- `products 1-n batch_details`
- `customers 1-n contracts`
- `customers 1-n devices`
- `contracts 1-n contract_services`
- `contracts 1-n devices`
- `products 1-n devices`
- `devices 1-n device_dashboard_data`
- `devices 1-n maintenance_records`
- `employees 1-n maintenance_records`
- `users 1-n activity_log`
- `attachments` polymorphic theo related type/id.

## 11. Seeder Plan

Seeder phải đủ dữ liệu để hiển thị toàn bộ Frontend hiện tại.

Thứ tự:

1. `RoleSeeder`: Administrator, Employee, Technician.
2. `EmployeeSeeder`: tối thiểu 9 nhân viên, có technician để gán bảo trì.
3. `UserSeeder`: admin, employee, technician, password hash.
4. `CategorySeeder`: RO, Nano, Công nghiệp, Lõi lọc & Phụ kiện.
5. `ProductSeeder`: tối thiểu 10 sản phẩm tương ứng MockData.
6. `SupplierSeeder`: tối thiểu 3 nhà cung cấp.
7. `InventorySeeder`: mỗi product một inventory, có trạng thái ok/low/out khi tính toán.
8. `BatchSeeder`: tối thiểu 6 batch.
9. `BatchDetailSeeder`: chi tiết sản phẩm trong từng batch.
10. `CustomerSeeder`: tối thiểu 24 khách hàng cá nhân/doanh nghiệp.
11. `ContractSeeder`: tối thiểu 18 hợp đồng, có active/expired/cancelled, có hợp đồng sắp hết hạn.
12. `DeviceSeeder`: tối thiểu 30 thiết bị, đủ active/maintenance/error/pending.
13. `DeviceDashboardDataSeeder`: dữ liệu telemetry cho 24h/7d/30d.
14. `MaintenanceRecordSeeder`: tối thiểu 20 record, đủ routine/repair/replace, completed/pending.
15. `ActivityLogSeeder`: tối thiểu 25 log.

Yêu cầu seed:

- Giữ mã gần giống mock để UI hiển thị ổn: `AQ-*`, `LOT-2025-*`, `KH-*`, `HĐ-2025-*`, `TB-*`, `NV-*`, `BT-*`.
- Dữ liệu ngày phải tạo được chart theo tháng.
- Có ít nhất một khách hàng có nhiều thiết bị/hợp đồng/bảo trì để test trang chi tiết.

## 12. API Planning

Định dạng lỗi chuẩn:

- 401: chưa đăng nhập.
- 403: không đủ quyền.
- 404: không tìm thấy.
- 422: validation errors `{ message, errors }`.
- 409: lỗi nghiệp vụ như tồn kho không đủ, hợp đồng/customer không khớp.
- 500: lỗi hệ thống, ghi Laravel log.

### Dashboard

| API | Method | Request | Response chính |
|---|---|---|---|
| `/api/dashboard/summary` | GET | none | `kpis[]` label/value/icon/color/trend/up |
| `/api/dashboard/device-status` | GET | none | labels, series |
| `/api/dashboard/customer-growth` | GET | `year?` | labels, series |
| `/api/dashboard/maintenance-stats` | GET | `year?` | labels, series |
| `/api/dashboard/expiring-contracts` | GET | `days?, limit?` | contract code/customer/end_date |
| `/api/dashboard/recent-activities` | GET | `limit?` | activities |
| `/api/dashboard/recent-maintenance` | GET | `limit?` | maintenance |

### Product

| API | Method | Request | Response |
|---|---|---|---|
| `/api/products` | GET | `search?, category_id?, status?, page?` | paginated products |
| `/api/products` | POST | product form | product |
| `/api/products/{id}` | GET | none | product detail |
| `/api/products/{id}` | PUT/PATCH | product form | product |
| `/api/products/{id}` | DELETE | none | success |
| `/api/categories` | GET | `status?` | categories |

### Inventory/Batch

| API | Method | Request | Response |
|---|---|---|---|
| `/api/inventories` | GET | `search?, stock_status?` | inventories with available/stock_status |
| `/api/inventories/{id}` | PATCH | quantity/reserved/unit_cost | inventory |
| `/api/batches` | GET | `search?` | batches |
| `/api/batches` | POST | batch + details | batch detail |
| `/api/batches/{id}` | GET | none | batch with details |
| `/api/batches/{id}` | PATCH | batch fields | batch |

### Customer

| API | Method | Request | Response |
|---|---|---|---|
| `/api/customers` | GET | `search?, status?, type?` | paginated customers |
| `/api/customers` | POST | customer form | customer |
| `/api/customers/{id}` | GET | none | customer detail |
| `/api/customers/{id}` | PATCH | customer form | customer |
| `/api/customers/{id}` | DELETE | none | success/inactive |
| `/api/customers/{id}/devices` | GET | none | devices |
| `/api/customers/{id}/contracts` | GET | none | contracts |
| `/api/customers/{id}/maintenance` | GET | none | maintenance |

### Contract

| API | Method | Request | Response |
|---|---|---|---|
| `/api/contracts` | GET | `search?, status?, customer_id?` | contracts |
| `/api/contracts` | POST | contract form | contract |
| `/api/contracts/{id}` | GET | none | contract detail |
| `/api/contracts/{id}` | PATCH | contract form | contract |
| `/api/contracts/{id}` | DELETE | none | success/cancelled |

### Device/Telemetry/Maintenance

| API | Method | Request | Response |
|---|---|---|---|
| `/api/devices` | GET | `search?, status?, customer_id?` | devices + counts optional |
| `/api/devices` | POST | device form | device |
| `/api/devices/{id}` | GET | none | device detail |
| `/api/devices/{id}` | PATCH | device form/status | device |
| `/api/devices/{id}/telemetry` | GET | `range=24h|7d|30d` | labels, tds, temperature, water_flow, ph |
| `/api/devices/{id}/telemetry` | POST | telemetry payload | telemetry record |
| `/api/devices/{id}/maintenance` | GET | none | maintenance records |
| `/api/maintenance-records` | POST | maintenance form | maintenance record |
| `/api/maintenance-records/{id}` | PATCH | maintenance form | maintenance record |

### Employee/Activity/Profile

| API | Method | Request | Response |
|---|---|---|---|
| `/api/employees` | GET | `search?, role_id?, status?` | employees |
| `/api/employees` | POST | employee/account form | employee |
| `/api/employees/{id}` | PATCH | employee form | employee |
| `/api/employees/{id}` | DELETE | none | inactive/success |
| `/api/activity-logs` | GET | `search?, module?, user_id?, from?, to?` | activity logs |
| `/api/profile` | GET | none | current profile |
| `/api/profile` | PATCH | profile form | profile |
| `/api/profile/password` | PATCH | password form | success |
| `/api/profile/avatar` | POST | avatar file | avatar url |

## 13. Điểm không thống nhất và phương án xử lý

| Không thống nhất | Ưu tiên | Phương án |
|---|---|---|
| Frontend có 5 role mock, yêu cầu chính thức 3 role | SRS/Use Case | Backend seed 3 role chính; `position` lưu chức vụ chi tiết |
| Frontend có nút thêm/tạo/nhập nhưng chưa có form | Frontend hiện tại | Backend vẫn thiết kế CRUD API/FormRequest; chỉ bật UI khi có form |
| ERD device status ít hơn Frontend | Frontend | Mở rộng status để đáp ứng badge/filter hiện tại |
| ERD thiếu firmware/capacity/avatar/install_date/cycle | Frontend | Bổ sung cột hoặc Resource mapping tương ứng |
| Device telemetry mock là dữ liệu tĩnh sin wave | SRS yêu cầu dữ liệu thiết bị | DB lưu record theo thời gian; API group dữ liệu theo range |
| Activity icon chỉ có trong mock | UI | Không bắt buộc lưu DB; Resource có thể map icon theo module/action |
| README nói chưa có DB/API/Auth | hiện trạng | Backend sẽ thay mock bằng DB/API/session thật theo roadmap |

## 14. Development Roadmap

### Phase 1: Nền tảng

1. Cấu hình Laravel backend, env MySQL, auth session.
2. Tạo migration theo ERD đã điều chỉnh.
3. Tạo seed dữ liệu khớp Frontend.
4. Tạo role/middleware/policy nền tảng.

### Phase 2: Core master data

1. Product/Category.
2. Supplier/Batch/BatchDetail.
3. Inventory với tính toán available/stock_status.

### Phase 3: Customer/Contract/Device

1. Customer CRUD và detail relationships.
2. Contract CRUD, service cycle, expiring contracts.
3. Device CRUD, assign customer/contract, status counts.

### Phase 4: Maintenance/Telemetry/Dashboard

1. Maintenance records.
2. Device telemetry ingestion/query.
3. DashboardService tổng hợp KPI/chart/widget.

### Phase 5: User/Admin

1. Employee/User management.
2. Profile update/password/avatar.
3. Activity log đầy đủ cho thao tác ghi.

### Phase 6: Hoàn thiện API và tích hợp Frontend

1. Thay `MockData` calls bằng Eloquent/Service.
2. Chuẩn hóa Resource trả dữ liệu đúng key Frontend.
3. Thêm filter/search server-side nếu DataTables chuyển qua AJAX.
4. Test policy, validation, business rules.

## 15. Test Plan đề xuất

- Feature tests cho auth login/logout/role guard.
- Feature tests cho CRUD từng module.
- Validation tests cho FormRequest quan trọng.
- Service tests cho inventory available/stock_status, dashboard KPI, expiring contract, telemetry range.
- Policy tests cho Administrator/Employee/Technician.
- Seeder smoke test: chạy seed xong các màn hình hiện tại có dữ liệu.

## 16. Kết luận

Backend nên phát triển theo Laravel MVC + Service Layer, dùng ERD hiện tại làm nền nhưng cần điều chỉnh schema để khớp Frontend chính thức. Ưu tiên đầu tiên là thay nguồn `MockData` bằng DB seeded tương đương, sau đó mở rộng CRUD/API theo SRS và Use Case. Các dữ liệu tính toán như KPI, tồn khả dụng, trạng thái tồn kho, hợp đồng sắp hết hạn và telemetry series nên nằm trong Service/Resource, không lưu trùng trong database.
