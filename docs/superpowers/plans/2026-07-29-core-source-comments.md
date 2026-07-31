# Core Source Comments Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add concise Vietnamese block comments that explain the executable architecture and cross-component data flow without changing system behavior.

**Architecture:** Comments follow telemetry from ESP32/simulator publishing, through Device Monitor normalization and persistence, into Admin monitoring and maintenance scheduling, ending at database schema/seeding ownership. Only hand-written entry points and business orchestration files are modified.

**Tech Stack:** C++17/Arduino/PlatformIO, PHP 8.1+, Slim 4, Laravel, Eloquent, Blade, MySQL.

## Global Constraints

- Comments explain blocks, responsibilities, inputs, outputs, side effects, and handoff points; they do not narrate individual statements.
- Do not change behavior, API routes, MQTT topics, payload fields, schema, validation, SQL, timing, or identifiers.
- Preserve external `mcu_id` as a string.
- `smartwater-database` remains the sole migration and seeding owner.
- Exclude `vendor`, `node_modules`, `.pio`, `storage/framework`, caches, logs, binaries, minified assets, and build output.
- Do not comment imports, framework boilerplate, trivial getters/setters, simple validation requests, or presentation-only Blade markup.

---

### Task 1: Comment firmware and simulator execution flow

**Files:**
- Modify: `Project/firmware/main/app_main.cpp`
- Modify: `Project/firmware/components/managers/device_manager/src/device_manager.cpp`
- Modify: `Project/firmware/components/managers/sensor_manager/src/sensor_manager.cpp`
- Modify: `Project/firmware/components/managers/network_manager/src/network_manager.cpp`
- Modify: `Project/firmware/components/services/wifi/src/wifi_manager.cpp`
- Modify: `Project/firmware/components/services/mqtt/src/mqtt_manager.cpp`
- Modify: `Project/firmware/components/drivers/tds_sensor/src/tds_sensor.cpp`
- Modify: `Project/firmware/Message_sending_test/src/main.cpp`
- Modify: `Project/firmware/Message_sending_test/src/simulator/SimulatorApp.cpp`
- Modify: `Project/firmware/Message_sending_test/src/mqtt/MqttManager.cpp`
- Modify: `Project/firmware/Message_sending_test/src/telemetry/TelemetryGenerator.cpp`

**Interfaces:**
- Consumes: sensor readings, Wi-Fi state, MQTT configuration, `Config::Device::MCU_ID`.
- Produces: JSON telemetry on `devices/telemetry` with string `mcu_id`, TDS and alert state.

- [ ] **Step 1: Record the pre-change firmware build state**

```powershell
cd Project\firmware
pio run
cd Message_sending_test
pio run
```

Expected: both environments compile, or any existing environment/dependency error is recorded before comment-only changes.

- [ ] **Step 2: Add orchestration comments to the primary firmware**

Add short comments before these blocks:

```cpp
// Entry point chỉ ủy quyền cho DeviceManager; toàn bộ khởi tạo phần cứng và task nền nằm trong manager này.
// Chu kỳ chính duy trì các tác vụ mạng và xử lý lệnh mà không chặn các FreeRTOS task đọc/gửi telemetry.
// Snapshot cảm biến được bảo vệ bằng mutex để task đọc cảm biến và task MQTT không dùng dữ liệu đang cập nhật dở.
// Khi MQTT mất kết nối, telemetry được giữ trong bộ đệm giới hạn và gửi bù sau khi kết nối phục hồi.
// TDS chỉ được xem là hợp lệ trong cửa sổ timeout; quá hạn sẽ tạo alert sensor_disconnected.
```

Place them only at `setup/loop`, `DeviceManager::begin/startTasks/cacheTelemetry/flushTelemetryCache`, `SensorManager::update/consumeSensorError`, network mutex/publish blocks, Wi-Fi reconnect, MQTT reconnect/publish, and TDS parsing/timeout.

- [ ] **Step 3: Add simulator data-flow comments**

Use block comments before simulator initialization, periodic generation, JSON construction, reconnect, and publish:

```cpp
// Simulator giữ cùng hợp đồng payload với firmware thật để backend xử lý hai nguồn theo một luồng duy nhất.
// Mỗi chu kỳ tạo một mẫu telemetry, gắn mcu_id dạng chuỗi rồi publish lên topic telemetry đã cấu hình.
```

