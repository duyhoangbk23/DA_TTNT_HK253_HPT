# CLAUDE.md - Project Memory

## 1. Project Summary

- Project: **SmartWater Admin**.
- Goal: Quan ly dich vu lap dat, bao tri va giam sat IoT cho may loc nuoc.
- Scope: Web admin cho san pham, kho, lo hang, khach hang, hop dong, thiet bi, telemetry, bao tri, nhan vien, activity log.
- Main app: `Project/smartwater-admin`.
- Architecture: Laravel monolith, Blade frontend + Laravel backend/API + Eloquent database.
- Tech: Laravel 13, PHP 8.3+, Blade, Bootstrap 5 CDN, Bootstrap Icons CDN, DataTables CDN, ApexCharts CDN, MySQL/SQLite via Eloquent.
- Current state: Backend foundation exists. Database migrations/seeders/models exist. Auth login exists. Product/Category/Inventory have partial CRUD/API. Several modules still read `MockData`.

## 2. Project Architecture

| Layer | Current implementation |
|---|---|
| Frontend | Blade views in `resources/views`, Bootstrap-based admin UI, shared layout/partials/components |
| Backend | Laravel Controllers, Services, Form Requests, API Resources |
| API | REST endpoints in `routes/api.php`; currently Product, Category, Inventory only |
| Database | 17 migrations and seeders aligned with ERD/MockData |
| Auth | Session-based Laravel Auth exists for web routes; RBAC/Policy not implemented |
| IoT | Telemetry schema exists; real MQTT ingestion not implemented |

Module flow:

- Web routes return Blade screens.
- Completed modules use Controller -> Service -> Eloquent Model -> DB -> Blade/API Resource.
- Incomplete modules still use Controller -> `App\Support\MockData` -> Blade.
- Dashboard uses `DashboardService` for DB-backed counts/charts, but some dashboard data mapping needs verification.

## 3. Source Structure

| Path | Purpose |
|---|---|
| `Project/smartwater-admin/app/Models` | Eloquent models and relationships |
| `Project/smartwater-admin/app/Http/Controllers` | Web and API controller actions |
| `Project/smartwater-admin/app/Http/Requests` | FormRequest validation for implemented CRUD |
| `Project/smartwater-admin/app/Http/Resources` | JSON response mapping for implemented APIs |
| `Project/smartwater-admin/app/Services` | Business/query logic for implemented modules |
| `Project/smartwater-admin/app/Support/MockData.php` | Legacy/mock data source still used by incomplete screens |
| `Project/smartwater-admin/resources/views` | Blade screens, layout, partials, components |
| `Project/smartwater-admin/routes/web.php` | Web routes; protected by `auth` except login |
| `Project/smartwater-admin/routes/api.php` | JSON APIs for products/categories/inventories |
| `Project/smartwater-admin/public/css/app.css` | Main custom admin theme |
| `Project/smartwater-admin/public/js/app.js` | Sidebar, DataTables, ApexCharts helpers |
| `Project/smartwater-admin/database/migrations` | Database schema |
| `Project/smartwater-admin/database/seeders` | Seed data matching MockData/demo screens |
| `Project/docs` | SRS and ERD image |
| `Project/firmware` | Firmware notes only; no implemented MQTT integration in Laravel yet |

Structure notes:

- Keep backend inside `Project/smartwater-admin`; do not recreate a separate backend unless explicitly requested.
- `Project/CLAUDE.md` is project memory. Do not treat it as public README.
- Improvement: gradually remove `MockData` dependencies from controllers and layout composer.

## 4. Module Overview

