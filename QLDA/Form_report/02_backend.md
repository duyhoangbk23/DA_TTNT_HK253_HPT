# 02. Backend Implementation Review - smartwater-admin

Scope: backend implementation only. Frontend is ignored.

## Backend Inventory

- Controllers exist under `app/Http/Controllers`.
- Services exist under `app/Services`.
- Helper/support code exists under `app/Support`.
- `app/Http/Middleware`, `app/Repositories`, and `app/Helpers` are not present in this repository.
- The backend is wired through `routes/web.php`, `routes/api.php`, `bootstrap/app.php`, and `app/Providers/AppServiceProvider.php`.

## Controllers

### `App\Http\Controllers\Controller`

- Responsibility: Base controller type for all application controllers.
- Public methods: None.
- Dependencies: Laravel controller base structure only.
- Request flow: Inherited by all concrete controllers.
- Response flow: No direct response handling.

### `AuthController`

- Responsibility: Login and logout flow.
- Public methods:
  - `showLoginForm()`
  - `login(Request $request)`
  - `logout(Request $request)`
- Dependencies: `Illuminate\Support\Facades\Auth`, `Illuminate\Http\Request`.
- Request flow:
  - `showLoginForm()` checks the current auth state.
  - `login()` validates `email` and `password`, then attempts authentication.
  - `logout()` invalidates the session after sign-out.
- Response flow:
  - Authenticated users are redirected to `/dashboard`.
  - Login success redirects to the intended page or `/dashboard`.
  - Login failure returns back with validation/auth errors.
  - Logout redirects to `/` with a flash message.

### `DashboardController`

- Responsibility: Assemble dashboard data for the main overview page.
- Public methods:
  - `index()`
- Dependencies: `App\Services\DashboardService`.
- Request flow: Receives a web `GET` request for `/` or `/dashboard`.
- Response flow: Returns `dashboard.index` with KPI, chart, activity, maintenance, and expiring-contract data.

### `ProductController`

- Responsibility: CRUD entry point for products plus JSON API variants.
- Public methods:
  - `index()`
  - `show($id)`
  - `store(StoreProductRequest $request)`
  - `update(UpdateProductRequest $request, $id)`
  - `destroy($id)`
  - `apiIndex()`
  - `apiShow($id)`
  - `apiStore(StoreProductRequest $request)`
  - `apiUpdate(UpdateProductRequest $request, $id)`
  - `apiDestroy($id)`
- Dependencies:
  - `App\Services\ProductService`
  - `App\Models\Category`
  - `App\Http\Requests\StoreProductRequest`
  - `App\Http\Requests\UpdateProductRequest`
  - `App\Http\Resources\ProductResource`
- Request flow:
  - Web routes call the HTML methods.
  - API routes call the JSON methods.
  - Store/update methods receive validated input from form requests.
  - List/show methods delegate read access to the service layer.
- Response flow:
  - Web methods return Blade views or redirects with flash messages.
  - API methods return JSON resources, with `201` on create and a deletion message on delete.

### `CategoryController`

- Responsibility: CRUD entry point for categories plus JSON API variants.
- Public methods:
  - `index()`
  - `show($id)`
  - `store(StoreCategoryRequest $request)`
  - `update(UpdateCategoryRequest $request, $id)`
  - `destroy($id)`
  - `apiIndex()`
  - `apiShow($id)`
  - `apiStore(StoreCategoryRequest $request)`
  - `apiUpdate(UpdateCategoryRequest $request, $id)`
  - `apiDestroy($id)`
- Dependencies:
  - `App\Services\CategoryService`
  - `App\Http\Requests\StoreCategoryRequest`
  - `App\Http\Requests\UpdateCategoryRequest`
  - `App\Http\Resources\CategoryResource`
- Request flow:
  - Web and API routes share the same service methods.
  - Validation is handled before create/update actions.
- Response flow:
  - Web methods return Blade views or redirects.
  - API methods return JSON resources and status codes.

