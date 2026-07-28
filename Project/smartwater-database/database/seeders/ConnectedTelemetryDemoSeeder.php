<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Mcu;
use App\Models\Product;
use App\Models\Telemetry;
use Illuminate\Database\Seeder;

class ConnectedTelemetryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $customer = Customer::updateOrCreate(
            ['customer_code' => 'KH-DEMO-001'],
            [
                'customer_name' => 'Demo Water Services',
                'phone' => '0900 000 001',
                'email' => 'demo.customer@smartwater.local',
                'address' => '123 Demo Street, Ho Chi Minh City',
                'type' => 'company',
                'status' => 'active',
                'joined_at' => $now->toDateString(),
            ]
        );

        $category = Category::firstOrCreate(
            ['name' => 'Demo Water Filter'],
            ['description' => 'Product used by the connected telemetry demo.', 'status' => 'active']
        );

        $product = Product::updateOrCreate(
            ['product_code' => 'SP-DEMO-001'],
            [
                'product_name' => 'SmartWater Demo Filter',
                'category_id' => $category->id,
                'model' => 'SW-DEMO-100',
                'capacity' => '100 L/h',
                'unit' => 'Unit',
                'price' => 5_000_000,
                'status' => 'active',
            ]
        );

        $contract = Contract::updateOrCreate(
            ['contract_code' => 'HD-DEMO-001'],
            [
                'customer_id' => $customer->id,
                'contract_type' => 'install',
                'start_date' => $now->copy()->subMonth()->toDateString(),
                'install_date' => $now->copy()->subWeeks(3)->toDateString(),
                'end_date' => $now->copy()->addYear()->toDateString(),
                'maintenance_cycle_months' => 6,
                'amount' => 5_000_000,
                'status' => 'active',
            ]
        );

        $mcu = Mcu::updateOrCreate(
            ['mcu_id' => 'MCU-DEMO-001'],
            [
                'serial_number' => 'SN-DEMO-000001',
                'firmware_version' => 'v1.0.0-demo',
                'api_key' => 'demo-mcu-api-key-000000000000000000000001',
                'status' => 'online',
                'last_connected_at' => $now,
                'registration_status' => 'REGISTERED',
                'connection_status' => 'CONNECTED',
                'first_seen_at' => $now->copy()->subHours(2),
                'last_seen_at' => $now,
            ]
        );

        Device::updateOrCreate(
            ['device_code' => 'TB-DEMO-001'],
            [
                'serial_number' => 'SN-DEVICE-DEMO-001',
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'contract_id' => $contract->id,
                'mcu_id' => $mcu->mcu_id,
                'import_date' => $now->copy()->subMonth()->toDateString(),
                'install_date' => $now->copy()->subWeeks(3)->toDateString(),
                'firmware_version' => 'v1.0.0-demo',
                'location' => $customer->address,
                'status' => 'active',
            ]
        );

        Telemetry::query()->where('mcu_id', $mcu->mcu_id)->delete();

        foreach ([
            ['timestamp' => $now->copy()->subMinutes(10), 'tds' => 118.50, 'alert' => 'normal'],
            ['timestamp' => $now->copy()->subMinutes(5), 'tds' => 120.25, 'alert' => 'normal'],
            ['timestamp' => $now, 'tds' => 119.75, 'alert' => 'normal'],
        ] as $reading) {
            Telemetry::create([
                'mcu_id' => $mcu->mcu_id,
                'timestamp' => $reading['timestamp']->format('Y-m-d H:i:s'),
                'topic' => 'devices/telemetry',
                'tds' => $reading['tds'],
                'alert' => $reading['alert'],
            ]);
        }
    }
}
