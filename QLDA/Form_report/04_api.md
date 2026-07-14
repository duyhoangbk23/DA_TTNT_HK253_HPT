# 04. Routing and API Review - smartwater-admin

Scope: routes and controllers only.

## Route Groups

- Public web routes: `/login` GET and POST.
- Auth-protected web routes: all routes inside `Route::middleware('auth')`.
- Public API routes: all routes in `routes/api.php`.
- API bootstrap: `bootstrap/app.php` registers `routes/web.php` and `routes/api.php`.

## Endpoint Catalog

### AuthController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/login` | `GET` | `AuthController` | `showLoginForm` | None | None | Public | Returns `auth.login` unless already authenticated, then redirects to `/dashboard` | None explicit |
| `/login` | `POST` | `AuthController` | `login` | `email`, `password` | Inline request validation for `email` and `password` | Public | Redirects to intended URL or `/dashboard` on success; back with errors on failure | Validation errors; auth failure message for invalid credentials |
| `/logout` | `POST` | `AuthController` | `logout` | None | None | `auth` middleware | Redirects to `/` with success flash message | None explicit |

### DashboardController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/` | `GET` | `DashboardController` | `index` | None | None | `auth` middleware | Returns `dashboard.index` with dashboard datasets | None explicit |
| `/dashboard` | `GET` | `DashboardController` | `index` | None | None | `auth` middleware | Returns `dashboard.index` with dashboard datasets | None explicit |

### ProductController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/products` | `GET` | `ProductController` | `index` | None | None | `auth` middleware | Returns `products.index` | None explicit |
| `/products` | `POST` | `ProductController` | `store` | Product payload | `StoreProductRequest` | `auth` middleware | Redirects to `products.index` with success flash message | Validation errors |
| `/products/{id}` | `GET` | `ProductController` | `show` | `id` | None | `auth` middleware | Returns `products.show` | Model lookup errors from service (`findOrFail`) |
| `/products/{id}` | `PUT` | `ProductController` | `update` | `id`, product payload | `UpdateProductRequest` | `auth` middleware | Redirects to `products.index` with success flash message | Validation errors; model lookup errors |
| `/products/{id}` | `DELETE` | `ProductController` | `destroy` | `id` | None | `auth` middleware | Redirects to `products.index` with success flash message | Model lookup errors |
| `/api/products` | `GET` | `ProductController` | `apiIndex` | None | None | Public API | Returns JSON resource collection | None explicit |
| `/api/products/{id}` | `GET` | `ProductController` | `apiShow` | `id` | None | Public API | Returns JSON resource object | Model lookup errors |
| `/api/products` | `POST` | `ProductController` | `apiStore` | Product payload | `StoreProductRequest` | Public API | Returns JSON resource object with `201` | Validation errors |
| `/api/products/{id}` | `PUT` | `ProductController` | `apiUpdate` | `id`, product payload | `UpdateProductRequest` | Public API | Returns JSON resource object | Validation errors; model lookup errors |
| `/api/products/{id}` | `DELETE` | `ProductController` | `apiDestroy` | `id` | None | Public API | Returns JSON message `Product deleted` | Model lookup errors |

### CategoryController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/categories` | `GET` | `CategoryController` | `index` | None | None | `auth` middleware | Returns `categories.index` | None explicit |
| `/categories` | `POST` | `CategoryController` | `store` | Category payload | `StoreCategoryRequest` | `auth` middleware | Redirects to `categories.index` with success flash message | Validation errors |
| `/categories/{id}` | `GET` | `CategoryController` | `show` | `id` | None | `auth` middleware | Returns `categories.show` | Model lookup errors |
| `/categories/{id}` | `PUT` | `CategoryController` | `update` | `id`, category payload | `UpdateCategoryRequest` | `auth` middleware | Redirects to `categories.index` with success flash message | Validation errors; model lookup errors |
| `/categories/{id}` | `DELETE` | `CategoryController` | `destroy` | `id` | None | `auth` middleware | Redirects to `categories.index` with success flash message | Model lookup errors |
| `/api/categories` | `GET` | `CategoryController` | `apiIndex` | None | None | Public API | Returns JSON resource collection | None explicit |
| `/api/categories/{id}` | `GET` | `CategoryController` | `apiShow` | `id` | None | Public API | Returns JSON resource object | Model lookup errors |
| `/api/categories` | `POST` | `CategoryController` | `apiStore` | Category payload | `StoreCategoryRequest` | Public API | Returns JSON resource object with `201` | Validation errors |
| `/api/categories/{id}` | `PUT` | `CategoryController` | `apiUpdate` | `id`, category payload | `UpdateCategoryRequest` | Public API | Returns JSON resource object | Validation errors; model lookup errors |
| `/api/categories/{id}` | `DELETE` | `CategoryController` | `apiDestroy` | `id` | None | Public API | Returns JSON message `Category deleted` | Model lookup errors |

