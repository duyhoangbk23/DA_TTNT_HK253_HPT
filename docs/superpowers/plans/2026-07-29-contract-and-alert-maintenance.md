# Contract and Alert Maintenance Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Create scheduled maintenance work orders from active contracts and urgent work orders from abnormal telemetry alerts without deriving dates from prior maintenance records.

**Architecture:** The database project owns a `maintenance_work_orders` migration. SmartWater Admin exposes work-order management, uses a service for idempotent creation, and runs two scheduled tasks: daily contract scheduling and minute-level alert synchronization from the shared telemetry table. A completed work order may create a historical maintenance record but never moves the contract schedule.

**Tech Stack:** Laravel 13, MySQL migrations in `Project/smartwater-database`, PHPUnit.

## Global Constraints

- External `mcu_id` remains a string.
- Only `Project/smartwater-database` owns migrations.
- Due dates come only from `contracts.install_date + n * maintenance_cycle_months` while the contract is active.
- An alert work order is independent of a scheduled work order.
- No duplicate open order for the same device and source.

### Task 1: Persist maintenance work orders

**Files:**
- Create: `Project/smartwater-database/database/migrations/2026_07_29_000000_create_maintenance_work_orders_table.php`
- Create: `Project/smartwater-admin/app/Models/MaintenanceWorkOrder.php`
- Modify: `Project/smartwater-admin/app/Models/Device.php`
- Modify: `Project/smartwater-admin/app/Models/Contract.php`

- [ ] Write a failing model-relation test.
- [ ] Run `php artisan test tests/Unit/MaintenanceWorkOrderTest.php` and confirm it fails because the model does not exist.
- [ ] Create the migration and model with nullable `open_key` unique index; set `open_key` to `scheduled:{device_id}` or `alert:{device_id}:{alert}` while the order is open and null on closure.
- [ ] Run the model test again and confirm it passes.

### Task 2: Generate idempotent contract and alert orders

**Files:**
- Create: `Project/smartwater-admin/app/Services/MaintenanceWorkOrderService.php`
- Create: `Project/smartwater-admin/app/Console/Commands/GenerateScheduledMaintenanceWorkOrders.php`
- Create: `Project/smartwater-admin/app/Console/Commands/SyncTelemetryAlertWorkOrders.php`
- Modify: `Project/smartwater-admin/routes/console.php`
- Test: `Project/smartwater-admin/tests/Unit/MaintenanceWorkOrderServiceTest.php`

- [ ] Write failing tests for a contract-derived due date, an alert order, and duplicate suppression.
- [ ] Run the unit test and confirm it fails because the service does not exist.
- [ ] Implement only the tested service behavior; schedule the contract command daily and alert synchronization every minute.
- [ ] Run the unit test and confirm it passes.

### Task 3: Manage the work-order lifecycle

**Files:**
- Create: `Project/smartwater-admin/app/Http/Controllers/MaintenanceWorkOrderController.php`
- Create: `Project/smartwater-admin/app/Http/Requests/UpdateMaintenanceWorkOrderRequest.php`
- Create: `Project/smartwater-admin/resources/views/maintenance-work-orders/index.blade.php`
- Modify: `Project/smartwater-admin/routes/web.php`
- Modify: `Project/smartwater-admin/resources/views/partials/sidebar.blade.php`
- Test: `Project/smartwater-admin/tests/Feature/MaintenanceWorkOrderRouteTest.php`

- [ ] Write a failing authenticated route test.
- [ ] Run it and confirm it fails because no route exists.
- [ ] Add list, assignment and status update flow; closing an order clears its `open_key` and writes one historical record without changing the contract cadence.
- [ ] Run the feature test and confirm it passes.

### Task 4: Verify the integrated change

**Files:**
- Test: `Project/smartwater-admin/tests/Unit/MaintenanceWorkOrderServiceTest.php`
- Test: `Project/smartwater-admin/tests/Feature/MaintenanceWorkOrderRouteTest.php`

- [ ] Run the focused tests, PHP syntax checks, and `git diff --check`.
- [ ] Report only source/test evidence; do not claim browser or hardware end-to-end validation without capturing it.
