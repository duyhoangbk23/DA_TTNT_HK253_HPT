<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Telemetry;
use Database\Seeders\ConnectedTelemetryDemoSeeder;
use Tests\TestCase;

class ConnectedTelemetryDemoSeederTest extends TestCase
{
    public function test_demo_mcu_is_connected_to_a_product_contract_customer_and_telemetry(): void
    {
        $seeder = new ConnectedTelemetryDemoSeeder();
        $seeder->run();
        $seeder->run();

        $device = Device::query()
            ->with(['product', 'customer', 'contract', 'mcu'])
            ->where('mcu_id', 'MCU-DEMO-001')
            ->firstOrFail();

        $this->assertSame('SP-DEMO-001', $device->product->product_code);
        $this->assertSame('KH-DEMO-001', $device->customer->customer_code);
        $this->assertSame('HD-DEMO-001', $device->contract->contract_code);
        $this->assertSame('CONNECTED', $device->mcu->connection_status);
        $this->assertSame(3, Telemetry::query()->where('mcu_id', 'MCU-DEMO-001')->count());
    }
}
