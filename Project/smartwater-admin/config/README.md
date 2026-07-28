# `config/`

Các file Laravel config lấy giá trị runtime từ `.env`.

`database.php` dùng MySQL làm connection mặc định. Giá trị mặc định của connection MySQL/MariaDB là:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartwater_database
DB_USERNAME=root
DB_PASSWORD=
```

Migrations không nằm trong `smartwater-admin`; chạy `Project/smartwater-database/migrate.bat` để áp dụng schema. Không hard-code thông tin môi trường vào file config.
