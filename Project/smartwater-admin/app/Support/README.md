# `app/Support/`

`MockData.php` cung cấp dữ liệu hỗ trợ giao diện, hiện được `AppServiceProvider` dùng cho `currentUser` và `navNotifications` của layout.

Đây không phải nguồn dữ liệu telemetry, product, customer, contract, device hoặc MCU. Các thực thể đó được lưu và truy vấn qua Eloquent models/MySQL.
