# Remove Runtime Mock Data Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove runtime `MockData` usage so SmartWater Admin renders persisted activity data and uses seeders only for intentional demo records.

**Architecture:** `ActivityController` will query `ActivityLog` with `user.employee`, apply server-side search and Laravel pagination, then pass Eloquent records to the Blade view. The global mock view composer and `MockData` class will be removed; authenticated-user display remains owned by Laravel `Auth`.

**Tech Stack:** PHP 8.1+, Laravel, Eloquent, Blade, PHPUnit, SQLite in-memory tests, MySQL production schema.

## Global Constraints

- Runtime requests must never fabricate sample records or fall back to sample data.
- Demo records must be persisted by seeders owned by `Project/smartwater-database`.
- The Admin application must not create or alter database schema.
- Preserve the existing safe database-unavailable response behavior.
- Do not modify telemetry or maintenance workflows.

---

### Task 1: Render Activity Log from the database

**Files:**
- Create: `Project/smartwater-admin/tests/Feature/ActivityLogDatabaseTest.php`
- Modify: `Project/smartwater-admin/app/Http/Controllers/ActivityController.php`
- Modify: `Project/smartwater-admin/resources/views/activities/index.blade.php`

**Interfaces:**
- Consumes: `ActivityLog::user(): BelongsTo`, `User::employee(): BelongsTo`, query parameter `q`.
- Produces: view variable `activities` as `LengthAwarePaginator<ActivityLog>` ordered by `created_at DESC` with 25 rows per page.

- [ ] **Step 1: Write the failing database-rendering test**

Create this SQLite-backed feature test. The helper writes complete persisted relationships without mocks:

```php
<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ActivityLogDatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        view()->share('errors', new ViewErrorBag());

        Schema::connection('sqlite')->create('employees', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('full_name');
            $table->string('avatar_path')->nullable();
        });
        Schema::connection('sqlite')->create('users', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('username');
            $table->string('email');
            $table->unsignedInteger('employee_id')->nullable();
            $table->string('avatar_path')->nullable();
        });
        Schema::connection('sqlite')->create('activity_logs', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('action');
            $table->string('module');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at');
        });
    }

    private function insertUserAndActivity(string $action, string $createdAt, int $id = 1): void
    {
        DB::table('employees')->insert([
            'id' => $id,
            'full_name' => $id === 1 ? 'Nhân viên Database' : 'Nhân viên Database ' . $id,
            'avatar_path' => null,
        ]);
        DB::table('users')->insert([
            'id' => $id,
            'username' => 'db-user-' . $id,
            'email' => 'db-user-' . $id . '@smartwater.test',
            'employee_id' => $id,
            'avatar_path' => null,
        ]);
        DB::table('activity_logs')->insert([
            'user_id' => $id,
            'action' => $action,
            'module' => 'Thiết bị',
            'description' => 'Bản ghi được lưu trong database',
            'ip_address' => '127.0.0.1',
            'created_at' => $createdAt,
        ]);
    }

public function test_activity_page_renders_only_persisted_database_records(): void
{
    $this->insertUserAndActivity('DB_REAL_ACTIVITY_001', '2026-07-29 11:00:00');

    $response = $this->withoutMiddleware()->get('/activities');

    $response->assertOk();
    $response->assertSee('DB_REAL_ACTIVITY_001');
    $response->assertSee('Nhân viên Database');
    $response->assertDontSee('Tạo hợp đồng mới');
    $response->assertViewHas('activities', fn ($rows) => $rows->total() === 1);
}
}
```

- [ ] **Step 2: Run the test and verify the RED state**

Run:

```powershell
cd Project\smartwater-admin
php artisan test tests/Feature/ActivityLogDatabaseTest.php --filter=test_activity_page_renders_only_persisted_database_records
```

Expected: FAIL because `ActivityController` still returns `MockData::activities()` and does not include `DB_REAL_ACTIVITY_001`.

- [ ] **Step 3: Implement the database query**

Replace the runtime mock call with the minimal real Eloquent query. Search is intentionally not added in this cycle:

```php
public function index()
{
    $activities = ActivityLog::query()
        ->with(['user.employee'])
        ->latest('created_at')
        ->paginate(25)
        ->withQueryString();

    return view('activities.index', [
        'activities' => $activities,
        'search' => '',
    ]);
}
```

Update the Blade table to consume Eloquent properties. Resolve the display name in this order: `user.employee.full_name`, `user.username`, `user.email`, then `Hệ thống`. Render `Storage::url($avatarPath)` only when the public disk contains the path; otherwise render a Bootstrap person icon. Add an `@empty` row and paginator links.

- [ ] **Step 4: Run the database-rendering test and verify GREEN**

Run:

```powershell
php artisan test tests/Feature/ActivityLogDatabaseTest.php --filter=test_activity_page_renders_only_persisted_database_records
```

Expected: PASS.

- [ ] **Step 5: Write the failing server-side search test**

Insert two persisted activities named `MATCH_DATABASE_ACTIVITY` and `HIDDEN_DATABASE_ACTIVITY`, request `/activities?q=MATCH_DATABASE`, and assert only the matching row is rendered and the query is retained by the paginator.

