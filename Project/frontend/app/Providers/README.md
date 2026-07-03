# app/Providers/ — Service Providers

Bootstrapping và cấu hình ứng dụng Laravel.

## 📌 AppServiceProvider.php

**Tác dụng chính:**

1. **View Composer** — Tự động chia sẻ dữ liệu toàn cục sang tất cả views:
   ```
   - currentUser: Thông tin user đăng nhập (từ MockData)
   - navNotifications: Thông báo navbar (từ MockData)
   ```

2. Giúp `navbar.blade.php` (partials) luôn có dữ liệu mà không cần truyền từ từng Controller

## 💡 Sử dụng

Khi tạo view mới, `currentUser` và `navNotifications` có sẵn mà không cần:

```php
// BAD - không cần
return view('my-page', [
    'currentUser' => MockData::currentUser(),
    'navNotifications' => MockData::navNotifications(),
]);

// GOOD - View Composer tự cung cấp
return view('my-page', $data);
```

Xem `AppServiceProvider.php` để chi tiết cài đặt.