### InventoryController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/inventory` | `GET` | `InventoryController` | `index` | None | None | `auth` middleware | Returns `inventory.index` with formatted inventory data | None explicit |
| `/inventory/{id}` | `GET` | `InventoryController` | `show` | `id` | None | `auth` middleware | Returns `inventory.show` | Model lookup errors |
| `/inventory/{id}` | `PATCH` | `InventoryController` | `adjust` | `id`, `quantity`, `reserved_quantity`, `unit_cost` | `AdjustInventoryRequest` | `auth` middleware | Redirects to `inventory.index` with success flash message | Validation errors; service exception if reserved exceeds quantity; model lookup errors |
| `/api/inventories` | `GET` | `InventoryController` | `apiIndex` | None | None | Public API | Returns JSON resource collection | None explicit |
| `/api/inventories/{id}` | `GET` | `InventoryController` | `apiShow` | `id` | None | Public API | Returns JSON resource object | Model lookup errors |
| `/api/inventories/{id}/adjust` | `PATCH` | `InventoryController` | `apiAdjust` | `id`, `quantity`, `reserved_quantity`, `unit_cost` | `AdjustInventoryRequest` | Public API | Returns JSON resource object | Validation errors; service exception; model lookup errors |

### BatchController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/batches` | `GET` | `BatchController` | `index` | None | None | `auth` middleware | Returns `batch.index` | None explicit |
| `/batches` | `POST` | `BatchController` | `store` | Batch payload | `StoreBatchRequest` | `auth` middleware | Redirects to `batches.index` with success flash message | Validation errors |
| `/batches/{id}` | `GET` | `BatchController` | `show` | `id` | None | `auth` middleware | Returns `batch.show` | Model lookup errors |
| `/batches/{id}` | `PUT` | `BatchController` | `update` | `id`, batch payload | `UpdateBatchRequest` | `auth` middleware | Redirects to `batches.index` with success flash message | Validation errors; model lookup errors |
| `/batches/{id}` | `DELETE` | `BatchController` | `destroy` | `id` | None | `auth` middleware | Redirects to `batches.index` with success flash message | Model lookup errors |

### CustomerController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/customers` | `GET` | `CustomerController` | `index` | None | None | `auth` middleware | Returns `customers.index` | None explicit |
| `/customers` | `POST` | `CustomerController` | `store` | Customer payload | `StoreCustomerRequest` | `auth` middleware | Redirects to `customers.index` with success flash message | Validation errors |
| `/customers/{id}` | `GET` | `CustomerController` | `show` | `id` | None | `auth` middleware | Returns `customers.show` | Model lookup errors |
| `/customers/{id}` | `PUT` | `CustomerController` | `update` | `id`, customer payload | `UpdateCustomerRequest` | `auth` middleware | Redirects to `customers.index` with success flash message | Validation errors; model lookup errors |
| `/customers/{id}` | `DELETE` | `CustomerController` | `destroy` | `id` | None | `auth` middleware | Redirects to `customers.index` with success flash message | Model lookup errors |

### ContractController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/contracts` | `GET` | `ContractController` | `index` | None | None | `auth` middleware | Returns `contracts.index` | None explicit |
| `/contracts` | `POST` | `ContractController` | `store` | Contract payload | `StoreContractRequest` | `auth` middleware | Redirects to `contracts.index` with success flash message | Validation errors |
| `/contracts/{id}` | `GET` | `ContractController` | `show` | `id` | None | `auth` middleware | Returns `contracts.show` | Model lookup errors |
| `/contracts/{id}` | `PUT` | `ContractController` | `update` | `id`, contract payload | `UpdateContractRequest` | `auth` middleware | Redirects to `contracts.index` with success flash message | Validation errors; model lookup errors |
| `/contracts/{id}` | `DELETE` | `ContractController` | `destroy` | `id` | None | `auth` middleware | Redirects to `contracts.index` with success flash message | Model lookup errors |