- [ ] **Step 4: Rebuild both firmware targets**

```powershell
cd Project\firmware
pio run
cd Message_sending_test
pio run
```

Expected: result matches or improves on Step 1; comments introduce no compiler change.

- [ ] **Step 5: Commit firmware comments**

```powershell
git add Project/firmware/main/app_main.cpp Project/firmware/components/managers/device_manager/src/device_manager.cpp Project/firmware/components/managers/sensor_manager/src/sensor_manager.cpp Project/firmware/components/managers/network_manager/src/network_manager.cpp Project/firmware/components/services/wifi/src/wifi_manager.cpp Project/firmware/components/services/mqtt/src/mqtt_manager.cpp Project/firmware/components/drivers/tds_sensor/src/tds_sensor.cpp Project/firmware/Message_sending_test/src/main.cpp Project/firmware/Message_sending_test/src/simulator/SimulatorApp.cpp Project/firmware/Message_sending_test/src/mqtt/MqttManager.cpp Project/firmware/Message_sending_test/src/telemetry/TelemetryGenerator.cpp
git commit -m "docs: explain firmware telemetry flow"
```

---

### Task 2: Comment Device Monitor ingestion and query flow

**Files:**
- Modify: `Project/Device-monitor/public/index.php`
- Modify: `Project/Device-monitor/src/Database.php`
- Modify: `Project/Device-monitor/src/DatabaseFailure.php`
- Modify: `Project/Device-monitor/src/TelemetryService.php`
- Modify: `Project/Device-monitor/src/TelemetryRepository.php`

**Interfaces:**
- Consumes: HiveMQ-forwarded request body containing topic, raw payload, timestamp and source.
- Produces: normalized telemetry rows, MCU status updates, alert work orders, paginated API responses and TDS series.

- [ ] **Step 1: Record the pre-change Device Monitor test state**

```powershell
cd Project\Device-monitor
php tests/TelemetryRepositoryTest.php
php -l public/index.php
php -l src/Database.php
php -l src/DatabaseFailure.php
php -l src/TelemetryService.php
php -l src/TelemetryRepository.php
```

- [ ] **Step 2: Comment the HTTP boundary and safe failure path**

Add comments at static-file delegation, middleware setup, lazy repository creation, telemetry POST, chart range validation and database health route:

```php
// Chỉ mở kết nối PDO khi route thực sự cần dữ liệu; các trang HTML tĩnh vẫn có thể tải khi MySQL tạm thời lỗi.
// Boundary nhận telemetry chịu trách nhiệm chuẩn hóa request, còn service/repository xử lý hợp đồng payload và lưu trữ.
// Lỗi kết nối được ghi log nội bộ theo ngữ cảnh đã lọc và trả về thông báo 503 an toàn cho client.
```

- [ ] **Step 3: Comment normalization, persistence and read models**

Add comments before `normalize/decodePayload/flatten`, `insert`, `updateMcuStatus`, `createAlertWorkOrder`, `paginate`, `mcus`, `tdsSeries`, and `normalizeMcuId`:

```php
// Chuẩn hóa nhiều dạng payload về một cấu trúc telemetry duy nhất trước khi chạm database.
// Một lần ingest gồm ba tác động liên tiếp: lưu telemetry, cập nhật trạng thái MCU, rồi đồng bộ ticket alert nếu cần.
// open_key bảo đảm mỗi thiết bị và loại alert chỉ có một ticket đang mở; telemetry mới cập nhật snapshot thay vì nhân bản ticket.
// Cửa sổ biểu đồ được neo tại bản ghi mới nhất của đúng MCU để dữ liệu mô phỏng và dữ liệu trễ vẫn hiển thị nhất quán.
```

- [ ] **Step 4: Re-run Device Monitor verification**

```powershell
php tests/TelemetryRepositoryTest.php
php -l public/index.php
php -l src/Database.php
php -l src/DatabaseFailure.php
php -l src/TelemetryService.php
php -l src/TelemetryRepository.php
```

Expected: repository test passes and every file reports no syntax errors.

- [ ] **Step 5: Commit Device Monitor comments**

```powershell
git add Project/Device-monitor/public/index.php Project/Device-monitor/src/Database.php Project/Device-monitor/src/DatabaseFailure.php Project/Device-monitor/src/TelemetryService.php Project/Device-monitor/src/TelemetryRepository.php
git commit -m "docs: explain device monitor data flow"
```

