# SmartWater Admin

SmartWater Admin là ứng dụng Laravel quản lý sản phẩm, khách hàng, hợp đồng, MCU và thiết bị SmartWater.

## Yêu cầu

- PHP 8.3+
- Composer 2.x
- MySQL với extension `pdo_mysql`
- Node.js và npm nếu cần build Vite assets

## Database ownership

Schema và seeders nằm trong `Project/smartwater-database`. SmartWater Admin autoload seeders từ project đó nhưng không sở hữu migration directory.

Cấu hình MySQL mặc định trong `.env.example`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartwater_database
DB_USERNAME=root
DB_PASSWORD=
```

## Chạy local

```bat
cd Project\smartwater-admin
composer install
copy .env.example .env
php artisan key:generate
..\smartwater-database\migrate.bat
php artisan db:seed --force
php artisan serve --host=127.0.0.1 --port=8000
```

Truy cập `http://127.0.0.1:8000/login`.

`run.bat` kiểm tra PHP, Composer, `.env`, `APP_KEY` và khởi động server. Khi migration cần chạy đúng schema đang dùng, dùng `../smartwater-database/migrate.bat`.

## Chức năng chính

- Quản lý product, category, inventory, batch và supplier.
- Quản lý customer, contract, employee và activity log.
- Quản lý MCU, device và thay thế device.
- Lưu telemetry qua `POST /api/telemetry`.

Thiết bị liên kết `product_id`, `customer_id`, `contract_id` và `mcu_id`. MCU dùng mã chuỗi `mcu_id`.

## API

| Method | Endpoint | Mô tả |
| --- | --- | --- |
| `POST` | `/api/telemetry` | Nhận telemetry dạng phẳng hoặc lồng trong `payload`. |
| `GET/POST` | `/api/products` | Danh sách hoặc tạo product. |
| `GET/POST` | `/api/categories` | Danh sách hoặc tạo category. |
| `GET` | `/api/inventories` | Danh sách inventory. |
| `PATCH` | `/api/inventories/{id}/adjust` | Điều chỉnh inventory. |

Các endpoint product/category/inventory còn có `GET`, `PUT` hoặc `DELETE` theo `{id}` như khai báo ở `routes/api.php`.

## Dữ liệu mẫu telemetry

Seeder `Database\Seeders\ConnectedTelemetryDemoSeeder` tạo `MCU-DEMO-001` ở trạng thái `CONNECTED`, liên kết với product, contract và customer; đồng thời tạo ba telemetry TDS. Chạy riêng:

```bat
php artisan db:seed --class="Database\Seeders\ConnectedTelemetryDemoSeeder" --force
```

## Kiểm tra

```bat
php artisan test
```

Kiểm tra riêng mẫu MCU:

```bat
php artisan test --filter=ConnectedTelemetryDemoSeederTest
```
