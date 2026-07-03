# app/ — Mã nguồn ứng dụng Laravel

Chứa toàn bộ logic ứng dụng Laravel (PHP code), bao gồm:

## 📁 Cấu trúc thư mục

| Folder | Tác dụng |
|--------|---------|
| **Http/** | Controllers, Requests, Resources, Middleware — xử lý HTTP request/response |
| **Models/** | Eloquent Models — không sử dụng trong demo này (không có Database) |
| **Providers/** | Service Providers — đăng ký services, bootstrapping ứng dụng |
| **Support/** | Helper classes, Utilities — chứa `MockData.php` (nguồn dữ liệu giả lập) |

## 📌 Chú ý

- Demo **KHÔNG dùng Database** — toàn bộ dữ liệu từ `Support/MockData.php`
- Controllers chỉ fetch dữ liệu từ MockData rồi truyền vào View
- KHÔNG có Business Logic thực, chỉ là giao diện minh họa