### `InventoryController`

- Responsibility: Inventory listing, detail, and adjustment flows for web and API.
- Public methods:
  - `index()`
  - `show($id)`
  - `adjust(AdjustInventoryRequest $request, $id)`
  - `apiIndex()`
  - `apiShow($id)`
  - `apiAdjust(AdjustInventoryRequest $request, $id)`
- Dependencies:
  - `App\Services\InventoryService`
  - `App\Http\Requests\AdjustInventoryRequest`
  - `App\Http\Resources\InventoryResource`
  - `App\Models\Inventory` is imported but not used directly in the controller body.
- Request flow:
  - Web and API requests both go through the service layer.
  - Adjustment requests are validated before service execution.
- Response flow:
  - Web index formats inventory data for `inventory.index`.
  - Web adjust redirects back with a flash message.
  - API methods return JSON resources.

### `BatchController`

- Responsibility: Batch CRUD flow for web views.
- Public methods:
  - `index()`
  - `show(int $id)`
  - `store(StoreBatchRequest $request)`
  - `update(UpdateBatchRequest $request, $id)`
  - `destroy($id)`
- Dependencies:
  - `App\Services\BatchService`
  - `App\Models\Supplier`
  - `App\Http\Requests\StoreBatchRequest`
  - `App\Http\Requests\UpdateBatchRequest`
- Request flow:
  - List and detail actions read from the service layer.
  - Create/update actions consume validated form request data.
- Response flow:
  - Returns `batch.index` or `batch.show` for reads.
  - Create/update/delete return redirects to `batches.index` with flash messages.

### `CustomerController`

- Responsibility: Customer CRUD flow for web views.
- Public methods:
  - `index()`
  - `show(int $id)`
  - `store(StoreCustomerRequest $request)`
  - `update(UpdateCustomerRequest $request, $id)`
  - `destroy($id)`
- Dependencies:
  - `App\Services\CustomerService`
  - `App\Models\Customer`
  - `App\Http\Requests\StoreCustomerRequest`
  - `App\Http\Requests\UpdateCustomerRequest`
- Request flow:
  - Reads are served from the service layer.
  - Create/update actions are validated before write.
- Response flow:
  - Returns `customers.index` or `customers.show` for reads.
  - Write actions redirect to `customers.index`.

### `ContractController`

- Responsibility: Contract CRUD flow and contract-device linkage overview.
- Public methods:
  - `index()`
  - `show(int $id)`
  - `store(StoreContractRequest $request)`
  - `update(UpdateContractRequest $request, $id)`
  - `destroy($id)`
- Dependencies:
  - `App\Services\ContractService`
  - `App\Models\Customer`
  - `App\Models\Device`
  - `App\Models\Mcu`
  - `App\Http\Requests\StoreContractRequest`
  - `App\Http\Requests\UpdateContractRequest`
- Request flow:
  - Reads use the service layer.
  - Index builds supporting lists of customers, unused devices, and unused MCUs directly from models.
  - Create/update actions use validated request data.
- Response flow:
  - Returns `contracts.index` or `contracts.show`.
  - Write actions redirect to `contracts.index`.

### `DeviceController`

- Responsibility: Device CRUD, replacement, and detail view with telemetry/maintenance context.
- Public methods:
  - `index()`
  - `show(int $id)`
  - `store(StoreDeviceRequest $request)`
  - `update(UpdateDeviceRequest $request, $id)`
  - `destroy($id)`
  - `replace(ReplaceDeviceRequest $request, $id)`
- Dependencies:
  - `App\Services\DeviceService`
  - `App\Services\McuService` via `app(...)` in `show()`
  - `App\Models\Product`
  - `App\Models\Customer`
  - `App\Models\Contract`
  - `App\Models\Batch`
  - `App\Models\Mcu`
  - `App\Models\Device`
  - `App\Models\DeviceDashboardData`
  - `App\Http\Requests\StoreDeviceRequest`
  - `App\Http\Requests\UpdateDeviceRequest`
  - `App\Http\Requests\ReplaceDeviceRequest`
