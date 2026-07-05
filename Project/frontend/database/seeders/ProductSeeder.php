<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['AQ-RO-50', 'AquaPure RO 50', 1, 'APRO-50', '50 L/h', 'active', 'AQ-RO-50.jpg'],
            ['AQ-RO-75', 'AquaPure RO 75', 1, 'APRO-75', '75 L/h', 'active', 'AQ-RO-75.jpg'],
            ['AQ-RO-100', 'AquaPure RO 100', 1, 'APRO-100', '100 L/h', 'active', 'AQ-RO-100.jpg'],
            ['AQ-NANO-30', 'AquaPure Nano 30', 2, 'APNA-30', '30 L/h', 'active', 'AQ-NANO-30.jpg'],
            ['AQ-NANO-45', 'AquaPure Nano 45', 2, 'APNA-45', '45 L/h', 'maintenance', 'AQ-NANO-45.jpg'],
            ['AQ-IND-500', 'AquaPure Industrial 500', 3, 'APIN-500', '500 L/h', 'active', 'AQ-IND-500.jpg'],
            ['AQ-IND-1000', 'AquaPure Industrial 1000', 3, 'APIN-1000', '1000 L/h', 'active', 'AQ-IND-1000.jpg'],
            ['AQ-CORE-01', 'Lõi lọc Sediment PP', 4, 'CORE-01', '-', 'active', 'AQ-CORE-01.jpg'],
            ['AQ-CORE-02', 'Lõi lọc Carbon GAC', 4, 'CORE-02', '-', 'active', 'AQ-CORE-02.jpg'],
            ['AQ-CORE-RO', 'Màng lọc RO 75GPD', 4, 'CORE-RO', '-', 'inactive', 'AQ-CORE-RO.jpg'],
        ];

        foreach ($products as $i => $p) {
            Product::create([
                'product_code' => $p[0],
                'product_name' => $p[1],
                'category_id' => $p[2],
                'model' => $p[3],
                'capacity' => $p[4],
                'unit' => 'Chiếc',
                'price' => 3_200_000 + $i * 850_000,
                'image_path' => $p[6],
                'status' => $p[5],
            ]);
        }
    }
}