| Module | Purpose | Screens/routes | Model(s) | Controller |
|---|---|---|---|---|
| Auth | Login/logout and session | `/login`, `/logout` | `User`, `Role`, `Employee` | `AuthController` |
| Dashboard | KPIs, charts, recent activity/maintenance, expiring contracts | `/`, `/dashboard` | `Customer`, `Product`, `Device`, `Contract`, `MaintenanceRecord`, `ActivityLog` | `DashboardController` |
| Products | Product catalog | `/products`, API `/api/products` | `Product`, `Category` | `ProductController` |
| Categories | Product category master data | `/categories`, API `/api/categories` | `Category` | `CategoryController` |
| Inventory | Stock by product, available/stock status | `/inventory`, API `/api/inventories` | `Inventory`, `Product` | `InventoryController` |
| Batches | Import batches and batch detail | `/batches`, `/batches/{id}` | `Batch`, `BatchDetail`, `Supplier`, `Product` | `BatchController` |
| Customers | Customer list/detail with devices/contracts/maintenance | `/customers`, `/customers/{id}` | `Customer`, `Device`, `Contract`, `MaintenanceRecord` | `CustomerController` |
| Contracts | Install/maintenance/replace contracts | `/contracts` | `Contract`, `ContractService`, `Customer` | `ContractController` |
| Devices | Installed devices and detail telemetry | `/devices`, `/devices/{id}` | `Device`, `DeviceDashboardData`, `MaintenanceRecord` | `DeviceController` |
| Maintenance | Service history and technician work | shown in dashboard/customer/device detail | `MaintenanceRecord`, `Device`, `Employee` | not dedicated yet |
| Employees | Staff and role display | `/employees` | `Employee`, `Role`, `User` | `EmployeeController` |
| Activity Log | Audit trail | `/activities` | `ActivityLog`, `User` | `ActivityController` |
| Profile | Current user's profile | `/profile` | `User`, `Employee` | `ProfileController` |
| Attachments | Polymorphic file records | no screen yet | `Attachment` | not implemented |

## 5. Development Status

| Area | Status |
|---|---|
| Frontend layout/screens | Mostly complete as Blade admin UI |
| Backend app shell | Implemented in Laravel monolith |
| Database schema | Implemented: 17 migrations |
| Seed data | Implemented: roles, users, employees, products, inventory, batches, customers, contracts, devices, telemetry, maintenance, logs |
| Authentication | Basic session login/logout implemented |
| Authorization/RBAC | Not implemented beyond `auth` middleware |
| Product CRUD/API | Implemented |
| Category CRUD/API | Implemented |
| Inventory read/adjust API | Implemented |
| Dashboard DB service | Partially implemented |
| Customer/Contract/Device/Batch/Employee APIs | Not implemented |
| MQTT/IoT ingestion | Not implemented |
| Logging/audit writes | Schema/seed exists; automatic logging not implemented |
| Testing | Laravel default example tests only |
| Deployment | XAMPP/setup docs exist; production deployment not implemented |

## 6. Coding Convention

Only conventions observable in the project:

