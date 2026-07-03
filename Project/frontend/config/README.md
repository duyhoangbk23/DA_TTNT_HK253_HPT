# config/ — Laravel configuration files

Chứa cấu hình ứng dụng Laravel.

## 📝 File cấu hình chính

| File | Công dụng |
|------|-----------|
| **app.php** | Cấu hình chính app (name, timezone, locale, providers) |
| **auth.php** | Cấu hình authentication (guards, providers) — không dùng trong demo |
| **database.php** | Cấu hình database connections — demo dùng SQLite in-memory |
| **cache.php** | Cấu hình cache drivers — default file cache |
| **session.php** | Cấu hình session storage — default file |
| **mail.php** | Cấu hình mail — không dùng trong demo |
| **logging.php** | Cấu hình logging — log output |
| **filesystems.php** | Cấu hình storage disk — local, S3, etc. |
| **queue.php** | Cấu hình job queue — không dùng trong demo |
| **services.php** | Third-party service credentials — không dùng |

---

## ⚙️ Cấu hình quan trọng

### app.php

```php
'app_timezone' => 'Asia/Ho_Chi_Minh',
'app_locale' => 'vi',  // Vietnamese locale
```

### database.php

```php
'default' => env('DB_CONNECTION', 'sqlite'),
// Demo dùng SQLite (no actual DB needed)
```

### auth.php

**KHÔNG DÙNG TRONG DEMO** — authentication là fake (chỉ redirect trang login tới dashboard).

---

## 📌 Chú ý

✅ Tất cả config được load từ `.env` file (environment variables)
❌ **Không sửa trực tiếp** config files — sửa `.env` thay vào

---

## 🔍 Ví dụ: Đổi cấu hình

**KHÔNG nên:**
```php
// config/app.php
'timezone' => 'UTC',  // ❌ Hard-coded
```

**NÊN:**
```php
// .env
APP_TIMEZONE=Asia/Ho_Chi_Minh

// config/app.php
'timezone' => env('APP_TIMEZONE', 'UTC'),  // ✅ Từ .env
```

---

## 📚 Tìm hiểu thêm

- [Laravel Config](https://laravel.com/docs/configuration)
- [Environment Configuration](https://laravel.com/docs/configuration#environment-configuration)