- Request flow:
  - Index reads device groups and reference lists directly from models.
  - Show loads the selected device, telemetry rows, maintenance records, and available MCUs.
  - Create/update/delete/replace actions go through the service layer after validation.
- Response flow:
  - Returns `devices.index` or `devices.show` for reads.
  - Write actions redirect back with flash messages.
  - Replace redirects to the new device detail page.

### `EmployeeController`

- Responsibility: Employee CRUD flow for web views.
- Public methods:
  - `index()`
  - `store(StoreEmployeeRequest $request)`
  - `update(UpdateEmployeeRequest $request, $id)`
  - `destroy($id)`
- Dependencies:
  - `App\Services\EmployeeService`
  - `App\Models\Role`
  - `App\Http\Requests\StoreEmployeeRequest`
  - `App\Http\Requests\UpdateEmployeeRequest`
- Request flow:
  - List action loads employees and roles.
  - Write actions use validated request payloads.
- Response flow:
  - Returns `employees.index` for reads.
  - Write actions redirect to `employees.index`.

### `McuController`

- Responsibility: MCU listing and lifecycle management.
- Public methods:
  - `index()`
  - `store(StoreMcuRequest $request)`
  - `update(UpdateMcuRequest $request, Mcu $mcu)`
  - `destroy(Mcu $mcu)`
- Dependencies:
  - `App\Services\McuService`
  - `App\Models\Mcu`
  - `App\Http\Requests\StoreMcuRequest`
  - `App\Http\Requests\UpdateMcuRequest`
- Request flow:
  - Index groups MCUs into used and unused sets.
  - Create/update/delete go through the service layer.
- Response flow:
  - Returns `mcus.index` for reads.
  - Create/update/delete return back with success or error messages.

### `ProfileController`

- Responsibility: User profile display and profile/password updates.
- Public methods:
  - `index()`
  - `updateProfile(UpdateProfileRequest $request)`
  - `updatePassword(UpdateProfileRequest $request)`
- Dependencies:
  - `Illuminate\Support\Facades\Auth`
  - `Illuminate\Support\Facades\Hash`
  - `Illuminate\Support\Facades\Storage`
  - `App\Http\Requests\UpdateProfileRequest`
- Request flow:
  - Index reads the current authenticated user and recent activity logs.
  - Profile update validates username/email/avatar/password.
  - Password update checks the current password before persisting the new one.
- Response flow:
  - Returns `profile.index` for reads.
  - Profile and password updates return back with flash messages or validation errors.

### `TelemetryController`

- Responsibility: Ingest device telemetry from external callers.
- Public methods:
  - `ingest(Request $request)`
- Dependencies:
  - `Illuminate\Http\Request`
  - `App\Models\Mcu`
  - `App\Models\DeviceDashboardData`
- Request flow:
  - Validates MCU ID, API key, optional timestamp, and telemetry fields.
  - Locates the MCU by `mcu_code`.
  - Resolves the current device attached to that MCU.
  - Persists a telemetry record and updates MCU status.
- Response flow:
  - Returns JSON errors for missing MCU, bad API key, or missing device.
  - Returns `{"status":"ok"}` on success.

## Services

### `ProductService`

- Responsibility: Product data access and write operations.
- Public methods:
  - `getAllProducts()`
  - `getProductById($id)`
  - `createProduct(array $data)`
  - `updateProduct($id, array $data)`
  - `deleteProduct($id)`
  - `getProductsByCategory($categoryId)`
- Dependencies: `App\Models\Product`.
- Request flow: Called by `ProductController` after validation.
- Response flow: Returns Eloquent models or booleans; controller decides HTML or JSON response.

### `CategoryService`

- Responsibility: Category data access and write operations.
- Public methods:
  - `getAllCategories()`
  - `getCategoryById($id)`
  - `createCategory(array $data)`
  - `updateCategory($id, array $data)`
  - `deleteCategory($id)`
