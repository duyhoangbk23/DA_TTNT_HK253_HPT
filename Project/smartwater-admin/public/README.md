# public/ — Publicly accessible files

Chứa tất cả assets công khai — CSS, JavaScript, images, favicon, robots.txt.

## 📁 Cấu trúc

| File/Folder | Tác dụng |
|------------|---------|
| **css/** | Custom CSS stylesheets |
| **js/** | Custom JavaScript files |
| **images/** | Static images (logos, icons, product images) — **TODO: create this folder** |
| `index.php` | Entry point Laravel (đừng chỉnh) |
| `robots.txt` | SEO — robots.txt cho crawlers |
| `.htaccess` | Apache rewrite rules (đừng chỉnh) |
| `favicon.ico` | Browser tab icon |

## 🎯 Quy tắc

✅ Serve publicly — bất kỳ file ở đây đều accessible via `http://localhost:8000/filename`
✅ Không commit secrets — API keys, passwords không để ở đây
❌ Không sửa `index.php`, `.htaccess` — Laravel magic ở đây

---

### 📚 Thêm chi tiết

Xem:
- [css/](css/) — Custom theme CSS
- [js/](js/) — JavaScript helpers, initialization
