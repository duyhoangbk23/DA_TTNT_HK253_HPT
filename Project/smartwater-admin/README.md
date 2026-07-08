# SmartWater Admin - Hướng dẫn chạy website

## Giới thiệu

Đây là giao diện quản lý hệ thống dịch vụ bảo trì máy lọc nước. Dự án được phát triển trên nền tảng **Laravel 11** & **SQLite** hỗ trợ kiểm thử và phát triển nhanh chóng.

## Yêu cầu môi trường

Trước khi chạy, hãy đảm bảo máy của bạn đã cài:

- PHP 8.2 trở lên (với các extension: `pdo_sqlite`, `sqlite3`, `curl`, `openssl`, `mbstring`, `zip`)
- Composer 2.x trở lên
- Git

---

## Cách chạy trên máy local

Bạn có thể chạy dự án bằng một trong hai cách dưới đây:

### Cách 1: Chạy nhanh qua File Script (Chỉ áp dụng Windows)

Chỉ cần chạy file script khởi động thông minh duy nhất đã được tối ưu hóa:
1. Nhấp đúp chuột vào file **`run.bat`** tại thư mục gốc của `smartwater-admin`.
2. Script sẽ tự động:
   - Kiểm tra các phần mềm yêu cầu (PHP, Composer).
   - Tạo file `.env` từ `.env.example` nếu chưa có (mặc định kết nối tới MySQL).
   - Tự động chạy `composer install` nếu phát hiện thiếu thư mục `vendor`.
   - Sinh khóa bảo mật `APP_KEY` và chạy các bước migration database. (Nếu kết nối MySQL thất bại, script sẽ đưa ra hướng dẫn để bạn tạm thời chuyển sang SQLite).
   - Khởi động Laravel Development Server tại địa chỉ `http://127.0.0.1:8000`.

---

### Cách 2: Chạy thủ công bằng Terminal (Mọi hệ điều hành)

1. **Mở terminal và di chuyển vào thư mục dự án**:
   ```bash
   cd Project/smartwater-admin
   ```

2. **Cài đặt các thư viện PHP**:
   ```bash
   composer install
   ```

3. **Cấu hình file môi trường**:
   Sao chép file `.env.example` thành `.env` để làm file cấu hình:
   * Trên Windows:
     ```bash
     copy .env.example .env
     ```
   * Trên Linux/macOS:
     ```bash
     cp .env.example .env
     ```
    *(Mặc định cấu hình sử dụng MySQL. Nếu muốn chạy thử nhanh bằng SQLite, vui lòng đổi `DB_CONNECTION=sqlite` trong file `.env`)*

4. **Tạo khóa bảo mật ứng dụng**:
   ```bash
   php artisan key:generate
   ```

5. **Khởi động server phát triển**:
   ```bash
   php artisan serve
   ```
   *Nếu cổng `8000` bị trùng, bạn có thể chỉ định cổng khác:*
   ```bash
   php artisan serve --port=8899
   ```

---

## Thông tin tài khoản & các trang chính

Sau khi chạy server thành công, truy cập trình duyệt theo địa chỉ:
👉 **[http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)**

### 🔑 Tài khoản đăng nhập Demo:
- **Email:** `admin@smartwater.vn`
- **Mật khẩu:** `password123`

### Các màn hình quản trị chính:
- `/login` - Trang đăng nhập demo
- `/` hoặc `/dashboard` - Bảng điều khiển tổng quan (KPIs, trạng thái thiết bị...)
- `/products` - Quản lý sản phẩm máy lọc nước
- `/inventory` - Quản lý kho linh kiện & thiết bị
- `/batches` - Danh sách lô hàng nhập kho
- `/customers` - Quản lý khách hàng
- `/contracts` - Quản lý hợp đồng bảo trì
- `/devices` - Quản lý thiết bị IoT & thiết bị lắp đặt
- `/employees` - Danh sách nhân viên kỹ thuật
- `/activities` - Nhật ký lịch sử hoạt động hệ thống
- `/profile` - Hồ sơ thông tin cá nhân

---

## Quản lý Cơ sở dữ liệu

Dự án mặc định sử dụng **MySQL** làm cơ sở dữ liệu chuẩn (môi trường Production/Development chính thức).
* **Cấu hình mặc định (MySQL):** 
  - Host: `127.0.0.1` | Port: `3306`
  - Database: `smartwater_admin`
  - Username: `root` | Password: ` `
  - Bạn cần chắc chắn máy chủ MySQL (ví dụ qua XAMPP, Laragon, Docker) đang hoạt động và database `smartwater_admin` đã được tạo trước khi chạy.

* **Sử dụng SQLite (Giải pháp thay thế tạm thời):**
  Nếu muốn chạy thử nghiệm nhanh ứng dụng mà không cần cài đặt hoặc khởi chạy MySQL server:
  1. Mở file `.env` và thay đổi: `DB_CONNECTION=sqlite`.
  2. Bỏ qua cấu hình host/port/database (Hệ thống sẽ tự nhận diện cơ sở dữ liệu SQLite demo tại `database/database.sqlite`).
  
* **Làm sạch và nạp lại dữ liệu mẫu:**
  ```bash
  php artisan migrate:fresh --seed
  ```

---

## Cấu trúc thư mục được tinh chỉnh
Để dự án gọn gàng nhất, toàn bộ các file rác, file log tạm (`artisan_serve.log`, `.phpunit.result.cache`) và các file setup XAMPP dư thừa (`xampp-setup.*`, `start-server.*`) đã được dọn dẹp khỏi mã nguồn, chỉ giữ lại file chạy tối ưu nhất là `run.bat`.
```
smartwater-admin/
  ├── app/                  # Logic xử lý chính (Controllers, Models, Services)
  ├── bootstrap/            # Khởi động ứng dụng Laravel
  ├── config/               # File cấu hình của Laravel
  ├── database/             # File SQLite, migrations & seeders dữ liệu mẫu
  ├── public/               # File tài nguyên tĩnh và assets đã build (CSS, JS)
  ├── resources/            # Giao diện hiển thị (Blade views, CSS gốc)
  ├── routes/               # Định nghĩa các routes web và API
  ├── run.bat               # File script khởi động duy nhất
  ├── tests/                # Thư mục kiểm thử tự động
  └── package.json / composer.json   # Quản lý thư viện bổ sung
```