- Dependencies: `App\Models\Category`.
- Request flow: Called by `CategoryController` after validation.
- Response flow: Returns Eloquent models or booleans; controller formats the final response.

### `InventoryService`

- Responsibility: Inventory read and adjustment operations.
- Public methods:
  - `getAllInventories()`
  - `getInventoryById($id)`
  - `adjustInventory($id, array $data)`
  - `getAvailable($id)`
  - `getStockStatus($id)`
- Dependencies: `App\Models\Inventory`.
- Request flow:
  - Controllers call `adjustInventory()` with validated quantity, reserved quantity, and unit cost.
  - Validation failure on reserved quantity is enforced in the service.
- Response flow:
  - Returns updated inventory models or computed values.
  - Throws an exception for invalid quantity relationships.

### `BatchService`

- Responsibility: Batch data access and write operations.
- Public methods:
  - `getAllBatches()`
  - `getBatchById($id)`
  - `createBatch(array $data)`
  - `updateBatch($id, array $data)`
  - `deleteBatch($id)`
- Dependencies: `App\Models\Batch`.
- Request flow: Called by `BatchController` after request validation.
- Response flow: Returns Eloquent models or booleans; controller formats redirects or views.

### `CustomerService`

- Responsibility: Customer data access and write operations.
- Public methods:
  - `getAllCustomers()`
  - `getCustomerById($id)`
  - `createCustomer(array $data)`
  - `updateCustomer($id, array $data)`
  - `deleteCustomer($id)`
- Dependencies: `App\Models\Customer`.
- Request flow: Called by `CustomerController` after validation.
- Response flow: Returns Eloquent models or booleans; controller handles the user-facing response.

### `ContractService`

- Responsibility: Contract creation, update, deletion, and device assignment orchestration.
- Public methods:
  - `getAllContracts()`
  - `getContractById($id)`
  - `createContract(array $data)`
  - `updateContract($id, array $data)`
  - `deleteContract($id)`
- Dependencies:
  - `App\Models\Contract`
  - `App\Models\Customer`
  - `App\Models\Device`
  - `App\Models\Mcu`
- Request flow:
  - Receives validated contract payloads from `ContractController`.
  - Optionally creates a customer record when the input includes `customer_name`.
  - Optionally assigns devices and MCUs to the new contract.
- Response flow:
  - Returns the contract model.
  - Controllers convert it into Blade redirects or, if extended, JSON.

### `DeviceService`

- Responsibility: Device CRUD and replacement workflow.
- Public methods:
  - `getAllDevices()`
  - `getDeviceById($id)`
  - `createDevice(array $data)`
  - `updateDevice($id, array $data)`
  - `deleteDevice($id)`
  - `replaceDevice(int $oldDeviceId, array $newData)`
- Dependencies:
  - `App\Models\Device`
  - `Illuminate\Support\Facades\DB`
- Request flow:
  - Controllers validate device payloads first.
  - Replacement runs inside a database transaction.
- Response flow:
  - Returns device models.
  - Replacement returns the new device after marking the old device as replaced.

### `EmployeeService`

- Responsibility: Employee CRUD operations.
- Public methods:
  - `getAllEmployees()`
  - `getEmployeeById($id)`
  - `createEmployee(array $data)`
  - `updateEmployee($id, array $data)`
  - `deleteEmployee($id)`
- Dependencies: `App\Models\Employee`.
- Request flow: Called by `EmployeeController` after validation.
- Response flow: Returns Eloquent models or booleans.

### `McuService`

- Responsibility: MCU lookup, availability filtering, creation, update, and guarded deletion.
- Public methods:
  - `getAllMcus()`
  - `getMcuById(int $id)`
  - `getAvailableMcus()`
  - `createMcu(array $data): Mcu`
  - `updateMcu(int $id, array $data): Mcu`
  - `deleteMcu(int $id): bool`
