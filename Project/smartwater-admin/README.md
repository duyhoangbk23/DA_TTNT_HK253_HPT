# SmartWater Admin - Hướng dẫn chạy website

## Giới thiệu

Đây là giao diện demo cho hệ thống quản lý dịch vụ bảo trì máy lọc nước. Mục tiêu của dự án là cho người dùng xem trước các màn hình chính như dashboard, quản lý sản phẩm, kho thiết bị, khách hàng và hợp đồng.

## Yêu cầu môi trường

Trước khi chạy, hãy đảm bảo máy của bạn đã cài:

- PHP 8.2 trở lên
- Composer 2.x
- Git

Trên Windows, nếu gặp lỗi thiếu module PHP như `openssl`, `curl`, `zip`, `mbstring`, hãy kiểm tra file `php.ini` và bật các extension cần thiết.

## Cách chạy trên máy local

1. Mở terminal và vào thư mục dự án:

```bash
cd Project/smartwater-admin
```

2. Cài đặt các thư viện PHP:

```bash
composer install
```

3. Tạo file môi trường:

```bash
copy .env.example .env
```

Nếu đang dùng Linux/macOS, dùng:

```bash
cp .env.example .env
```

4. Tạo khóa ứng dụng:

```bash
php artisan key:generate
```

5. Khởi động server:

```bash
php artisan serve
```

Sau khi chạy xong, mở trình duyệt và truy cập:

```text
http://127.0.0.1:8000/login
```

Bạn có thể thay đổi cổng nếu port `8000` đã được dùng:

```bash
php artisan serve --port=8899
```

## Các trang chính

Sau khi mở website, bạn có thể xem các màn hình sau:

- `/login` - trang đăng nhập demo
- `/` hoặc `/dashboard` - dashboard tổng quan
- `/products` - quản lý sản phẩm
- `/inventory` - kho thiết bị
- `/batches` - danh sách lô hàng
- `/customers` - khách hàng
- `/contracts` - hợp đồng
- `/devices` - thiết bị
- `/employees` - nhân viên
- `/activities` - lịch sử hoạt động
- `/profile` - hồ sơ cá nhân

## Nếu muốn chạy với database

Một số chức năng có thể cần database. Nếu bạn muốn chạy đầy đủ theo hướng backend, hãy thực hiện:

```bash
php artisan migrate
php artisan db:seed
```

## Xử lý sự cố thường gặp

- Nếu gặp lỗi `Composer could not find a composer.json file`: hãy chắc chắn đang đứng đúng thư mục dự án.
- Nếu gặp lỗi thiếu extension PHP: mở file `php.ini` và bật các extension cần thiết.
- Nếu port `8000` đang bị chiếm: đổi sang cổng khác bằng lệnh `php artisan serve --port=8899`.

## Ghi chú

Đây là bản demo giao diện, nên dữ liệu hiển thị chủ yếu là dữ liệu mẫu và không cần kết nối backend thật để xem được website.
