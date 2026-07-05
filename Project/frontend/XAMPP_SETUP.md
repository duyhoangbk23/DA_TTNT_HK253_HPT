# Hướng dẫn Chạy Frontend trên XAMPP

Hướng dẫn này giúp bạn cấu hình và chạy ứng dụng Laravel (frontend) trên XAMPP.

## Yêu cầu

- **XAMPP** (với PHP >= 8.2, Apache)
- **Composer** 2.x
- **Git** (để clone/pull repository)

## Cách 1: Sử dụng Virtual Host (Khuyến nghị) ⭐

### Bước 1: Copy project vào thư mục XAMPP

```bash
# Giả sử XAMPP được cài tại C:\xampp
# Copy folder frontend vào:
C:\xampp\htdocs\smartwater-frontend\
```

### Bước 2: Cấu hình Virtual Host

#### Trên Windows:

1. **Mở file hosts:**
   - Mở Command Prompt **as Administrator**
   - Chạy: `notepad C:\Windows\System32\drivers\etc\hosts`

2. **Thêm dòng sau vào file hosts:**
   ```
   127.0.0.1   smartwater.local
   ```

3. **Cấu hình Apache Virtual Host:**
   - Mở file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Thêm đoạn code sau vào cuối file:

   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:\xampp\htdocs\smartwater-frontend"
       ServerName smartwater.local
       ServerAlias www.smartwater.local

       <Directory "C:\xampp\htdocs\smartwater-frontend">
           AllowOverride All
           Require all granted
       </Directory>

       # Log files
       ErrorLog "logs/smartwater-error.log"
       CustomLog "logs/smartwater-access.log" combined
   </VirtualHost>
   ```

4. **Enable mod_rewrite:**
   - Mở file: `C:\xampp\apache\conf\httpd.conf`
   - Tìm dòng `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Bỏ dấu `#` ở đầu dòng (nếu chưa bỏ)
   - Tìm dòng `AllowOverride None` trong `<Directory "C:/xampp/htdocs">`
   - Thay thành: `AllowOverride All`

5. **Restart Apache:**
   - Trong XAMPP Control Panel, click "Restart" trên Apache

### Bước 3: Cài đặt Dependencies

```bash
cd C:\xampp\htdocs\smartwater-frontend
composer install
```

### Bước 4: Sinh APP_KEY (nếu chưa có)

```bash
php artisan key:generate
```

### Bước 5: Cập nhật .env (nếu dùng virtual host)

```bash
# Nếu dùng smartwater.local
APP_URL=http://smartwater.local
```

Mở trình duyệt và truy cập: **http://smartwater.local**

---

## Cách 2: Sử dụng URL mặc định (Nhanh nhất)

Nếu không muốn cấu hình virtual host:

### Bước 1: Copy vào htdocs

```bash
# Copy thư mục frontend vào C:\xampp\htdocs
C:\xampp\htdocs\frontend\
```

### Bước 2: Cài đặt Dependencies

```bash
cd C:\xampp\htdocs\frontend
composer install
```

### Bước 3: Sinh APP_KEY

```bash
php artisan key:generate
```

### Bước 4: Kiểm tra .env

Đảm bảo `.env` có:
```env
APP_URL=http://localhost/frontend
```

### Bước 5: Truy cập

Mở trình duyệt và vào: **http://localhost/frontend**

---

## Cách 3: Sử dụng Artisan Serve (Phát triển nhanh)

Nếu muốn chạy mà không cần copy vào htdocs:

```bash
cd C:\path\to\Project\frontend
composer install
php artisan key:generate
php artisan serve --port=8000
```

Truy cập: **http://127.0.0.1:8000**

---

## Xử lý sự cố

### Lỗi: "mod_rewrite not enabled"

**Giải pháp:**
1. Mở `C:\xampp\apache\conf\httpd.conf`
2. Tìm `LoadModule rewrite_module modules/mod_rewrite.so`
3. Bỏ dấu `#` ở đầu dòng
4. Restart Apache

### Lỗi: "APP_KEY not set"

**Giải pháp:**
```bash
php artisan key:generate
```

### Lỗi: 404 Not Found / .htaccess không hoạt động

**Kiểm tra:**
1. Đảm bảo `AllowOverride All` trong cấu hình Virtual Host
2. Kiểm tra file `.htaccess` và `public/.htaccess` tồn tại
3. Restart Apache sau khi thay đổi cấu hình

### Lỗi: Không tìm thấy file

**Kiểm tra:**
```bash
# Đảm bảo vendor folder và tất cả files tồn tại
ls vendor/
ls public/
```

---

## Thêm tính năng (Optional)

### Chạy Database Migrations (nếu cần)

```bash
php artisan migrate
```

### Seed Mock Data

```bash
php artisan db:seed
```

### Build Assets (CSS/JS)

```bash
npm install
npm run build
```

---

## Ghi chú

- Dự án này **chỉ là Frontend Demo** (UI-only), không có Business Logic thật
- Dữ liệu hiển thị là **Mock Data** từ `app/Support/MockData.php`
- **Không có Database thật** - chỉ dùng SQLite demo
- **Không có Authentication thật** - login chỉ là giao diện

---

## Các Route disponible

| Route | Mô tả |
|-------|-------|
| `/login` | Trang đăng nhập (demo) |
| `/` | Dashboard chính |
| `/products` | Quản lý sản phẩm |
| `/inventory` | Kho thiết bị |
| `/batches` | Danh sách lô hàng |
| `/customers` | Danh sách khách hàng |
| `/contracts` | Hợp đồng |
| `/devices` | Danh sách thiết bị |
| `/employees` | Nhân viên |
| `/activities` | Lịch sử hoạt động |
| `/profile` | Hồ sơ cá nhân |
