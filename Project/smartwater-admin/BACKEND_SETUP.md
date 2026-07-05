# Backend Setup Guide - SmartWater Admin

## Database Configuration

### SQLite (Development)
Default configuration uses SQLite. No additional setup required.

**Database file**: `database/database.sqlite`

### MySQL (Production)
To switch to MySQL:

1. Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=smartwater
DB_USERNAME=root
DB_PASSWORD=
```

2. Create database:
```bash
mysql -u root -p
CREATE DATABASE smartwater CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Installation

1. **Install dependencies**:
```bash
composer install
```

2. **Generate app key**:
```bash
php artisan key:generate
```

3. **Run migrations**:
```bash
php artisan migrate
```

4. **Seed database**:
```bash
php artisan db:seed
```

This will populate the database with 4 categories, 10 products, 10 inventories, 24 customers, 30 devices, and other test data matching the MockData structure.

## Running the Application

1. **Start development server**:
```bash
php artisan serve
```

Server runs at: `http://localhost:8000`

2. **Using Artisan Tinker** (for quick database testing):
```bash
php artisan tinker
```

Examples:
```php
App\Models\Product::count();
App\Models\Product::with('category')->first();
App\Models\Inventory::with('product')->first();
```

## API Testing

### Accessing API Endpoints

Base URL: `http://localhost:8000/api`

**Products API**:
- `GET /api/products` — List all products
- `POST /api/products` — Create product
- `GET /api/products/{id}` — Get product
- `PUT /api/products/{id}` — Update product
- `DELETE /api/products/{id}` — Delete product

**Categories API**:
- `GET /api/categories` — List all categories
- `POST /api/categories` — Create category
- `GET /api/categories/{id}` — Get category
- `PUT /api/categories/{id}` — Update category
- `DELETE /api/categories/{id}` — Delete category

**Inventories API**:
- `GET /api/inventories` — List all inventories
- `GET /api/inventories/{id}` — Get inventory
- `PATCH /api/inventories/{id}/adjust` — Adjust inventory quantities

### Example cURL requests:

```bash
# Get all products
curl http://localhost:8000/api/products

# Get product by ID
curl http://localhost:8000/api/products/1

# Get all inventories
curl http://localhost:8000/api/inventories
```

## Verification

After setup, verify the database is populated:

```bash
php artisan tinker
```

```php
# Check record counts
App\Models\Product::count();        // Should return: 10
App\Models\Category::count();       // Should return: 4
App\Models\Inventory::count();      // Should return: 10
App\Models\Customer::count();       // Should return: 24
App\Models\Device::count();         // Should return: 30

# Check relationships
$product = App\Models\Product::with('category')->first();
echo $product->product_name;         // Should show product name
echo $product->category->name;       // Should show category name

# Check computed attributes
$inventory = App\Models\Inventory::first();
echo $inventory->available;          // qty - reserved
echo $inventory->stock_status;       // 'out'|'low'|'ok'
```

## File Structure

```
app/
  Models/               # 17 Eloquent models
  Http/
    Controllers/       # ProductController, CategoryController, InventoryController
    Requests/         # FormRequests for validation
    Resources/        # API Resources (JSON responses)
  Services/           # ProductService, CategoryService, InventoryService

database/
  migrations/         # 17 migrations (tables)
  seeders/           # 16 seeders (populate test data)

routes/
  web.php            # Web routes (Blade views)
  api.php            # API routes (JSON responses)
```

## Common Commands

```bash
# Reset database and reseed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status

# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName -m  # -m creates migration

# Create controller
php artisan make:controller ControllerName -r  # -r creates CRUD methods
```

## Troubleshooting

**Migration errors**: Delete `database.sqlite` file and run `php artisan migrate:fresh --seed`

**Seeder issues**: Ensure migrations ran successfully first. Check `php artisan migrate:status`

**Database connection error**: Verify `.env` database settings match your MySQL configuration

**API not working**: Ensure `routes/api.php` is registered in `config/app.php` or `bootstrap/app.php`
