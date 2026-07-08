# 03. Database Review - smartwater-admin

Scope: database-related files only. This covers models, migrations, seeders, and ORM/database configuration.

## ORM Configuration

- Default connection: `sqlite` in `config/database.php`.
- Supported connections: `sqlite`, `mysql`, `mariadb`, `pgsql`, `sqlsrv`.
- SQLite foreign key enforcement is enabled through `foreign_key_constraints`.
- Redis is configured for cache / queue support in the same file.
- The migration repository table is `migrations`.

## Seeder Coverage

Seeders are registered in `database/seeders/DatabaseSeeder.php` in this order:

- `RoleSeeder`
- `EmployeeSeeder`
- `UserSeeder`
- `CategorySeeder`
- `ProductSeeder`
- `SupplierSeeder`
- `InventorySeeder`
- `BatchSeeder`
- `BatchDetailSeeder`
- `CustomerSeeder`
- `ContractSeeder`
- `ContractServiceSeeder`
- `McuSeeder`
- `UnusedMcuSeeder`
- `DeviceSeeder`
- `UnusedDeviceSeeder`
- `DeviceDashboardDataSeeder`
- `MaintenanceRecordSeeder`
- `ActivityLogSeeder`

These seeders indicate the project maintains sample data for all core tables plus separate seeded records for unused MCUs and devices.

## Table Catalog

### `roles`

- Purpose: Stores application role names.
- Primary key: `id`.
- Foreign keys: None.
- Constraints:
  - `name` is unique.
  - `name` length is limited to 100.
- Indexes: Unique index on `name`.
- Relationships:
  - `Role` has many `users`.
  - `Role` has many `employees`.

### `employees`

- Purpose: Stores employee records and their role assignment.
- Primary key: `id`.
- Foreign keys:
  - `role_id` references `roles.id`.
- Constraints:
  - `employee_code` is unique.
  - `email` is unique.
  - `status` is limited to `active` or `inactive`.
  - `phone`, `address`, `avatar_path`, and `hire_date` are nullable.
- Indexes: Unique indexes on `employee_code` and `email`.
- Relationships:
  - `Employee` belongs to `Role`.
  - `Employee` has one `User`.
  - `Employee` has many `MaintenanceRecord`.
  - `Employee` has many `ActivityLog` records through `user_id`.

### `users`

- Purpose: Stores login accounts for application users.
- Primary key: `id`.
- Foreign keys:
  - `role_id` references `roles.id`.
  - `employee_id` references `employees.id`.
- Constraints:
  - `username` is unique.
  - `email` is unique.
  - `status` is limited to `active` or `inactive`.
  - `email_verified_at`, `role_id`, `employee_id`, `last_login`, and `remember_token` are nullable.
- Indexes: Unique indexes on `username` and `email`.
- Relationships:
  - `User` belongs to `Role`.
  - `User` belongs to `Employee`.
  - `User` has many `ActivityLog`.

### `password_reset_tokens`

- Purpose: Stores password reset tokens.
- Primary key: `email`.
- Foreign keys: None.
- Constraints:
  - `email` is the primary key.
  - `token` is required by schema.
- Indexes: Primary key on `email`.
- Relationships: None declared in the project models.

### `sessions`

- Purpose: Stores framework session state.
- Primary key: `id`.
- Foreign keys:
  - `user_id` is indexed, but no explicit foreign key constraint is declared in the migration.
- Constraints:
  - `id` is the primary key.
  - `user_id`, `ip_address`, `user_agent` are nullable.
- Indexes:
  - Index on `user_id`.
  - Index on `last_activity`.
- Relationships: Framework-level session storage only.

### `customers`

- Purpose: Stores customer master data.
- Primary key: `id`.
- Foreign keys: None.
- Constraints:
  - `customer_code` is unique.
  - `email` is unique when present.
  - `type` is `individual` or `company`.
  - `status` is `active` or `inactive`.
  - Soft deletes are enabled.
