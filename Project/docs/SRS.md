# SmartWater Admin — Software Requirements Specification (SRS)

**Version**: 2.0 (Updated with Backend Implementation)  
**Date**: July 5, 2026  
**Status**: Phase 1 & 2 Complete ✅

---

## 1. Tổng Quan Dự Án

### 1.1 Mô Tả

**SmartWater Admin** là hệ thống quản lý dịch vụ bảo trì máy lọc nước toàn diện. Hệ thống cho phép:
- Quản lý sản phẩm (máy lọc nước, lõi lọc, phụ kiện)
- Quản lý kho hàng và nhập hàng
- Quản lý khách hàng và hợp đồng bảo trì
- Theo dõi thiết bị và cảm biến (IoT)
- Quản lý nhân viên và lịch bảo trì
- Lịch sử hoạt động hệ thống (audit logs)

### 1.2 Mục Tiêu

- ✅ Thay thế MockData bằng database thật
- ✅ Xây dựng backend API (REST)
- ✅ Tích hợp backend + frontend trong cùng một Laravel app
- ✅ Hỗ trợ MySQL database
- ✅ Tạo tài liệu API và database schema

### 1.3 Scope

**Phase 1 (Complete)**: Foundation
- 17 Eloquent Models
- 17 Database Migrations
- 16 Database Seeders
- Mock data seeding

**Phase 2 (Complete)**: CRUD + API
- 3 Controllers (Product, Category, Inventory)
- 5 FormRequests (validation)
- 3 API Resources (JSON responses)
- 3 Services (business logic)
- REST API endpoints
- Documentation

**Phase 3 (Future)**: Extended Features
- Authentication & Authorization
- Dashboard with real data
- Maintenance scheduling
- Device telemetry dashboard
- Report generation

---

## 2. Yêu Cầu Chức Năng

### 2.1 Quản Lý Sản Phẩm (Products)

| Chức Năng | Trạng Thái | API | Web |
|-----------|----------|-----|-----|
| Xem danh sách sản phẩm | ✅ | `GET /api/products` | `GET /products` |
| Xem chi tiết sản phẩm | ✅ | `GET /api/products/{id}` | `GET /products/{id}` |
| Tạo sản phẩm mới | ✅ | `POST /api/products` | `POST /products` |
| Cập nhật sản phẩm | ✅ | `PUT /api/products/{id}` | `PUT /products/{id}` |
| Xóa sản phẩm | ✅ | `DELETE /api/products/{id}` | `DELETE /products/{id}` |
| Lọc theo danh mục | 🔄 | Có schema, cần implement filter | - |
| Tìm kiếm sản phẩm | 🔄 | Có schema, cần implement search | - |

### 2.2 Quản Lý Danh Mục (Categories)

| Chức Năng | Trạng Thái | API | Web |
|-----------|----------|-----|-----|
| Xem danh sách danh mục | ✅ | `GET /api/categories` | `GET /categories` |
| Xem chi tiết danh mục | ✅ | `GET /api/categories/{id}` | `GET /categories/{id}` |
| Tạo danh mục | ✅ | `POST /api/categories` | `POST /categories` |
| Cập nhật danh mục | ✅ | `PUT /api/categories/{id}` | `PUT /categories/{id}` |
| Xóa danh mục | ✅ | `DELETE /api/categories/{id}` | `DELETE /categories/{id}` |

### 2.3 Quản Lý Tồn Kho (Inventories)

| Chức Năng | Trạng Thái | API | Web |
|-----------|----------|-----|-----|
| Xem danh sách tồn kho | ✅ | `GET /api/inventories` | `GET /inventory` |
| Xem chi tiết tồn kho | ✅ | `GET /api/inventories/{id}` | `GET /inventory/{id}` |
| Điều chỉnh số lượng | ✅ | `PATCH /api/inventories/{id}/adjust` | `PATCH /inventory/{id}` |
| Tính toán hàng có sẵn | ✅ | Computed: `available = qty - reserved` | Real-time |
| Tính toán trạng thái hàng | ✅ | Computed: `out\|low\|ok` | Real-time |

**Computed Fields (không lưu trữ):**
- `available` = `quantity - reserved_quantity`
- `stock_status` = `'out'` (qty=0) \| `'low'` (qty≤10) \| `'ok'`

