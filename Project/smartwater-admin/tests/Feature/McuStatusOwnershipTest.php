<?php

namespace Tests\Feature;

use App\Models\Mcu;
use App\Services\McuService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class McuStatusOwnershipTest extends TestCase
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

        Schema::connection('sqlite')->create('mcus', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('mcu_id')->unique();
            $table->string('serial_number')->unique();
            $table->string('firmware_version')->nullable();
            $table->string('api_key')->nullable();
            $table->string('status')->default('offline');
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });
        Schema::connection('sqlite')->create('devices', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('device_code');
            $table->unsignedInteger('contract_id')->nullable();
            $table->string('mcu_id')->nullable();
            $table->timestamp('replaced_at')->nullable();
        });
    }

    public function test_manager_payload_cannot_set_or_change_mcu_status(): void
    {
        $service = app(McuService::class);
        $mcu = $service->createMcu([
            'mcu_id' => '000123',
            'serial_number' => 'SN-123456',
            'firmware_version' => '1.0.0',
            'status' => 'online',
        ]);

        $this->assertSame('offline', $mcu->fresh()->status);

        $updated = $service->updateMcu($mcu->id, [
            'mcu_id' => '000123',
            'serial_number' => 'SN-123456',
            'firmware_version' => '1.1.0',
            'status' => 'error',
        ]);

        $this->assertSame('offline', $updated->fresh()->status);
        $this->assertSame('1.1.0', $updated->fresh()->firmware_version);
    }

    public function test_mcu_management_page_shows_status_without_status_input(): void
    {
        Mcu::create([
            'mcu_id' => 'ESP32_BACKEND',
            'serial_number' => 'SN-654321',
            'status' => 'online',
        ]);

        $response = $this->withoutMiddleware()->get('/mcus');

        $response->assertOk();
        $response->assertSee('Online');
        $response->assertDontSee('name="status"', false);
    }
}
