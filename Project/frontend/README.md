# SmartWater Admin — Demo giao diện quản lý dịch vụ bảo trì máy lọc nước

## Giới thiệu

Đây là **website demo giao diện** (UI-only) cho hệ thống quản lý dịch vụ bảo trì máy
lọc nước, phục vụ mục đích thực tập. Dự án **chỉ xây dựng Frontend**:

- Không có Backend/Business Logic thật.
- Không kết nối Database (chưa có DB).
- Không có API.
- Không có Authentication thật (trang `/login` chỉ là giao diện, bấm **Đăng nhập** sẽ
  chuyển thẳng sang Dashboard).
- Toàn bộ dữ liệu hiển thị là **Mock Data**, được định nghĩa tại
  [`app/Support/MockData.php`](app/Support/MockData.php).

## Công nghệ sử dụng

| Thành phần        | Công nghệ |
|-------------------|-----------|
| Framework         | Laravel 12 |
| Template Engine   | Blade |
| CSS Framework     | Bootstrap 5 (qua CDN) |
| Icon              | Bootstrap Icons (qua CDN) |
| Biểu đồ           | ApexCharts (qua CDN) |
| Bảng dữ liệu      | DataTables (qua CDN) |
| JS Helper         | jQuery (qua CDN) |
| Theme tuỳ biến    | `public/css/app.css`, `public/js/app.js` |

> Toàn bộ thư viện frontend (Bootstrap, Icons, ApexCharts, DataTables, jQuery) được
> tải qua CDN trong [`resources/views/layouts/app.blade.php`](resources/views/layouts/app.blade.php),
> nên **không bắt buộc** phải chạy `npm install` / Vite chỉ để xem demo.

## Yêu cầu môi trường

- **PHP >= 8.2** (đã kiểm thử với PHP 8.5.5).
- **Composer** 2.x.

> ⚠️ Lưu ý trên Windows: nếu PHP không có sẵn `php.ini` được load (`php --ini` báo
> `Loaded Configuration File: (none)`), lệnh `composer install` / `php artisan` có thể
> báo lỗi thiếu extension `openssl`. Cần tạo/khai báo `php.ini` (đặt cạnh `php.exe`)
> và bật tối thiểu các extension sau trước khi chạy:
>
> ```ini
> extension=openssl
> extension=curl
> extension=zip
> extension=mbstring
> extension=fileinfo
> extension=pdo_sqlite
> extension=sqlite3
> ```

## Các bước chạy demo

```bash
cd Project/frontend

# 1. Cài dependency PHP (bỏ qua nếu đã có thư mục vendor/)
composer install

# 2. Tạo file .env nếu chưa có
cp .env.example .env

# 3. Sinh APP_KEY nếu .env chưa có APP_KEY
php artisan key:generate

# 4. Khởi chạy server demo
php artisan serve
```

Mặc định server chạy tại `http://127.0.0.1:8000`. Có thể đổi cổng khác nếu cần:

```bash
php artisan serve --port=8899
```

Sau đó mở trình duyệt vào địa chỉ được in ra (hoặc trực tiếp `/login` để vào màn hình
đăng nhập demo).

## Danh sách trang / route

| Route | Trang |
|-------|-------|
| `/login` | Đăng nhập |
| `/` hoặc `/dashboard` | Dashboard (KPI, biểu đồ, widget) |
| `/products` | Quản lý sản phẩm |
| `/inventory` | Kho thiết bị |
| `/batches` | Danh sách lô hàng |
| `/batches/{id}` | Chi tiết lô hàng |
| `/customers` | Danh sách khách hàng |
| `/customers/{id}` | Chi tiết khách hàng |
| `/contracts` | Hợp đồng |
| `/devices` | Danh sách thiết bị |
| `/devices/{id}` | Chi tiết thiết bị (biểu đồ cảm biến mock) |
| `/employees` | Nhân viên |
| `/activities` | Lịch sử hoạt động |
| `/profile` | Hồ sơ cá nhân |

Toàn bộ route được khai báo tại [`routes/web.php`](routes/web.php) và chỉ trả về View,
không xử lý nghiệp vụ.

## Giới hạn của bản demo

- Không có xử lý đăng nhập/đăng xuất thật — chỉ là giao diện.
- Không có thao tác Thêm/Sửa/Xóa được lưu trữ thật (dữ liệu không đổi giữa các lần tải trang).
- Không kết nối Database, API hay MQTT.
- Dữ liệu hiển thị (khách hàng, thiết bị, hợp đồng, lô hàng, nhân viên, chỉ số cảm
  biến...) đều là dữ liệu giả lập, được sinh ra trong
  [`app/Support/MockData.php`](app/Support/MockData.php) và không đổi khi tải lại trang.
