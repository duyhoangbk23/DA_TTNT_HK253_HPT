<?php

namespace Database\Seeders;

use App\Models\BatchDetail;
use App\Models\Batch;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BatchDetailSeeder extends Seeder
{
    public function run(): void
    {
        $batches = Batch::all();
        $products = Product::whereIn('category_id', [1, 2, 3])->take(5)->get();

        foreach ($batches as $batch) {
            foreach ($products as $i => $product) {
                BatchDetail::create([
                    'batch_id' => $batch->id,
                    'product_id' => $product->id,
                    'quantity' => (($batch->id + $i) % 5 + 1) * 8,
                    'unit_cost' => intval($product->price * 0.7),
                ]);
            }
        }
    }
}