- Indexes: Unique indexes on `customer_code` and `email`.
- Relationships:
  - `Customer` has many `devices`.
  - `Customer` has many `contracts`.

### `categories`

- Purpose: Stores product categories.
- Primary key: `id`.
- Foreign keys: None.
- Constraints:
  - `status` is `active` or `inactive`.
  - `name` is required by schema but not unique.
- Indexes: None declared.
- Relationships:
  - `Category` has many `products`.

### `products`

- Purpose: Stores product master records.
- Primary key: `id`.
- Foreign keys:
  - `category_id` references `categories.id`.
- Constraints:
  - `product_code` is unique.
  - `status` is `active`, `maintenance`, or `inactive`.
  - `capacity`, `price`, and `image_path` are nullable.
  - `unit` has a default of `Chiếc`.
- Indexes: Unique index on `product_code`.
- Relationships:
  - `Product` belongs to `Category`.
  - `Product` has one `Inventory`.
  - `Product` has many `BatchDetail`.
  - `Product` has many `Device`.

### `suppliers`

- Purpose: Stores supplier master data.
- Primary key: `id`.
- Foreign keys: None.
- Constraints: All columns are optional except `supplier_name`.
- Indexes: None declared.
- Relationships:
  - `Supplier` has many `batches`.

### `inventories`

- Purpose: Stores per-product stock information.
- Primary key: `id`.
- Foreign keys:
  - `product_id` references `products.id`.
- Constraints:
  - `product_id` is unique, so one inventory row per product.
  - `quantity` and `reserved_quantity` default to `0`.
  - `unit_cost` is nullable.
  - `last_updated` is nullable.
- Indexes: Unique index on `product_id`.
- Relationships:
  - `Inventory` belongs to `Product`.
- Notes:
  - The model exposes computed `available` and `stock_status` accessors.

### `batches`

- Purpose: Stores inbound stock batches from suppliers.
- Primary key: `id`.
- Foreign keys:
  - `supplier_id` references `suppliers.id`.
- Constraints:
  - `batch_code` is unique.
  - `supplier_id` is nullable.
  - `expiry_date`, `note` are nullable.
- Indexes: Unique index on `batch_code`.
- Relationships:
  - `Batch` belongs to `Supplier`.
  - `Batch` has many `BatchDetail`.
  - `Batch` has many `Device`.

### `batch_details`

- Purpose: Stores line items for each batch.
- Primary key: `id`.
- Foreign keys:
  - `batch_id` references `batches.id`.
  - `product_id` references `products.id`.
- Constraints:
  - `quantity` defaults to `0`.
  - `unit_cost` is nullable.
- Indexes: None declared.
- Relationships:
  - `BatchDetail` belongs to `Batch`.
  - `BatchDetail` belongs to `Product`.

### `contracts`

- Purpose: Stores customer contract records.
- Primary key: `id`.
- Foreign keys:
  - `customer_id` references `customers.id`.
- Constraints:
  - `contract_code` is unique.
  - `contract_type` is `install`, `maintenance`, or `replace`.
  - `status` is `active`, `expired`, or `cancelled`.
  - `install_date`, `maintenance_cycle_months`, and `amount` are nullable.
- Indexes: Unique index on `contract_code`.
- Relationships:
  - `Contract` belongs to `Customer`.
  - `Contract` has many `ContractService`.
  - `Contract` has many `Device`.
- Notes:
  - The model exposes a computed `expiring` accessor.

### `contract_services`

- Purpose: Stores service items attached to a contract.
- Primary key: `id`.
- Foreign keys:
  - `contract_id` references `contracts.id`.
- Constraints:
  - `contract_id` is nullable.
  - `service_interval` is nullable.
  - `description` is nullable.
- Indexes: None declared.
- Relationships:
  - `ContractService` belongs to `Contract`.

### `devices`

