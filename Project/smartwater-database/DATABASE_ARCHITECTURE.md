# SmartWater Database: ERD và kiến trúc

Tài liệu này mô tả schema MySQL hiện tại được định nghĩa bởi các migration trong
`database/migrations`. `smartwater-database` là chủ sở hữu schema; SmartWater
Admin và Device Monitor chỉ sử dụng schema đã được migrate.

- Database mặc định: `smartwater_database`
- Migration runner: `migrate.bat`
- Migrations: `database/migrations/`
- Seeders: `database/seeders/`

## Kiến trúc dữ liệu

Schema được chia thành sáu nhóm:

| Nhóm | Bảng | Vai trò |
| --- | --- | --- |
| Phân quyền | `roles`, `employees`, `users` | Quản lý vai trò, nhân sự và tài khoản. |
| Danh mục và kho | `categories`, `products`, `suppliers`, `inventories`, `batches`, `batch_details` | Quản lý sản phẩm, tồn kho và nhập hàng. |
| Khách hàng và hợp đồng | `customers`, `contracts`, `contract_services` | Quản lý khách hàng và dịch vụ theo hợp đồng. |
| Thiết bị và MCU | `devices`, `mcus`, `device_dashboard_data`, `maintenance_records` | Quản lý thiết bị lắp đặt, MCU, dữ liệu dashboard cũ và bảo trì. |
| Telemetry thời gian thực | `telemetry`, `device_logs`, `device_status` | Lưu TDS, cảnh báo, log và trạng thái kết nối theo `mcu_id`. |
| Kiểm toán và tệp | `activity_logs`, `attachments` | Theo dõi thao tác và tệp đính kèm đa hình. |

Hầu hết bảng nghiệp vụ có khóa chính `id` và hai cột `created_at`, `updated_at`.
Các bảng telemetry tách khỏi dữ liệu nghiệp vụ để phù hợp cho luồng ghi liên tục
từ Device Monitor.

## ERD

```mermaid
erDiagram
    ROLES ||--o{ EMPLOYEES : "role_id"
    ROLES ||--o{ USERS : "role_id"
    EMPLOYEES o|--o| USERS : "employee_id"

    CATEGORIES ||--o{ PRODUCTS : "category_id"
    PRODUCTS ||--o| INVENTORIES : "product_id"
    SUPPLIERS o|--o{ BATCHES : "supplier_id"
    BATCHES ||--o{ BATCH_DETAILS : "batch_id"
    PRODUCTS ||--o{ BATCH_DETAILS : "product_id"

    CUSTOMERS ||--o{ CONTRACTS : "customer_id"
    CONTRACTS o|--o{ CONTRACT_SERVICES : "contract_id"

    PRODUCTS ||--o{ DEVICES : "product_id"
    CUSTOMERS o|--o{ DEVICES : "customer_id"
    CONTRACTS o|--o{ DEVICES : "contract_id"
    BATCHES o|--o{ DEVICES : "batch_id"
    MCUS o|--o{ DEVICES : "mcu_id -> mcus.mcu_id"
    DEVICES o|--o| DEVICES : "replaced_by_device_id"

    DEVICES ||--o{ DEVICE_DASHBOARD_DATA : "device_id"
    DEVICES ||--o{ MAINTENANCE_RECORDS : "device_id"
    EMPLOYEES ||--o{ MAINTENANCE_RECORDS : "employee_id"

    USERS ||--o{ ACTIVITY_LOGS : "user_id"
    USERS ||--o{ ATTACHMENTS : "uploaded_by"

    MCUS ||--o{ TELEMETRY : "logical mcu_id"
    MCUS ||--o{ DEVICE_LOGS : "logical mcu_id"
    MCUS ||--o| DEVICE_STATUS : "logical mcu_id"

    ROLES {
        bigint id PK
        varchar name UK
    }
    EMPLOYEES {
        bigint id PK
        bigint role_id FK
        varchar employee_code UK
    }
    USERS {
        bigint id PK
        bigint role_id FK
        bigint employee_id FK
        varchar username UK
    }
    CATEGORIES {
        bigint id PK
    }
    PRODUCTS {
        bigint id PK
        bigint category_id FK
        varchar product_code UK
    }
    INVENTORIES {
        bigint id PK
        bigint product_id FK
    }
    SUPPLIERS {
        bigint id PK
    }
    BATCHES {
        bigint id PK
        bigint supplier_id FK
        varchar batch_code UK
    }
    BATCH_DETAILS {
        bigint id PK
        bigint batch_id FK
        bigint product_id FK
    }
    CUSTOMERS {
        bigint id PK
        varchar customer_code UK
    }
    CONTRACTS {
        bigint id PK
        bigint customer_id FK
        varchar contract_code UK
    }
    CONTRACT_SERVICES {
        bigint id PK
        bigint contract_id FK
    }
    MCUS {
        bigint id PK
        varchar mcu_id UK
        varchar serial_number UK
        varchar connection_status
    }
    DEVICES {
        bigint id PK
        bigint product_id FK
        bigint customer_id FK
        bigint contract_id FK
        bigint batch_id FK
        varchar mcu_id FK
        bigint replaced_by_device_id FK
        varchar device_code UK
    }
    TELEMETRY {
        bigint id PK
        datetime timestamp
        varchar mcu_id IDX
        decimal tds
        varchar alert
    }
    DEVICE_LOGS {
        bigint id PK
        varchar mcu_id IDX
    }
    DEVICE_STATUS {
        bigint id PK
        varchar mcu_id UK
    }
    DEVICE_DASHBOARD_DATA {
        bigint id PK
        bigint device_id FK
        datetime recorded_at
    }
    MAINTENANCE_RECORDS {
        bigint id PK
        bigint device_id FK
        bigint employee_id FK
    }
    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
    }
    ATTACHMENTS {
        bigint id PK
        bigint uploaded_by FK
        varchar related_type
        bigint related_id
    }
```

