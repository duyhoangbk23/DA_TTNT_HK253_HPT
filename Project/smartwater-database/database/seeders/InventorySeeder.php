<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $quantities = [42, 18, 7, 65, 3, 12, 5, 210, 180, 0];
        $reserved = [8, 2, 1, 10, 0, 3, 1, 40, 30, 0];

        $products = Product::all();
        foreach ($products as $i => $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => $quantities[$i] ?? 10,
                'reserved_quantity' => $reserved[$i] ?? 2,
                'unit_cost' => intval($product->price * 0.7),
                'last_updated' => now()->subDays($i),
            ]);
        }
    }
}