```php
public function test_activity_search_filters_persisted_records(): void
{
    $this->insertUserAndActivity('MATCH_DATABASE_ACTIVITY', '2026-07-29 12:00:00');
    $this->insertUserAndActivity('HIDDEN_DATABASE_ACTIVITY', '2026-07-29 11:00:00', 2);

    $response = $this->withoutMiddleware()->get('/activities?q=MATCH_DATABASE');

    $response->assertOk();
    $response->assertSee('MATCH_DATABASE_ACTIVITY');
    $response->assertDontSee('HIDDEN_DATABASE_ACTIVITY');
    $response->assertViewHas('activities', fn ($rows) => $rows->total() === 1);
}
```

- [ ] **Step 6: Run the search test and verify the RED state**

Run:

```powershell
php artisan test tests/Feature/ActivityLogDatabaseTest.php --filter=test_activity_search_filters_persisted_records
```

Expected: FAIL because the minimal controller from Step 3 does not filter by `q` and returns both persisted rows.

- [ ] **Step 7: Connect the search form to the database query**

Change `index()` to accept `Request $request`, import `Illuminate\Database\Eloquent\Builder` and `Illuminate\Http\Request`, and insert this `when()` block before `latest('created_at')`:

```php
$search = trim((string) $request->query('q', ''));

->when($search !== '', function (Builder $query) use ($search): void {
    $query->where(function (Builder $query) use ($search): void {
        $query->where('action', 'like', "%{$search}%")
            ->orWhere('module', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhereHas('user', function (Builder $query) use ($search): void {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('employee', fn (Builder $query) => $query->where('full_name', 'like', "%{$search}%"));
            });
    });
})
```

Pass both `$activities` and `$search` to the view. Replace the DataTables-only input with a GET form:

```blade
<form method="GET" action="{{ route('activities.index') }}" class="d-flex gap-2">
    <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm"
           placeholder="Tìm hoạt động...">
    <button type="submit" class="btn btn-sm btn-primary">Tìm</button>
</form>
```

Remove `data-datatable` and `data-dt-search` from this server-paginated table so the browser does not apply a second, incomplete filter to the current page.

- [ ] **Step 8: Run the complete feature test**

Run:

```powershell
php artisan test tests/Feature/ActivityLogDatabaseTest.php
```

Expected: 2 tests PASS.

- [ ] **Step 9: Commit Task 1**

```powershell
git add Project/smartwater-admin/tests/Feature/ActivityLogDatabaseTest.php Project/smartwater-admin/app/Http/Controllers/ActivityController.php Project/smartwater-admin/resources/views/activities/index.blade.php
git commit -m "feat: load activity history from database"
```

---

### Task 2: Remove the runtime mock provider and class

**Files:**
- Modify: `Project/smartwater-admin/app/Providers/AppServiceProvider.php`
- Delete: `Project/smartwater-admin/app/Support/MockData.php`
- Verify: `Project/smartwater-database/database/seeders/DatabaseSeeder.php`
- Verify: `Project/smartwater-database/database/seeders/ActivityLogSeeder.php`

**Interfaces:**
- Consumes: Laravel `Auth` already used by `resources/views/partials/navbar.blade.php`.
- Produces: no global `currentUser` or `navNotifications` mock variables; no `App\Support\MockData` class.

- [ ] **Step 1: Remove the mock view composer**

Remove the `MockData` import and the `View::composer('layouts.*', ...)` callback. Leave `boot()` empty because the navbar already reads the authenticated user through `Auth::user()`.

- [ ] **Step 2: Delete the unused mock source**

Delete `Project/smartwater-admin/app/Support/MockData.php` after `ActivityController` and `AppServiceProvider` no longer reference it.

- [ ] **Step 3: Verify the database seeder path**

Confirm `DatabaseSeeder` invokes `UserSeeder` before `ActivityLogSeeder` and that `ActivityLogSeeder` writes through `ActivityLog::create()`. Do not add migrations or seeders to `smartwater-admin`.

- [ ] **Step 4: Verify no runtime mock references remain**

Run:

```powershell
rg -n "MockData|Support\\MockData" Project/smartwater-admin/app Project/smartwater-admin/resources Project/smartwater-admin/routes
```

Expected: no matches.

- [ ] **Step 5: Run focused regression tests and compilation checks**

Run:

```powershell
cd Project\smartwater-admin
php artisan test tests/Feature/ActivityLogDatabaseTest.php tests/Feature/DatabaseUnavailableResponseTest.php
php -l app/Http/Controllers/ActivityController.php
php -l app/Providers/AppServiceProvider.php
php artisan view:clear
php artisan view:cache
cd ..\..
git diff --check
```

Expected: tests PASS, PHP reports no syntax errors, Blade templates cache successfully, and `git diff --check` exits 0.

- [ ] **Step 6: Commit Task 2**

```powershell
git add Project/smartwater-admin/app/Providers/AppServiceProvider.php Project/smartwater-admin/app/Support/MockData.php
git commit -m "refactor: remove runtime mock data"
```