## Quan hệ chính

### Chuỗi nghiệp vụ thiết bị

Một `customer` có nhiều `contracts`. Một `device` tham chiếu `product`, và có
thể gắn trực tiếp với `customer`, `contract`, `batch` và một `mcu`. Liên kết
`devices.mcu_id -> mcus.mcu_id` là khóa ngoại dùng định danh MCU dạng chuỗi.

`devices.replaced_by_device_id` là self-reference để lưu thiết bị thay thế;
`replaced_at` đánh dấu thời điểm thay thế.

### Telemetry và trạng thái MCU

`mcus.mcu_id` là định danh chuỗi tối đa 50 ký tự, duy nhất. Bảng `telemetry`
lưu các trường `timestamp`, `topic`, `mcu_id`, `tds` và `alert`.

`telemetry.mcu_id` không có khóa ngoại trong migration telemetry. Đây là liên
kết logic với `mcus.mcu_id`, cho phép Device Monitor ghi nhận telemetry trước
khi dữ liệu MCU nghiệp vụ hoàn thiện. Để truy vấn theo thời gian, bảng có index
trên `timestamp`, `mcu_id` và index kết hợp `(mcu_id, timestamp)`.

`device_logs` và `device_status` cũng lưu `mcu_id` theo liên kết logic.
`device_status.mcu_id` là duy nhất, đại diện cho trạng thái mới nhất của một
MCU; `device_logs` lưu nhiều sự kiện theo MCU.

### Kho và bảo trì

`products` thuộc `categories`; một product có tối đa một bản ghi `inventories`.
`batches` thuộc `suppliers`, còn `batch_details` là bảng chi tiết nối batch với
product. `maintenance_records` nối một device với employee thực hiện bảo trì.

### Kiểm toán và tệp đính kèm

`activity_logs` thuộc `users`. `attachments.uploaded_by` cũng tham chiếu
`users`, còn cặp `(related_type, related_id)` là liên kết đa hình để gắn tệp với
nhiều loại bản ghi nghiệp vụ; cặp này được đánh index.

## Chỉ mục và ràng buộc đáng chú ý

| Bảng | Ràng buộc/chỉ mục | Mục đích |
| --- | --- | --- |
| `mcus` | `mcu_id` unique | Định danh MCU duy nhất. |
| `devices` | `device_code`, `serial_number` unique; `mcu_id` FK | Nhận diện thiết bị và gắn MCU. |
| `telemetry` | `timestamp`, `mcu_id`, `(mcu_id, timestamp)` | Lọc lịch sử TDS theo MCU và thời gian. |
| `inventories` | `product_id` unique | Mỗi product có tối đa một bản ghi tồn kho. |
| `products`, `customers`, `contracts`, `batches` | Mã nghiệp vụ unique | Tránh trùng mã product, customer, contract, batch. |
| `maintenance_records` | `maintenance_date` | Hỗ trợ truy vấn lịch bảo trì. |
| `activity_logs` | `created_at`, `(user_id, created_at)` | Tra cứu lịch sử thao tác. |
| `attachments` | `(related_type, related_id)` | Tra cứu tệp theo bản ghi liên quan. |

## Dữ liệu mẫu liên kết end-to-end

`ConnectedTelemetryDemoSeeder` tạo dữ liệu mẫu có quan hệ đầy đủ:

```text
KH-DEMO-001 (Customer)
  -> HD-DEMO-001 (Contract)
  -> SP-DEMO-001 (Product)
  -> TB-DEMO-001 (Device)
  -> MCU-DEMO-001 (MCU, CONNECTED)
  -> 3 bản ghi telemetry TDS trên devices/telemetry
```

Chạy seeder riêng từ `Project/smartwater-admin`:

```bat
php artisan db:seed --class="Database\Seeders\ConnectedTelemetryDemoSeeder" --force
```

Seeder xóa telemetry cũ của `MCU-DEMO-001` trước khi tạo lại ba bản ghi, nên có
thể chạy lặp lại mà không tạo dữ liệu telemetry trùng.