### 2.4 Quản Lý Khách Hàng (Customers)

| Chức Năng | Trạng Thái | 
|-----------|----------|
| Xem danh sách khách hàng | ✅ |
| Xem chi tiết khách hàng | ✅ |
| Tạo khách hàng | 🔄 |
| Cập nhật khách hàng | 🔄 |
| Xóa khách hàng (soft delete) | 🔄 |

### 2.5 Quản Lý Thiết Bị (Devices)

| Chức Năng | Trạng Thái |
|-----------|----------|
| Xem danh sách thiết bị | ✅ |
| Xem chi tiết thiết bị | ✅ |
| Theo dõi cảm biến IoT | ✅ (Database schema ready) |
| Biểu đồ dữ liệu telemetry | 🔄 (Need frontend chart) |

### 2.6 Quản Lý Hợp Đồng (Contracts)

| Chức Năng | Trạng Thái |
|-----------|----------|
| Xem danh sách hợp đồng | ✅ |
| Tính hợp đồng sắp hết hạn | ✅ (Computed attribute) |
| Tạo hợp đồng | 🔄 |
| Quản lý dịch vụ bảo trì | ✅ (Schema ready) |

### 2.7 Quản Lý Bảo Trì (Maintenance)

| Chức Năng | Trạng Thái |
|-----------|----------|
| Ghi nhận phiếu bảo trì | ✅ (Schema ready) |
| Gán kỹ thuật viên | ✅ (Foreign key) |
| Theo dõi chi phí | ✅ (Schema ready) |

### 2.8 Audit Log (Activity Log)

| Chức Năng | Trạng Thái |
|-----------|----------|
| Ghi lại hoạt động người dùng | ✅ |
| Lịch sử thay đổi dữ liệu | ✅ |
| IP address tracking | ✅ |

---

## 3. Kiến Trúc Hệ Thống

### 3.1 Architecture Pattern

```
Integrated Monolith (Unified Laravel Application)
├── Frontend (Blade Templates + Bootstrap UI)
├── Backend (Controllers + Services)
├── API (REST endpoints)
└── Database (MySQL via Eloquent ORM)
```

### 3.2 Technology Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 13 |
| Backend | PHP 8.5+ |
| Frontend | Blade Templates |
| CSS | Bootstrap 5 (CDN) |
| Database | MySQL 5.7+ / MariaDB |
| ORM | Eloquent |
| API | REST (JSON) |

### 3.3 Project Structure

```
Project/smartwater-admin/
├── app/
│   ├── Models/              # 17 Eloquent models
│   │   ├── Role.php
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Inventory.php
│   │   ├── Customer.php
│   │   ├── Device.php
│   │   ├── MaintenanceRecord.php
│   │   └── ... (10 more)
│   ├── Http/
│   │   ├── Controllers/     # CRUD controllers
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── InventoryController.php
│   │   │   └── ... (others)
│   │   ├── Requests/        # Form validation
│   │   │   ├── StoreProductRequest.php
│   │   │   ├── UpdateProductRequest.php
│   │   │   └── ... (5 files)
│   │   └── Resources/       # API responses
│   │       ├── ProductResource.php
│   │       ├── CategoryResource.php
│   │       └── InventoryResource.php
│   └── Services/            # Business logic
│       ├── ProductService.php
│       ├── CategoryService.php
│       └── InventoryService.php
├── database/
│   ├── migrations/          # 17 table definitions
│   └── seeders/             # 16 data populators
├── routes/
│   ├── web.php              # Web routes (Blade)
│   └── api.php              # API routes (JSON)
└── resources/views/         # Blade templates
```

---

## 4. Database Schema

### 4.1 Core Tables (17 tables)

| Table | Records | Purpose |
|-------|---------|---------|
| `roles` | 3 | Administrator, Employee, Technician |
| `users` | 3 | System accounts with passwords |
| `employees` | 9 | Staff members with roles |
| `categories` | 4 | Product categories (RO, Nano, etc.) |
| `products` | 10 | Water filters and components |
| `suppliers` | 3 | Equipment suppliers |
| `inventories` | 10 | Stock tracking (one per product) |
| `batches` | 6 | Import batches (LOT-2025-001, etc.) |
| `batch_details` | 30 | Products in each batch (many-to-many) |
| `customers` | 24 | Individual + company customers |
| `contracts` | 18 | Service contracts |
| `contract_services` | 3 | Service types (static reference) |
| `devices` | 30 | Water filter devices (TB-01000, etc.) |
| `device_dashboard_data` | 24+ | IoT telemetry (TDS, temperature, pH, flow) |
| `maintenance_records` | 20 | Service history (BT-0001, etc.) |
| `activity_logs` | 25 | Audit trail |
| `attachments` | 0 | Polymorphic file storage |