### DeviceController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/devices` | `GET` | `DeviceController` | `index` | None | None | `auth` middleware | Returns `devices.index` | None explicit |
| `/devices` | `POST` | `DeviceController` | `store` | Device payload | `StoreDeviceRequest` | `auth` middleware | Redirects to `devices.index` with success flash message | Validation errors |
| `/devices/{id}` | `GET` | `DeviceController` | `show` | `id` | None | `auth` middleware | Returns `devices.show` | Model lookup errors |
| `/devices/{id}` | `PUT` | `DeviceController` | `update` | `id`, device payload | `UpdateDeviceRequest` | `auth` middleware | Redirects to `devices.index` with success flash message | Validation errors; model lookup errors |
| `/devices/{id}` | `DELETE` | `DeviceController` | `destroy` | `id` | None | `auth` middleware | Redirects to `devices.index` with success flash message | Model lookup errors |
| `/devices/{id}/replace` | `POST` | `DeviceController` | `replace` | `id`, replacement payload | `ReplaceDeviceRequest` | `auth` middleware | Redirects to new `devices.show` page with success flash message | Validation errors; model lookup errors |

### EmployeeController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/employees` | `GET` | `EmployeeController` | `index` | None | None | `auth` middleware | Returns `employees.index` | None explicit |
| `/employees` | `POST` | `EmployeeController` | `store` | Employee payload | `StoreEmployeeRequest` | `auth` middleware | Redirects to `employees.index` with success flash message | Validation errors |
| `/employees/{id}` | `PUT` | `EmployeeController` | `update` | `id`, employee payload | `UpdateEmployeeRequest` | `auth` middleware | Redirects to `employees.index` with success flash message | Validation errors; model lookup errors |
| `/employees/{id}` | `DELETE` | `EmployeeController` | `destroy` | `id` | None | `auth` middleware | Redirects to `employees.index` with success flash message | Model lookup errors |

### ActivityController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/activities` | `GET` | `ActivityController` | `index` | None | None | `auth` middleware | Returns `activities.index` with mock activity data | None explicit |

### ProfileController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/profile` | `GET` | `ProfileController` | `index` | None | None | `auth` middleware | Returns `profile.index` | None explicit |
| `/profile` | `POST` | `ProfileController` | `updateProfile` | `username`, `email`, `avatar`, `current_password`, `password` | `UpdateProfileRequest` | `auth` middleware | Redirects back with success flash message | Validation errors |
| `/profile/password` | `POST` | `ProfileController` | `updatePassword` | `current_password`, `password` | `UpdateProfileRequest` | `auth` middleware | Redirects back with success flash message | Validation errors; wrong current password error |

### McuController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/mcus` | `GET` | `McuController` | `index` | None | None | `auth` middleware | Returns `mcus.index` | None explicit |
| `/mcus` | `POST` | `McuController` | `store` | MCU payload | `StoreMcuRequest` | `auth` middleware | Redirects back with success flash message containing API key | Validation errors |
| `/mcus/{mcu}` | `PUT` | `McuController` | `update` | `mcu`, MCU payload | `UpdateMcuRequest` | `auth` middleware | Redirects back with success flash message | Validation errors; model lookup errors |
| `/mcus/{mcu}` | `DELETE` | `McuController` | `destroy` | `mcu` | None | `auth` middleware | Redirects back with success flash message or error bag | Delete refusal exception; model lookup errors |

### TelemetryController

| URL | Method | Controller | Function | Parameters | Validation | Authentication | Response | Errors |
|---|---|---|---|---|---|---|---|---|
| `/api/telemetry` | `POST` | `TelemetryController` | `ingest` | `mcu_id`, `api_key`, `timestamp`, `tds`, `temperature`, `water_flow`, `ph` | Inline request validation | Public API | Returns JSON `{"status":"ok"}` after storing telemetry and updating MCU state | `404` if MCU not found; `401` if API key mismatch; `404` if MCU has no active device; validation errors |

## Authentication Summary

- Public:
  - `/login` GET and POST
  - `/api/*` routes
- Protected by `auth` middleware:
  - all web routes except login
- No controller-level auth guards are defined beyond the route middleware and `Auth` usage in controllers.

## Validation Summary

- Form request classes are used for create/update/adjust endpoints.
- Inline validation is used in `AuthController@login` and `TelemetryController@ingest`.
- Validation errors will return standard Laravel validation responses or redirect back, depending on route type.

## Error Handling Summary

- Model lookup failures are handled through service `findOrFail()` calls and surface as not-found errors.
- `InventoryService::adjustInventory()` throws if reserved quantity exceeds total quantity.
- `McuController::destroy()` catches exceptions and returns them as validation-style back errors.
- `TelemetryController::ingest()` returns explicit JSON errors for invalid MCU or API key conditions.

