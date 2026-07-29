<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MaintenanceWorkOrderRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('maintenance_work_orders', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('device_id')->nullable();
            $table->unsignedInteger('contract_id')->nullable();
            $table->unsignedInteger('employee_id')->nullable();
            $table->string('type');
            $table->string('status');
            $table->timestamps();
        });
        Schema::connection('sqlite')->create('employees', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('full_name');
            $table->string('status');
        });
        Schema::connection('sqlite')->create('devices', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('device_code');
            $table->string('mcu_id')->nullable();
            $table->string('location')->nullable();
            $table->string('status');
            $table->timestamp('replaced_at')->nullable();
        });
    }

    public function test_maintenance_work_orders_require_authentication(): void
    {
        $this->get('/maintenance-work-orders')->assertRedirect('/login');
    }

    public function test_maintenance_page_is_named_bao_tri_and_lists_only_faulted_devices(): void
    {
        DB::table('devices')->insert([
            ['device_code' => 'TB-ERROR-001', 'mcu_id' => 'ESP32_ERROR', 'location' => 'Khu A', 'status' => 'error', 'replaced_at' => null],
            ['device_code' => 'TB-ACTIVE-001', 'mcu_id' => 'ESP32_ACTIVE', 'location' => 'Khu B', 'status' => 'active', 'replaced_at' => null],
        ]);

        $response = $this->withoutMiddleware()->get('/maintenance-work-orders');

        $response->assertOk();
        $response->assertSee('Bảo trì');
        $response->assertSee('TB-ERROR-001');
        $response->assertDontSee('TB-ACTIVE-001');
        $response->assertViewHas('errorDevices', fn ($devices) => $devices->count() === 1 && $devices->first()->status === 'error');
    }
}