- Purpose: Stores installed devices and their assignment history.
- Primary key: `id`.
- Foreign keys:
  - `product_id` references `products.id`.
  - `customer_id` references `customers.id`.
  - `contract_id` references `contracts.id`.
  - `batch_id` references `batches.id`.
  - `mcu_id` references `mcus.id`.
  - `replaced_by_device_id` references `devices.id`.
- Constraints:
  - `device_code` is unique.
  - `serial_number` is unique.
  - `status` originally allowed `active`, `maintenance`, `error`, `pending`, `inactive`, then was extended to include `replaced`.
  - `customer_id`, `contract_id`, `batch_id`, `mcu_id`, `replaced_at`, and `replaced_by_device_id` are nullable.
- Indexes: Unique indexes on `device_code` and `serial_number`.
- Relationships:
  - `Device` belongs to `Product`, `Customer`, `Contract`, `Batch`, and `Mcu`.
  - `Device` belongs to another `Device` through `replaced_by_device_id`.
  - `Device` has many replacement children through `replaces()`.
  - `Device` has many `DeviceDashboardData`.
  - `Device` has many `MaintenanceRecord`.

### `device_dashboard_data`

- Purpose: Stores telemetry samples for device charts and dashboards.
- Primary key: `id`.
- Foreign keys:
  - `device_id` references `devices.id`.
- Constraints:
  - `status` is `good`, `warning`, or `bad`.
  - `recorded_at` is required.
  - `tds`, `temperature`, `water_flow`, and `ph` are nullable.
  - The table has no timestamp columns in the migration.
- Indexes:
  - Composite index on `device_id` and `recorded_at`.
- Relationships:
  - `DeviceDashboardData` belongs to `Device`.

### `maintenance_records`

- Purpose: Stores maintenance work records for devices.
- Primary key: `id`.
- Foreign keys:
  - `device_id` references `devices.id`.
  - `employee_id` references `employees.id`.
- Constraints:
  - `maintenance_code` is unique.
  - `maintenance_type` is `routine`, `repair`, or `replace`.
  - `status` is `completed` or `pending`.
  - `parts_used` and `cost` are nullable.
- Indexes:
  - Index on `maintenance_date`.
  - Unique index on `maintenance_code`.
- Relationships:
  - `MaintenanceRecord` belongs to `Device`.
  - `MaintenanceRecord` belongs to `Employee`.
- Notes:
  - The model exposes a `code` accessor mapped from `maintenance_code`.

### `activity_logs`

- Purpose: Stores user activity audit records.
- Primary key: `id`.
- Foreign keys:
  - `user_id` references `users.id`.
- Constraints:
  - `created_at` is explicitly stored.
  - `description`, `record_id`, `record_type`, and `ip_address` are nullable.
  - The migration omits `updated_at`.
- Indexes:
  - Index on `created_at`.
  - Composite index on `user_id` and `created_at`.
- Relationships:
  - `ActivityLog` belongs to `User`.

### `mcus`

- Purpose: Stores MCU hardware records used for telemetry ingestion.
- Primary key: `id`.
- Foreign keys: None.
- Constraints:
  - `mcu_code` is unique.
  - `serial_number` is unique.
  - `api_key` is unique.
  - `status` is `online`, `offline`, or `error`.
  - `firmware_version` and `last_connected_at` are nullable.
- Indexes: Unique indexes on `mcu_code`, `serial_number`, and `api_key`.
- Relationships:
  - `Mcu` has many `Device`.
  - `Mcu` exposes a `currentDevice()` helper in the model.

### `attachments`

- Purpose: Stores uploaded files linked to arbitrary records.
- Primary key: `id`.
- Foreign keys:
  - `uploaded_by` references `users.id`.
- Constraints:
  - `related_type` and `related_id` form a polymorphic target pair.
  - File metadata fields are required by schema.
- Indexes:
  - Composite index on `related_type` and `related_id`.
- Relationships:
  - `Attachment` belongs to `User` through `uploaded_by`.
  - The `related_*` pair indicates polymorphic attachment targeting.

## Relationship Summary

