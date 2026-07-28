# `app/Http/`

`Http/Controllers` xử lý web routes và API routes. Các controller hiện có gồm authentication, dashboard, product, category, inventory, batch, customer, contract, MCU, device, employee, activity, profile và telemetry.

- Web routes được khai báo trong `routes/web.php` và yêu cầu middleware `auth` ngoại trừ `/login`.
- API routes được khai báo trong `routes/api.php`.
- `TelemetryController` xử lý `POST /api/telemetry`.
- `Requests/` chứa validation request cho các thao tác tạo và cập nhật.
- `Resources/` chứa JSON resource khi controller cần chuẩn hóa response.