| Area | Convention |
|---|---|
| PHP style | PSR-4 namespace `App\`; 4 spaces; LF; UTF-8 from `.editorconfig` |
| Laravel structure | Models in `app/Models`, controllers in `app/Http/Controllers`, services in `app/Services` |
| Services | Use service layer for implemented business logic (`ProductService`, `CategoryService`, `InventoryService`, `DashboardService`) |
| Validation | Use FormRequest classes for implemented write/adjust actions |
| API responses | Use JsonResource classes where implemented |
| Model naming | Singular model classes; plural DB tables |
| DB code fields | `product_code`, `customer_code`, `contract_code`, `device_code`, etc. |
| Status values | Lowercase enum strings; display mapping belongs in `status-badge` or Resource/view mapping |
| Blade layout | Use `layouts.app`, `@section` for page title/subtitle/actions/breadcrumb/content |
| Components | Reuse `<x-panel>`, `<x-kpi-card>`, `<x-status-badge>` |
| Tables | Use `data-datatable`, `data-dt-search`, `data-dt-filter` with `public/js/app.js` |
| CSS | Keep custom theme variables/classes in `public/css/app.css` |
| JS | Keep global UI helper namespace as `window.SW` |
| Comments | Existing comments are short section comments; avoid verbose narration |
| Error handling | Current code uses Laravel validation errors and `findOrFail`; no custom exception layer yet |
| Logging | Laravel logging config exists; activity log automation not implemented |

## 7. Design Rules

- Do not change UI layout, sidebar, navbar, theme, or component structure unless requested.
- Backend must match the current Blade fields and status labels.
- Prefer DB/Eloquent/Service data over `MockData` when migrating a module.
- Do not hard-code new business data in views/controllers; seed or query it.
- Reuse existing Blade components and Bootstrap utility style.
- Keep DataTables/ApexCharts integration compatible with `public/js/app.js`.
- Preserve route names used by sidebar and Blade links.
- When replacing mock data, keep output shape compatible with existing Blade variables.
- Report conflicts between SRS/ERD/code instead of silently changing scope.
- Avoid adding new frameworks or frontend build dependency unless required.

## 8. Database Notes

Core tables:

- Auth/staff: `roles`, `users`, `employees`.
- Product/inventory: `categories`, `products`, `inventories`, `suppliers`, `batches`, `batch_details`.
- Customer/service: `customers`, `contracts`, `contract_services`, `devices`, `maintenance_records`.
- IoT/audit/files: `device_dashboard_data`, `activity_logs`, `attachments`.

Key relationships:

- `Role` has many `User`, `Employee`.
- `Category` has many `Product`.
- `Product` has one `Inventory`, has many `BatchDetail`, `Device`.
- `Supplier` has many `Batch`; `Batch` has many `BatchDetail`.
- `Customer` has many `Device`, `Contract`.
- `Contract` has many `ContractService`, `Device`.
- `Device` belongs to `Product`, `Customer`, `Contract`, `Batch`; has many telemetry and maintenance records.
- `MaintenanceRecord` belongs to `Device` and `Employee`.
- `ActivityLog` belongs to `User`.
- `Attachment` uses `related_type` + `related_id`.

Computed fields:

- `Inventory.available = max(quantity - reserved_quantity, 0)`.
- `Inventory.stock_status = out|low|ok`.
- `Contract.expiring` / expiring soon based on `end_date <= 30 days` and active status.

Important naming difference:

- DB/model fields use names like `product_name`, `customer_name`, `maintenance_code`.
- Legacy mock/view arrays use shorter keys like `name`, `code`, `customer`.
- Resources/services should map DB fields to frontend-friendly keys where needed.

## 9. Important Decisions

- Main application is a unified Laravel monolith at `Project/smartwater-admin`.
- Use Laravel session auth for web routes.
- Product/Category/Inventory are the first backend-backed modules.
- Maintain existing Blade UI as the official frontend surface.
- Keep seed data aligned with original MockData so current screens remain populated.
- Use MySQL/MariaDB for target DB; SQLite exists for local/dev convenience.
- Use Bootstrap 5/DataTables/ApexCharts from CDN in layout.
- Use Service Layer for business logic instead of putting all logic in controllers.
- Use FormRequest for validation and API Resource for JSON formatting.
- IoT telemetry is stored in `device_dashboard_data`; real MQTT ingestion remains future work.

## 10. Known Limitations

- Several controllers still use `MockData`: `ActivityController`, `BatchController`, `ContractController`, `CustomerController`, `DeviceController`, `EmployeeController`, `ProfileController`, and layout composer notifications/currentUser.
- `ProductController`, `CategoryController`, `InventoryController`, `DashboardController`, and `AuthController` are DB-backed or partially DB-backed.
- `ProfileController` does not update user/profile/password/avatar yet.
- No Policies or role-based permission middleware.
- No API auth; API endpoints are public under `routes/api.php`.
- Product/category/inventory API lacks pagination/filter/sorting despite docs mentioning future support.
- Customer/Contract/Device/Batch/Employee/Maintenance/Activity APIs are not implemented.
- Activity logs are seeded but not automatically written on business actions.
- MQTT/device telemetry ingestion is not implemented.
- File upload/attachment UI and controller are not implemented.
- Some Vietnamese text in source/docs appears mojibake in terminal output; verify file encoding before editing text-heavy content.
- `README.md` still describes UI-only state in places and may be stale compared with SRS/backend docs.

## 11. Development Priorities

1. Stabilize authentication and replace layout composer mock current user/notifications.
2. Add authorization: roles `Administrator`, `Employee`, `Technician`, middleware/policies.
3. Finish replacing `MockData` module by module: Customer, Device, Contract, Batch, Employee, Activity, Profile.
4. Implement CRUD/API for Customer, Contract, Device, Batch, Maintenance, Employee, Activity.
5. Add search/filter/pagination to existing APIs without breaking Blade.
6. Add automatic activity logging for create/update/delete/login/logout.
7. Implement telemetry query API and real device dashboard data flow.
8. Add tests for services, validation, auth, and route behavior.
9. Add deployment hardening only after core CRUD is stable.

## 12. Instructions for Future Agents

- Read this file before making changes.
- Treat `Project/smartwater-admin` as the main Laravel app.
- Read the relevant controller/service/model/view before editing a module.
- Do not scan or refactor unrelated code unless the task requires it.
- Do not change the UI design or route names unless requested.
- Prefer existing Service/FormRequest/Resource patterns for new backend work.
- When migrating a mock-backed screen, keep the existing Blade variable shape or update the view minimally.
- Check `Project/docs/SRS.md`, `Project/docs/ERD.png`, `DATABASE_SCHEMA.md`, and `API_SPECIFICATION.md` for domain/schema/API context.
- If source code conflicts with docs, state the conflict and follow current UI for fields/layout, ERD/schema for persisted data, and SRS for business scope.
- Run focused checks after edits: syntax for touched PHP files, `php artisan test` when feasible, and `git diff --check`.
