<?php

namespace Tests\Unit;

use App\Services\MaintenanceWorkOrderService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MaintenanceWorkOrderServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('contracts', function (Blueprint $table): void {
            $table->increments('id');
            $table->date('install_date')->nullable();
            $table->date('end_date');
            $table->unsignedSmallInteger('maintenance_cycle_months')->nullable();
            $table->string('status')->default('active');
        });
        Schema::connection('sqlite')->create('devices', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('contract_id')->nullable();
            $table->string('mcu_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('replaced_at')->nullable();
        });
        Schema::connection('sqlite')->create('telemetry', function (Blueprint $table): void {
            $table->increments('id');
            $table->dateTime('timestamp');
            $table->string('topic');
            $table->string('mcu_id');
            $table->float('tds')->nullable();
            $table->string('alert')->nullable();
        });
        Schema::connection('sqlite')->create('maintenance_work_orders', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('device_id');
            $table->unsignedInteger('contract_id')->nullable();
            $table->string('type');
            $table->string('source_alert')->nullable();
            $table->string('priority');
            $table->string('status');
            $table->date('scheduled_for')->nullable();
            $table->dateTime('triggered_at')->nullable();
            $table->text('telemetry_snapshot')->nullable();
            $table->string('open_key')->nullable()->unique();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_a_due_order_from_the_contract_install_date(): void
    {
        DB::table('contracts')->insert([
            'id' => 1,
            'install_date' => '2026-01-29',
            'end_date' => '2027-01-29',
            'maintenance_cycle_months' => 6,
            'status' => 'active',
        ]);
        DB::table('devices')->insert(['id' => 10, 'contract_id' => 1, 'mcu_id' => 'ESP32_001', 'status' => 'active']);

        $service = $this->service();
        $created = $service->synchronizeScheduled(Carbon::parse('2026-07-29'));

        $this->assertSame(1, $created);
        $this->assertDatabaseHas('maintenance_work_orders', [
            'device_id' => 10,
            'contract_id' => 1,
            'type' => 'scheduled',
            'priority' => 'normal',
            'status' => 'new',
            'scheduled_for' => '2026-07-29',
            'open_key' => 'scheduled:10',
        ]);
    }

    public function test_it_does_not_duplicate_an_open_scheduled_order(): void
    {
        DB::table('contracts')->insert([
            'id' => 1,
            'install_date' => '2026-01-29',
            'end_date' => '2027-01-29',
            'maintenance_cycle_months' => 6,
            'status' => 'active',
        ]);
        DB::table('devices')->insert(['id' => 10, 'contract_id' => 1, 'mcu_id' => 'ESP32_001', 'status' => 'active']);

        $service = $this->service();
        $service->synchronizeScheduled(Carbon::parse('2026-07-29'));
        $created = $service->synchronizeScheduled(Carbon::parse('2026-07-29'));

        $this->assertSame(0, $created);
        $this->assertDatabaseCount('maintenance_work_orders', 1);
    }

    public function test_it_creates_the_next_contract_due_order_after_the_previous_cycle_is_completed(): void
    {
        DB::table('contracts')->insert([
            'id' => 1,
            'install_date' => '2026-01-29',
            'end_date' => '2027-07-29',
            'maintenance_cycle_months' => 6,
            'status' => 'active',
        ]);
        DB::table('devices')->insert(['id' => 10, 'contract_id' => 1, 'mcu_id' => 'ESP32_001', 'status' => 'active']);
        DB::table('maintenance_work_orders')->insert([
            'device_id' => 10,
            'contract_id' => 1,
            'type' => 'scheduled',
            'priority' => 'normal',
            'status' => 'completed',
            'scheduled_for' => '2026-07-29',
            'completed_at' => '2026-07-30 08:00:00',
            'created_at' => '2026-07-22 00:00:00',
            'updated_at' => '2026-07-30 08:00:00',
        ]);

        $created = $this->service()->synchronizeScheduled(Carbon::parse('2027-01-22'));

        $this->assertSame(1, $created);
        $this->assertDatabaseHas('maintenance_work_orders', [
            'device_id' => 10,
            'type' => 'scheduled',
            'scheduled_for' => '2027-01-29',
            'open_key' => 'scheduled:10',
        ]);
    }

    public function test_it_creates_an_alert_order_without_replacing_the_scheduled_order(): void
    {
        DB::table('devices')->insert(['id' => 10, 'contract_id' => null, 'mcu_id' => 'ESP32_001', 'status' => 'active']);
        DB::table('maintenance_work_orders')->insert([
            'device_id' => 10,
            'type' => 'scheduled',
            'priority' => 'normal',
            'status' => 'new',
            'scheduled_for' => '2026-07-29',
            'open_key' => 'scheduled:10',
            'created_at' => '2026-07-29 00:00:00',
            'updated_at' => '2026-07-29 00:00:00',
        ]);
        DB::table('telemetry')->insert([
            'timestamp' => '2026-07-29 10:00:00',
            'topic' => 'devices/telemetry',
            'mcu_id' => 'ESP32_001',
            'tds' => null,
            'alert' => 'sensor_disconnected',
        ]);

        $created = $this->service()->synchronizeAlerts(Carbon::parse('2026-07-29 10:01:00'));

        $this->assertSame(1, $created);
        $this->assertDatabaseHas('maintenance_work_orders', [
            'device_id' => 10,
            'type' => 'alert',
            'source_alert' => 'sensor_disconnected',
            'priority' => 'critical',
            'status' => 'new',
            'open_key' => 'alert:10:sensor_disconnected',
        ]);
        $this->assertDatabaseCount('maintenance_work_orders', 2);
    }

    public function test_completing_an_order_keeps_the_contract_schedule_and_closes_only_that_order(): void
    {
        DB::table('contracts')->insert([
            'id' => 1,
            'install_date' => '2026-01-29',
            'end_date' => '2027-01-29',
            'maintenance_cycle_months' => 6,
            'status' => 'active',
        ]);
        DB::table('devices')->insert(['id' => 10, 'contract_id' => 1, 'mcu_id' => 'ESP32_001', 'status' => 'active']);
        DB::table('maintenance_work_orders')->insert([
            'id' => 1,
            'device_id' => 10,
            'contract_id' => 1,
            'type' => 'scheduled',
            'priority' => 'normal',
            'status' => 'in_progress',
            'scheduled_for' => '2026-07-29',
            'open_key' => 'scheduled:10',
            'created_at' => '2026-07-29 08:00:00',
            'updated_at' => '2026-07-29 08:00:00',
        ]);

        $service = $this->service();
        if (!method_exists($service, 'completeWorkOrder')) {
            $this->fail('Maintenance work-order completion has not been implemented.');
        }
        $service->completeWorkOrder(1, Carbon::parse('2026-07-29 11:00:00'));

        $this->assertDatabaseHas('maintenance_work_orders', [
            'id' => 1,
            'status' => 'completed',
            'open_key' => null,
            'completed_at' => '2026-07-29 11:00:00',
        ]);
        $this->assertSame('2026-01-29', DB::table('contracts')->where('id', 1)->value('install_date'));
        $this->assertSame(0, $service->synchronizeScheduled(Carbon::parse('2026-07-29')));
    }

    private function service(): MaintenanceWorkOrderService
    {
        if (!class_exists(MaintenanceWorkOrderService::class)) {
            $this->fail('Maintenance work-order service has not been implemented.');
        }

        return app(MaintenanceWorkOrderService::class);
    }
}
