<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Batch;
use App\Models\Mcu;
use App\Models\Contract;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['active', 'active', 'active', 'maintenance', 'error', 'pending'];
        $products = Product::whereIn('category_id', [1, 2, 3])->get();
        $customers = Customer::all();
        $batches = Batch::all();
        $mcus = Mcu::all();
        $contracts = Contract::all();

        for ($i = 0; $i < 30; $i++) {
            $product = $products[$i % $products->count()];
            $customer = $customers[$i % $customers->count()];
            $batch = $batches[$i % $batches->count()];
            $mcu = $mcus[$i % $mcus->count()];
            $contract = $contracts[$i % $contracts->count()];

            Device::create([
                'device_code' => sprintf('TB-%05d', 1000 + $i),
                'serial_number' => sprintf('SN-%s-%04d', strtoupper(substr($product->model, 0, 4)), 2500 + $i),
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'contract_id' => $contract->id,
                'batch_id' => $batch->id,
                'mcu_id' => $mcu->mcu_id,
                'import_date' => now()->subDays(30 + $i * 5)->format('Y-m-d'),
                'install_date' => now()->subDays(20 + $i * 4)->format('Y-m-d'),
                'firmware_version' => 'v' . (1 + $i % 3) . '.' . ($i % 10) . '.' . ($i % 5),
                'location' => $customer->address,
                'status' => $statuses[$i % count($statuses)],
            ]);
        }
    }
}