- `Role` -> `User`, `Employee`
- `Employee` -> `Role`, `User`, `MaintenanceRecord`, `ActivityLog`
- `User` -> `Role`, `Employee`, `ActivityLog`, `Attachment`
- `Customer` -> `Device`, `Contract`
- `Category` -> `Product`
- `Product` -> `Category`, `Inventory`, `BatchDetail`, `Device`
- `Supplier` -> `Batch`
- `Inventory` -> `Product`
- `Batch` -> `Supplier`, `BatchDetail`, `Device`
- `BatchDetail` -> `Batch`, `Product`
- `Contract` -> `Customer`, `ContractService`, `Device`
- `ContractService` -> `Contract`
- `Device` -> `Product`, `Customer`, `Contract`, `Batch`, `Mcu`, `DeviceDashboardData`, `MaintenanceRecord`, self-replacement links
- `DeviceDashboardData` -> `Device`
- `MaintenanceRecord` -> `Device`, `Employee`
- `Mcu` -> `Device`
- `ActivityLog` -> `User`
- `Attachment` -> `User`

## Primary Key Summary

- Standard auto-increment `id` primary keys: `roles`, `employees`, `users`, `customers`, `categories`, `products`, `suppliers`, `inventories`, `batches`, `batch_details`, `contracts`, `contract_services`, `devices`, `device_dashboard_data`, `maintenance_records`, `activity_logs`, `mcus`, `attachments`.
- Nonstandard primary keys:
  - `password_reset_tokens.email`
  - `sessions.id`

## Foreign Key Summary

- `employees.role_id` -> `roles.id`
- `users.role_id` -> `roles.id`
- `users.employee_id` -> `employees.id`
- `products.category_id` -> `categories.id`
- `inventories.product_id` -> `products.id`
- `batches.supplier_id` -> `suppliers.id`
- `batch_details.batch_id` -> `batches.id`
- `batch_details.product_id` -> `products.id`
- `contracts.customer_id` -> `customers.id`
- `contract_services.contract_id` -> `contracts.id`
- `devices.product_id` -> `products.id`
- `devices.customer_id` -> `customers.id`
- `devices.contract_id` -> `contracts.id`
- `devices.batch_id` -> `batches.id`
- `devices.mcu_id` -> `mcus.id`
- `devices.replaced_by_device_id` -> `devices.id`
- `device_dashboard_data.device_id` -> `devices.id`
- `maintenance_records.device_id` -> `devices.id`
- `maintenance_records.employee_id` -> `employees.id`
- `activity_logs.user_id` -> `users.id`
- `attachments.uploaded_by` -> `users.id`

## Constraints and Indexes Summary

- Unique constraints:
  - `roles.name`
  - `employees.employee_code`
  - `employees.email`
  - `users.username`
  - `users.email`
  - `customers.customer_code`
  - `customers.email`
  - `products.product_code`
  - `inventories.product_id`
  - `batches.batch_code`
  - `contracts.contract_code`
  - `devices.device_code`
  - `devices.serial_number`
  - `maintenance_records.maintenance_code`
  - `mcus.mcu_code`
  - `mcus.serial_number`
  - `mcus.api_key`
- Enum constraints:
  - `employees.status`
  - `users.status`
  - `customers.type`
  - `customers.status`
  - `categories.status`
  - `products.status`
  - `contracts.contract_type`
  - `contracts.status`
  - `devices.status`
  - `device_dashboard_data.status`
  - `maintenance_records.maintenance_type`
  - `maintenance_records.status`
  - `mcus.status`
- Other schema constraints:
  - `customers` uses soft deletes.
  - `devices.product_id`, `contracts.customer_id`, and several similar fields are nullable through their migrations.
  - `device_dashboard_data` uses a composite index on `(device_id, recorded_at)`.
  - `activity_logs` uses indexes on `created_at` and `(user_id, created_at)`.
  - `attachments` uses an index on `(related_type, related_id)`.
  - `maintenance_records` uses an index on `maintenance_date`.