### 4.2 Key Relationships

```
Role → User, Employee
Category → Product
Supplier → Batch
Product → Inventory, BatchDetail, Device
Batch → BatchDetail
Customer → Device, Contract
Contract → ContractService (nullable)
Device → DeviceDashboardData, MaintenanceRecord
Employee → MaintenanceRecord
User → ActivityLog
```

### 4.3 Computed Attributes (Not Persisted)

| Model | Attribute | Formula |
|-------|-----------|---------|
| Inventory | `available` | `quantity - reserved_quantity` |
| Inventory | `stock_status` | `out\|low\|ok` based on qty |
| Contract | `expiring_soon` | `end_date` within 30 days |

---

## 5. API Specification

### 5.1 Base URL
```
http://localhost:8000/api
```

### 5.2 Endpoints

**Products API**
```
GET    /api/products              # List all
POST   /api/products              # Create
GET    /api/products/{id}         # Show
PUT    /api/products/{id}         # Update
DELETE /api/products/{id}         # Delete
```

**Categories API**
```
GET    /api/categories            # List all
POST   /api/categories            # Create
GET    /api/categories/{id}       # Show
PUT    /api/categories/{id}       # Update
DELETE /api/categories/{id}       # Delete
```

**Inventories API**
```
GET    /api/inventories           # List all
GET    /api/inventories/{id}      # Show
PATCH  /api/inventories/{id}/adjust # Update quantities
```

### 5.3 Response Format

**Success (200)**
```json
{
  "id": 1,
  "code": "AQ-RO-50",
  "name": "AquaPure RO 50",
  "price": 3200000,
  "status": "active"
}
```

**Error (422)**
```json
{
  "message": "The given data was invalid",
  "errors": {
    "code": ["The code must be unique"]
  }
}
```

---

## 6. Implementation Status

### 6.1 Completed (✅)

**Foundation Phase**
- [x] 17 Eloquent Models with relationships
- [x] 17 Database Migrations with FK constraints
- [x] 16 Database Seeders (exact MockData replication)
- [x] MySQL database setup and configuration
- [x] Migration order fixed (roles → employees → users)

**CRUD & API Phase**
- [x] ProductController (index, show, store, update, destroy)
- [x] CategoryController (full CRUD)
- [x] InventoryController (index, show, adjust)
- [x] StoreProductRequest, UpdateProductRequest validation
- [x] StoreCategoryRequest, UpdateCategoryRequest validation
- [x] AdjustInventoryRequest validation
- [x] ProductResource (JSON response)
- [x] CategoryResource (JSON response)
- [x] InventoryResource with computed fields
- [x] ProductService, CategoryService, InventoryService
- [x] REST API routes (api.php)
- [x] Web routes updated (web.php)

**Documentation**
- [x] BACKEND_SETUP.md (installation guide)
- [x] DATABASE_SCHEMA.md (entity relationships)
- [x] API_SPECIFICATION.md (endpoint examples)

### 6.2 In Progress (🔄)

**Frontend Integration**
- [ ] Update Product views to use DB instead of MockData
- [ ] Update Inventory views to use DB instead of MockData
- [ ] Update Dashboard to show real KPIs from DB
- [ ] Update Customer views
- [ ] Update Device views

**Extended Features**
- [ ] Customer CRUD endpoints
- [ ] Device CRUD endpoints
- [ ] Maintenance Record management
- [ ] Contract management
- [ ] Batch management
- [ ] Search & filtering
- [ ] Pagination

### 6.3 Future (⏳)

**Authentication & Authorization**
- [ ] Login with password verification
- [ ] Role-based access control (RBAC)
- [ ] Authentication middleware
- [ ] JWT tokens (optional)

**Advanced Features**
- [ ] Device telemetry dashboard (real charts)
- [ ] Maintenance scheduling
- [ ] Automated alerts
- [ ] PDF report generation
- [ ] Data export (CSV, Excel)
- [ ] Multi-language support

