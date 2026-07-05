# routes/ — Route definitions

Định nghĩa tất cả HTTP routes (URLs) của ứng dụng.

## 📌 web.php

**Công dụng:** Route table — ánh xạ URL → Controller → View.

**Loại route:** Tất cả là **GET + view-only** (không POST, PUT, DELETE).

### Danh sách 14 routes

| Route | Controller | View | Mô tả |
|-------|------------|------|-------|
| `/login` | AuthController@login | auth/login | Màn hình đăng nhập (demo) |
| `/` | DashboardController@index | dashboard/index | Dashboard |
| `/dashboard` | DashboardController@index | dashboard/index | Dashboard (alias) |
| `/products` | ProductController@index | products/index | Danh sách sản phẩm |
| `/inventory` | InventoryController@index | inventory/index | Kho thiết bị |
| `/batches` | BatchController@index | batch/index | Danh sách lô hàng |
| `/batches/{id}` | BatchController@show | batch/show | Chi tiết lô hàng |
| `/customers` | CustomerController@index | customers/index | Danh sách khách hàng |
| `/customers/{id}` | CustomerController@show | customers/show | Chi tiết khách hàng |
| `/contracts` | ContractController@index | contracts/index | Hợp đồng |
| `/devices` | DeviceController@index | devices/index | Danh sách thiết bị |
| `/devices/{id}` | DeviceController@show | devices/show | Chi tiết thiết bị |
| `/employees` | EmployeeController@index | employees/index | Danh sách nhân viên |
| `/activities` | ActivityController@index | activities/index | Lịch sử hoạt động |
| `/profile` | ProfileController@index | profile/index | Hồ sơ cá nhân |

---

## 📝 Route Pattern

Tất cả routes follow pattern:

```php
Route::get('/path', [ControllerName::class, 'method']);
```

**Không có:**
- POST, PUT, DELETE routes (chỉ GET)
- API routes
- Authentication middleware
- Validation

---

## 🎯 Cách thêm route mới

1. **Tạo Controller:**
   ```php
   // app/Http/Controllers/MyController.php
   public function index() {
       return view('my-page', [
           'data' => MockData::myData(),
       ]);
   }
   ```

2. **Thêm route:**
   ```php
   // routes/web.php
   Route::get('/my-page', [MyController::class, 'index']);
   ```

3. **Tạo View:**
   ```blade
   // resources/views/my-page/index.blade.php
   @extends('layouts.app')
   @section('content')
       <!-- content -->
   @endsection
   ```

---

## ⚠️ Route Detail Pages

Routes có parameter route `{id}` (e.g., `/batches/{1}`):

```php
Route::get('/batches/{id}', [BatchController::class, 'show']);
```

**IMPORTANT:** Routes này mong đợi URL param, nhưng **không validate** id.
Controller tự xử lý filtering từ MockData:

```php
public function show($id) {
    $batches = MockData::batches();
    $batch = $batches->firstWhere('id', $id);
    return view('batch.show', ['batch' => $batch]);
}
```

---

## 🔗 Navigation Links

Links ở **navbar** và **sidebar** pointing to routes:

```blade
<!-- Sidebar menu link -->
<a href="/dashboard">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<!-- Product detail link (dynamic) -->
@foreach ($products as $product)
    <a href="/products/{{ $product['id'] }}">{{ $product['name'] }}</a>
@endforeach
```

---

## 📌 Chú ý quan trọng

✅ Route names không dùng (chỉ paths)
✅ Tất cả trả về view (HTML)
❌ KHÔNG thêm routes cho Create/Edit/Delete (demo chỉ display)
❌ KHÔNG thêm middleware auth (demo không authenticate)
❌ KHÔNG thêm API routes JSON (chỉ HTML views)

---

## 🔧 Debugging routes

```bash
# List all routes
php artisan route:list

# Check specific route
php artisan route:list --name=batches
```

Xem full routes bằng `php artisan route:list` ở terminal.
