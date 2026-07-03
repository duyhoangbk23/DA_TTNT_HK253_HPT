# app/Models/ — Eloquent Models (Not used in demo)

Chứa Eloquent Models — Laravel ORM models cho database entities.

## 📝 Status trong demo

**KHÔNG DÙNG** — Demo không có database thực.

Folder này trống hoặc chứa các model scaffolding (nếu từng tạo bằng `php artisan make:model`).

---

## 📌 Nếu scale sang production

Khi chuyển từ MockData sang thực tế, tạo models như:

```php
// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['sku', 'name', 'category_id', 'model', 'capacity', 'status'];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function batches() {
        return $this->hasMany(Batch::class);
    }
}
```

Rồi thay đổi Controller:

```php
// BEFORE (demo)
$products = MockData::products();

// AFTER (production)
$products = Product::all();  // From DB
```

---

## 🔗 Reference

- [Eloquent Models](https://laravel.com/docs/eloquent)
- [Relationships](https://laravel.com/docs/eloquent-relationships)

Hiện tại folder này có thể bỏ trống an toàn (chưa dùng).
