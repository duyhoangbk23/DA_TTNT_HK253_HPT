<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Product;
use Illuminate\Database\Seeder;

class UnusedDeviceSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::whereIn('category_id', [1, 2, 3])->get();
        $statuses = ['pending', 'pending', 'pending', 'pending', 'active', 'maintenance'];

        for ($i = 0; $i < 15; $i++) {
            $product = $products[$i % $products->count()];

            Device::create([
                'device_code' => sprintf('TB-%04d', 5001 + $i),
                'serial_number' => sprintf('SN-%s-%04d', strtoupper(substr($product->model, 0, 4)), 9000 + $i),
                'product_id' => $product->id,
                'customer_id' => null,
                'contract_id' => null,
                'batch_id' => null,
                'mcu_id' => null,
                'import_date' => now()->subDays(5 + $i * 2)->format('Y-m-d'),
                'install_date' => null,
                'firmware_version' => 'v1.0.0',
                'location' => null,
                'status' => $statuses[$i % count($statuses)],
            ]);
        }
    }
}
