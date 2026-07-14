# Database Schema - SmartWater Admin

## Entity Relationship Overview

```
Roles
  ├── has_many: Users
  └── has_many: Employees

Categories
  └── has_many: Products

Suppliers
  └── has_many: Batches

Products
  ├── belongs_to: Category
  ├── has_one: Inventory
  ├── has_many: BatchDetails
  └── has_many: Devices

Batches
  ├── belongs_to: Supplier
  └── has_many: BatchDetails (pivot with quantity)

BatchDetails
  ├── belongs_to: Batch
  └── belongs_to: Product

Customers
  ├── has_many: Devices
  └── has_many: Contracts

Contracts
  ├── belongs_to: Customer
  └── has_many: ContractServices

ContractServices
  (Reference table, static services)

Devices
  ├── belongs_to: Product
  ├── belongs_to: Customer
  ├── belongs_to: Batch
  ├── belongs_to: Contract
  └── has_many: DeviceDashboardData
  └── has_many: MaintenanceRecords

DeviceDashboardData
  └── belongs_to: Device

MaintenanceRecords
  ├── belongs_to: Device
  ├── belongs_to: Employee
  └── polymorphic: Attachments

Users
  ├── belongs_to: Role
  ├── belongs_to: Employee
  └── has_many: ActivityLogs
  └── polymorphic: Attachments

Employees
  ├── belongs_to: Role
  └── has_many: MaintenanceRecords

ActivityLogs
  └── belongs_to: User

Attachments
  (Polymorphic: belongs_to users, devices, etc.)
```

## Table Details

### Roles Table
```
id (pk)
name (string, 100)
  Values: 'Administrator', 'Employee', 'Technician'
created_at, updated_at
```

### Users Table
```
id (pk)
role_id (fk → roles)
employee_id (fk → employees, nullable)
username (string, 100)
email (string, 100)
password (string, hashed)
status (enum: 'active'|'inactive')
last_login (timestamp, nullable)
created_at, updated_at
```

### Employees Table
```
id (pk)
role_id (fk → roles)
name (string, 100)
avatar_path (string, nullable)
email (string, 100, nullable)
phone (string, 20, nullable)
position (string, 100)
hire_date (date)
status (enum: 'active'|'inactive')
created_at, updated_at
```

### Categories Table
```
id (pk)
name (string, 100, unique)
description (text, nullable)
status (enum: 'active'|'inactive')
created_at, updated_at
```

### Products Table
```
id (pk)
product_code (string, 50, unique)
product_name (string, 150)
category_id (fk → categories)
model (string, 100)
capacity (string, 50, nullable)
unit (string, 20, default: 'Chiếc')
price (unsignedBigInteger, nullable)
  VND pricing, e.g., 3,200,000
status (enum: 'active'|'maintenance'|'inactive')
image_path (string, nullable)
created_at, updated_at
```

### Suppliers Table
```
id (pk)
supplier_name (string, 150)
contact_person (string, 100, nullable)
phone (string, 20, nullable)
email (string, 100, nullable)
address (text, nullable)
created_at, updated_at
```

### Inventories Table
```
id (pk)
product_id (fk → products, unique, one-to-one)
quantity (integer)
reserved_quantity (integer)
unit_cost (unsignedBigInteger)
last_updated (timestamp, nullable)
created_at, updated_at

Computed (not stored):
  available = max(quantity - reserved_quantity, 0)
  stock_status = quantity == 0 ? 'out' : (quantity <= 10 ? 'low' : 'ok')
```

### Batches Table
```
id (pk)
batch_code (string, 50, unique)
supplier_id (fk → suppliers)
import_date (date)
expiry_date (date, nullable)
quantity (integer)
note (text, nullable)
created_at, updated_at
```

### BatchDetails Table
```
id (pk)
batch_id (fk → batches)
product_id (fk → products)
quantity (integer)
unit_cost (unsignedBigInteger)
created_at, updated_at
```

### Customers Table
```
id (pk)
customer_code (string, 50, unique)
customer_name (string, 150)
avatar_path (string, nullable)
phone (string, 20, nullable)
email (string, 100, nullable)
address (text, nullable)
type (enum: 'individual'|'company')
status (enum: 'active'|'inactive')
joined_at (date)
deleted_at (timestamp, nullable)  ← soft deletes
created_at, updated_at
```

