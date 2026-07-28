# `app/`

Mã nguồn Laravel của SmartWater Admin.

| Thư mục | Vai trò |
| --- | --- |
| `Http/` | Controllers, Form Requests và API Resources. |
| `Models/` | Eloquent models cho MySQL. |
| `Providers/` | Bootstrap application và view composer. |
| `Services/` | Business services. |
| `Support/` | Dữ liệu hỗ trợ giao diện, gồm `MockData`. |

Controllers và models sử dụng MySQL cho dữ liệu nghiệp vụ. `MockData` chỉ cung cấp dữ liệu chung cho layout qua `AppServiceProvider`.
