# SmartWater Database

`smartwater-database` sở hữu migrations và seeders dùng chung cho SmartWater Admin và Device Monitor. Ứng dụng không tự tạo hoặc thay đổi schema khi chạy.

## Migration

Chạy từ thư mục này trên Windows:

```bat
migrate.bat
```

`migrate.bat` gọi Laravel runtime ở `../smartwater-admin/artisan` với migration path `database/migrations` của project này.

Database mặc định là `smartwater_database` tại `127.0.0.1:3306`; cấu hình thực tế nằm trong `Project/smartwater-admin/.env`.

## Seed dữ liệu

Sau khi migration hoàn tất, chạy từ `Project/smartwater-admin`:

```bat
php artisan db:seed --force
```

Seeder được autoload từ thư mục `smartwater-database/database/seeders` qua Composer của SmartWater Admin.

### Mẫu MCU đã kết nối

`ConnectedTelemetryDemoSeeder` tạo hoặc cập nhật chuỗi dữ liệu sau:

| Thành phần | Mã mẫu |
| --- | --- |
| Customer | `KH-DEMO-001` |
| Contract | `HD-DEMO-001` |
| Product | `SP-DEMO-001` |
| Device | `TB-DEMO-001` |
| MCU | `MCU-DEMO-001` (`CONNECTED`) |
| Telemetry | 3 bản ghi TDS trên `devices/telemetry` |

Chạy riêng seeder:

```bat
php artisan db:seed --class="Database\Seeders\ConnectedTelemetryDemoSeeder" --force
```

Seeder xóa và tạo lại telemetry của `MCU-DEMO-001`, vì vậy chạy nhiều lần vẫn giữ đúng ba bản ghi mẫu.

## Cấu trúc

```text
database/
  migrations/   # Schema MySQL
  seeders/      # Dữ liệu mẫu và dữ liệu khởi tạo
  factories/    # Laravel factories
migrate.bat     # Chạy migrations bằng SmartWater Admin
```