### Contracts Table
```
id (pk)
contract_code (string, 50, unique)
customer_id (fk → customers)
contract_type (enum: 'install'|'maintenance'|'replace')
start_date (date)
install_date (date, nullable)
end_date (date)
maintenance_cycle_months (integer, nullable)
amount (unsignedBigInteger)
status (enum: 'active'|'expired'|'cancelled')
created_at, updated_at

Computed (not stored):
  expiring_soon = end_date is within 30 days from now
```

### ContractServices Table
```
id (pk)
contract_id (fk → contracts, nullable)
service_name (string, 150)
service_interval (unsignedSmallInteger, nullable)
  Days between services, e.g., 90, 180, 365
description (text, nullable)
created_at, updated_at
```

### Devices Table
```
id (pk)
device_code (string, 50, unique)
serial_number (string, 50, unique)
product_id (fk → products, nullable)
customer_id (fk → customers, nullable)
contract_id (fk → contracts, nullable)
batch_id (fk → batches, nullable)
firmware_version (string, 50, nullable)
location (string, 255, nullable)
status (enum: 'active'|'maintenance'|'error'|'pending'|'inactive')
created_at, updated_at
```

### DeviceDashboardData Table
```
id (pk)
device_id (fk → devices)
recorded_at (timestamp)
tds (decimal: 8,2)
  Total Dissolved Solids, ppm
temperature (decimal: 5,2)
  Celsius
water_flow (decimal: 8,2)
  Liters per hour
ph (decimal: 4,2)
  pH value, divided by 10 for display
status (enum: 'normal'|'warning'|'error')
composite_index: (device_id, recorded_at)
```

### MaintenanceRecords Table
```
id (pk)
maintenance_code (string, 50, unique)
device_id (fk → devices)
employee_id (fk → employees)
maintenance_date (date)
maintenance_type (enum: 'routine'|'repair'|'replace')
description (text, nullable)
parts_used (text, nullable)
cost (unsignedBigInteger)
status (enum: 'completed'|'pending')
index: (maintenance_date)
created_at, updated_at
```

### ActivityLogs Table
```
id (pk)
user_id (fk → users)
action (string, 150)
  e.g., 'Đăng nhập hệ thống', 'Tạo hợp đồng mới'
module (string, 100)
  e.g., 'Auth', 'Hợp đồng', 'Khách hàng'
record_id (unsignedBigInteger, nullable)
  ID of affected record
record_type (string, 100, nullable)
  e.g., 'Product', 'Customer'
description (text, nullable)
ip_address (string, 45, nullable)
  IPv4/IPv6
index: (created_at), (user_id, created_at)
created_at
```

### Attachments Table
```
id (pk)
file_name (string, 255)
file_path (string, 255)
mime_type (string, 100)
file_size (unsignedBigInteger)
uploaded_by (fk → users)
related_type (string, 100)
  Polymorphic type: 'Device', 'MaintenanceRecord', 'Customer', etc.
related_id (unsignedBigInteger)
  ID of the related model
created_at, updated_at
```

## Indexes

### Performance Indexes
```
products:           product_code (unique), category_id
inventories:        product_id (unique)
batches:            batch_code (unique), supplier_id
customers:          customer_code (unique)
contracts:          contract_code (unique), customer_id, status
devices:            device_code (unique), serial_number (unique), customer_id
maintenance_records: device_id, maintenance_date
activity_logs:      user_id, created_at
batch_details:      batch_id, product_id
```

## Data Types

- **Prices/Costs**: `unsignedBigInteger` (VND, no decimals)
- **Measurements**: `decimal(8,2)` for telemetry (TDS, flow, etc.)
- **Dates**: `date` for simple dates, `timestamp` for audit trails
- **Booleans**: `enum` for clarity (active|inactive)
- **Text**: `text` for descriptions, `string` for codes/names

## Seeded Data

Current seed data matches MockData structure:
- 3 Roles
- 3 Users
- 9 Employees
- 4 Categories
- 10 Products (codes: AQ-RO-50 to AQ-CORE-RO)
- 3 Suppliers
- 10 Inventories
- 6 Batches (LOT-2025-001 to 006)
- 18 Contracts
- 3 ContractServices (static reference services)
- 24 Customers (individual + company)
- 30 Devices (TB-01000 to TB-01029)
- 24 DeviceDashboardData (telemetry readings)
- 20 MaintenanceRecords (BT-0001 to BT-0020)
- 25 ActivityLogs