**DevOps**
- [ ] Docker containerization
- [ ] CI/CD pipeline
- [ ] Production deployment guide
- [ ] Database backup strategy
- [ ] Performance optimization

---

## 7. Installation & Setup

### 7.1 Prerequisites
- PHP 8.2+
- MySQL 5.7+ (or MariaDB)
- Composer 2.x
- XAMPP (development)

### 7.2 Quick Start
```bash
cd Project/smartwater-admin

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env
# DB_HOST=127.0.0.1
# DB_DATABASE=smartwater_admin
# DB_USERNAME=root

# Create database and seed
php artisan migrate:fresh --seed

# Start server
php artisan serve
```

Server runs at: `http://localhost:8000`

### 7.3 Database

**Configuration**: `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartwater_admin
DB_USERNAME=root
DB_PASSWORD=
```

**Seed Data**
- 3 Roles
- 3 Users (admin/employee/technician)
- 9 Employees
- 4 Categories
- 10 Products
- 24 Customers
- 30 Devices
- 18 Contracts
- 20 Maintenance Records
- 25 Activity Logs
- 100+ other records

---

## 8. Testing

### 8.1 API Testing

**Using cURL**
```bash
# List products
curl http://localhost:8000/api/products

# Get product by ID
curl http://localhost:8000/api/products/1

# Create product
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{"code":"TEST","name":"Test","category_id":1,"price":5000000,"status":"active"}'
```

**Using Tinker**
```bash
php artisan tinker
```
```php
Product::count()              # 10
Category::count()             # 4
Inventory::count()            # 10
Product::with('category')->first()
```

### 8.2 Database Verification

```sql
SELECT COUNT(*) FROM products;       -- 10
SELECT COUNT(*) FROM categories;     -- 4
SELECT COUNT(*) FROM inventories;    -- 10
SELECT COUNT(*) FROM customers;      -- 24
SELECT COUNT(*) FROM devices;        -- 30
```

---

## 9. Known Issues & Limitations

### 9.1 Current Limitations

| Issue | Impact | Workaround |
|-------|--------|-----------|
| No authentication | All endpoints public | Add Sanctum in Phase 3 |
| No pagination | All results returned | Limit to first 100 records or implement pagination |
| No search/filter | Can't query by specific fields | Add query params in Phase 3 |
| Frontend still uses MockData | Inconsistent data | Update views in Phase 3 |
| No file upload | Can't attach images | Manual image path input only |

### 9.2 Future Improvements

- [ ] Add caching layer (Redis)
- [ ] Implement rate limiting
- [ ] Add request logging/monitoring
- [ ] Optimize N+1 query problems
- [ ] Add soft deletes for data safety
- [ ] Implement queue for long operations
- [ ] Add webhook notifications
- [ ] GraphQL alternative API

---

## 10. Deployment Checklist

### 10.1 Pre-Deployment

- [ ] Verify all migrations run on production DB
- [ ] Test API endpoints in production environment
- [ ] Configure environment variables (.env)
- [ ] Set up database backups
- [ ] Enable HTTPS/SSL
- [ ] Configure CORS headers
- [ ] Set up monitoring/logging
- [ ] Performance testing (load testing)

### 10.2 Production Deployment

- [ ] Use MySQL (not SQLite)
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Use strong `APP_KEY`
- [ ] Configure database credentials securely
- [ ] Enable query optimization
- [ ] Set up error tracking (Sentry)
- [ ] Configure email for notifications

---

## 11. References

- **Laravel Documentation**: https://laravel.com/docs/13
- **Database Schema**: See `DATABASE_SCHEMA.md`
- **API Documentation**: See `API_SPECIFICATION.md`
- **Setup Guide**: See `BACKEND_SETUP.md`
- **Git Repository**: Project branch `frontend_backend`

---

## 12. Version History

| Version | Date | Changes |
|---------|------|---------|
| 2.0 | 2026-07-05 | Complete Phase 1 & 2, MySQL setup, SRS update |
| 1.0 | 2026-06-29 | Initial UI-only frontend, MockData only |

---

**Last Updated**: July 5, 2026  
**Status**: Phase 1 & 2 ✅ Complete | Phase 3 🔄 In Planning
