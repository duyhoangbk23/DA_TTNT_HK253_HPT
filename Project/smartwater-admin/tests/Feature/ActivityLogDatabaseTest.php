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

    public function test_activity_search_filters_persisted_records(): void
    {
        $this->insertUserAndActivity('MATCH_DATABASE_ACTIVITY', '2026-07-29 12:00:00');
        $this->insertUserAndActivity('HIDDEN_DATABASE_ACTIVITY', '2026-07-29 11:00:00', 2);

        $response = $this->withoutMiddleware()->get('/activities?q=MATCH_DATABASE');

        $response->assertOk();
        $response->assertSee('MATCH_DATABASE_ACTIVITY');
        $response->assertDontSee('HIDDEN_DATABASE_ACTIVITY');
        $response->assertSee('value="MATCH_DATABASE"', false);
        $response->assertViewHas('activities', fn ($rows) => $rows->total() === 1);
    }

    public function test_activity_page_shows_empty_state_when_database_has_no_records(): void
    {
        $this->withoutMiddleware()
            ->get('/activities')
            ->assertOk()
            ->assertSee('Chưa có lịch sử hoạt động.');
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
}
