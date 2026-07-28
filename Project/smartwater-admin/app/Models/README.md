# `app/Models/`

Eloquent models ánh xạ MySQL schema do `Project/smartwater-database` sở hữu.

Các model nghiệp vụ chính gồm `Product`, `Customer`, `Contract`, `Device`, `Mcu` và `Telemetry`.

- `Device` liên kết product, customer, contract và MCU bằng `mcu_id` dạng chuỗi.
- `Mcu` có trạng thái đăng ký, kết nối, thời điểm kết nối và danh sách device liên quan.
- `Telemetry` lưu `timestamp`, `topic`, `mcu_id`, `tds` và `alert`.
- Customer, contract, inventory, batch, supplier, employee và activity log cũng dùng Eloquent với quan hệ trong model tương ứng.