---

### Task 3: Comment SmartWater Admin business flows

**Files:**
- Modify: `Project/smartwater-admin/routes/web.php`
- Modify: `Project/smartwater-admin/routes/api.php`
- Modify: `Project/smartwater-admin/routes/console.php`
- Modify: `Project/smartwater-admin/app/Http/Controllers/DeviceController.php`
- Modify: `Project/smartwater-admin/app/Http/Controllers/McuController.php`
- Modify: `Project/smartwater-admin/app/Http/Controllers/MaintenanceWorkOrderController.php`
- Modify: `Project/smartwater-admin/app/Http/Controllers/ActivityController.php`
- Modify: `Project/smartwater-admin/app/Services/DeviceTelemetryService.php`
- Modify: `Project/smartwater-admin/app/Services/McuService.php`
- Modify: `Project/smartwater-admin/app/Services/MaintenanceWorkOrderService.php`
- Modify: `Project/smartwater-admin/app/Console/Commands/GenerateScheduledMaintenanceWorkOrders.php`
- Modify: `Project/smartwater-admin/app/Console/Commands/SyncTelemetryAlertWorkOrders.php`
- Modify: `Project/smartwater-admin/app/Models/Mcu.php`
- Modify: `Project/smartwater-admin/app/Models/MaintenanceWorkOrder.php`

**Interfaces:**
- Consumes: authenticated management requests, persisted telemetry, contract schedules and open alerts.
- Produces: device views, read-only MCU status display, scheduled/alert work orders and Activity Log pages.

- [ ] **Step 1: Record focused Admin test state**

```powershell
cd Project\smartwater-admin
php artisan test tests/Feature/ActivityLogDatabaseTest.php tests/Feature/McuStatusOwnershipTest.php tests/Feature/MaintenanceWorkOrderRouteTest.php tests/Unit/MaintenanceWorkOrderServiceTest.php tests/Unit/McuModelTest.php
```

- [ ] **Step 2: Comment route and controller boundaries**

Add one comment per route group and before controller methods that combine multiple data sources:

```php
// Route quản trị chỉ điều phối request đã xác thực; nghiệp vụ và truy vấn phức tạp được giao cho service tương ứng.
// Trang chi tiết thiết bị ghép dữ liệu nghiệp vụ với telemetry thật theo mcu_id; phân trang log độc lập với chuỗi biểu đồ.
// Trang bảo trì ghép ticket công việc với danh sách thiết bị lỗi nhưng giữ hai bộ phân trang tách biệt.
```

- [ ] **Step 3: Comment telemetry and MCU ownership rules**

Add comments at `DeviceTelemetryService::forMcu/paginatedLogsForMcu`, `McuService::getUsedMcus/getUnusedMcus/createMcu/updateMcu/deleteMcu`, and the non-trivial model relations/scopes:

```php
// mcu_id là định danh chuỗi xuyên suốt firmware, telemetry và thiết bị; không chuyển sang khóa số nội bộ mcus.id.
// Dữ liệu quản lý chỉ được phép thay đổi thông tin đăng ký MCU; status do backend telemetry cập nhật và chỉ được hiển thị ở Admin.
// Biểu đồ dùng tối đa 500 bản ghi mới nhất rồi sắp lại theo thời gian tăng dần; log dùng paginator riêng để không làm đổi dữ liệu biểu đồ.
```

- [ ] **Step 4: Comment maintenance scheduling and alert synchronization**

Add comments at both console schedules, command handlers, `synchronizeScheduled`, `scheduleDates`, `synchronizeAlerts`, duplicate guards, completion and priority mapping:

```php
// Lịch định kỳ bắt đầu từ ngày hiệu lực hợp đồng và tăng theo maintenance_cycle_months; không lấy ngày bảo trì gần nhất làm mốc.
// Alert tạo lịch tức thì từ telemetry. Khóa chống trùng ngăn cùng một alert đang mở sinh nhiều ticket khi telemetry đến liên tục.
// Hai command chạy độc lập: lịch hợp đồng chạy hằng ngày, còn alert được đồng bộ mỗi phút và đều khóa chồng lệnh.
```

- [ ] **Step 5: Comment Activity Log database flow**

Add comments before the eager-loaded/search/pagination query:

```php
// Activity Log chỉ đọc bản ghi đã lưu trong database; eager load user.employee tránh truy vấn lặp khi hiển thị người thực hiện.
```

- [ ] **Step 6: Re-run Admin tests and syntax checks**

```powershell
php artisan test tests/Feature/ActivityLogDatabaseTest.php tests/Feature/McuStatusOwnershipTest.php tests/Feature/MaintenanceWorkOrderRouteTest.php tests/Unit/MaintenanceWorkOrderServiceTest.php tests/Unit/McuModelTest.php
php -l routes/web.php
php -l routes/api.php
php -l routes/console.php
php artisan view:clear
php artisan view:cache
```

Expected: focused tests pass, routes have no syntax errors and Blade compilation succeeds.

- [ ] **Step 7: Commit Admin comments**

```powershell
git add Project/smartwater-admin/routes Project/smartwater-admin/app/Http/Controllers/DeviceController.php Project/smartwater-admin/app/Http/Controllers/McuController.php Project/smartwater-admin/app/Http/Controllers/MaintenanceWorkOrderController.php Project/smartwater-admin/app/Http/Controllers/ActivityController.php Project/smartwater-admin/app/Services/DeviceTelemetryService.php Project/smartwater-admin/app/Services/McuService.php Project/smartwater-admin/app/Services/MaintenanceWorkOrderService.php Project/smartwater-admin/app/Console/Commands/GenerateScheduledMaintenanceWorkOrders.php Project/smartwater-admin/app/Console/Commands/SyncTelemetryAlertWorkOrders.php Project/smartwater-admin/app/Models/Mcu.php Project/smartwater-admin/app/Models/MaintenanceWorkOrder.php
git commit -m "docs: explain admin business flows"
```

---

### Task 4: Comment database ownership and maintenance schema

**Files:**
- Modify: `Project/smartwater-database/database/seeders/DatabaseSeeder.php`
- Modify: `Project/smartwater-database/database/migrations/2026_07_29_000000_create_maintenance_work_orders_table.php`

**Interfaces:**
- Consumes: empty or existing `smartwater_database` schema managed by the database project.
- Produces: dependency-ordered seed records and the maintenance work-order schema used by both web applications.

- [ ] **Step 1: Add dependency-order comments to DatabaseSeeder**

Use comments that divide the existing call list without changing its order:

```php
// Nhóm nền tảng phải tồn tại trước vì user và dữ liệu nghiệp vụ tham chiếu role/employee.
// Nhóm danh mục và kho tạo khóa cha trước batch detail, hợp đồng và thiết bị.
// MCU được seed trước thiết bị để devices.mcu_id luôn tham chiếu định danh chuỗi hợp lệ.
// Telemetry, bảo trì và activity log được seed cuối vì phụ thuộc toàn bộ quan hệ phía trên.
```

- [ ] **Step 2: Comment the maintenance schema invariants**

Add comments before schedule/alert fields, `open_key`, and indexes:

```php
// scheduled_for dùng cho lịch hợp đồng; triggered_at và telemetry_snapshot giữ bằng chứng của lịch tức thì từ alert.
// open_key là khóa chống trùng cho ticket đang mở; khi hoàn tất service đặt khóa này về null để alert mới có thể tạo ticket tiếp theo.
// Các index phục vụ hai truy vấn chính: ticket mở theo thiết bị và lịch định kỳ theo hợp đồng/ngày đến hạn.
```

- [ ] **Step 3: Run PHP syntax checks without executing migrations**

```powershell
php -l Project\smartwater-database\database\seeders\DatabaseSeeder.php
php -l Project\smartwater-database\database\migrations\2026_07_29_000000_create_maintenance_work_orders_table.php
git diff --check
```

Expected: no syntax or whitespace errors; no migration is executed from a web project.

- [ ] **Step 4: Review the complete diff for comment-only changes**

```powershell
git diff --word-diff=porcelain -- Project/firmware Project/Device-monitor Project/smartwater-admin Project/smartwater-database
```

Expected: all additions are comments or whitespace adjacent to comments; executable tokens are unchanged.

- [ ] **Step 5: Commit database comments**

```powershell
git add Project/smartwater-database/database/seeders/DatabaseSeeder.php Project/smartwater-database/database/migrations/2026_07_29_000000_create_maintenance_work_orders_table.php
git commit -m "docs: explain database seed and maintenance schema"
```