- Dependencies:
  - `App\Models\Mcu`
  - `Illuminate\Support\Str`
- Request flow:
  - Controllers submit validated MCU data.
  - MCU creation auto-generates an API key.
  - Deletion is blocked if the MCU still has an active device link.
- Response flow:
  - Returns MCU models or a boolean.
  - Throws an exception for delete refusal.

### `DashboardService`

- Responsibility: Aggregate dashboard metrics and widget datasets.
- Public methods:
  - `getKpis()`
  - `getDeviceStatusBreakdown()`
  - `getCustomersByMonth()`
  - `getMaintenanceByMonth()`
  - `getRecentActivity($limit = 6)`
  - `getRecentMaintenance($limit = 5)`
  - `getExpiringContracts($limit = 5)`
- Dependencies:
  - `App\Models\Customer`
  - `App\Models\Product`
  - `App\Models\Device`
  - `App\Models\Contract`
  - `App\Models\MaintenanceRecord`
  - `App\Models\ActivityLog`
- Request flow: Called by `DashboardController` for the main dashboard view.
- Response flow:
  - Returns arrays and collections already shaped for the dashboard page.
  - No HTTP response is produced directly by the service.

## Helper / Support Classes

### `App\Support\MockData`

- Responsibility: Static in-memory data provider for the demo backend.
- Public methods:
  - `categories()`
  - `products()`
  - `suppliers()`
  - `inventories()`
  - `batches()`
  - `batchDetails(int $batchId)`
  - `customers()`
  - `findCustomer(int $id)`
  - `contracts()`
  - `contractsForCustomer(int $customerId)`
  - `contractServices()`
  - `devices()`
  - `findDevice(int $id)`
  - `devicesForCustomer(int $customerId)`
  - `telemetry(string $range = '24h')`
  - `roles()`
  - `employees()`
  - `maintenance()`
  - `maintenanceForDevice(int $deviceId)`
  - `maintenanceForCustomer(string $customerName)`
  - `activities()`
  - `dashboardKpis()`
  - `deviceStatusBreakdown()`
  - `customersByMonth()`
  - `maintenanceByMonth()`
  - `currentUser()`
  - `notifications()`
- Dependencies:
  - `Illuminate\Support\Collection`
  - Laravel helper functions such as `collect()` and `now()`
- Request flow:
  - Used by `ActivityController` and by view composition in `AppServiceProvider`.
  - Serves as an in-memory data source when no live data source is desired.
- Response flow:
  - Returns collections or arrays only.
  - No HTTP response is produced directly.

## Middleware

- No custom middleware classes are present under `app/Http/Middleware`.
- The application uses Laravel’s route middleware such as `auth` and `api` from framework defaults.

## Repositories

- No repository layer exists in this codebase.
- Controllers call services directly, and services call Eloquent models directly.

## Business Services

- The service classes in `app/Services` are the business-service layer.
- They hold the main data orchestration logic for products, categories, inventory, batches, customers, contracts, devices, employees, MCUs, and dashboard aggregation.
- There is no separate domain-service package or repository abstraction between controllers and models.

## Backend Wiring

### `AppServiceProvider`

- Responsibility: Framework bootstrapping and shared view data.
- Public methods:
  - `register()`
  - `boot()`
- Dependencies:
  - `App\Support\MockData`
  - `Illuminate\Support\Facades\View`
- Request flow:
  - Registers a view composer for `layouts.*`.
  - Injects `currentUser` and `navNotifications` into layout views.
- Response flow:
  - No direct HTTP response.
  - Alters the data available to layout-rendered views.

### `bootstrap/app.php`

- Responsibility: Laravel application bootstrap and route registration.
- Public methods: None.
- Dependencies: Laravel application factory and middleware/exception configuration.
- Request flow: Connects the HTTP and API route files to the application.
- Response flow: Creates the application instance used by `public/index.php` and `artisan`.

