<?php

namespace Database\Seeders;

use App\Models\Batch;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $batches = [
            ['LOT-2025-001', 1, '2025-01-15', '2028-01-15', 120],
            ['LOT-2025-002', 2, '2025-02-02', '2028-02-02', 80],
            ['LOT-2025-003', 3, '2025-03-20', '2027-03-20', 300],
            ['LOT-2025-004', 1, '2025-04-11', '2028-04-11', 60],
            ['LOT-2025-005', 2, '2025-05-05', '2028-05-05', 150],
            ['LOT-2025-006', 3, '2025-05-28', '2027-05-28', 45],
        ];

        foreach ($batches as $b) {
            Batch::create([
                'batch_code' => $b[0],
                'supplier_id' => $b[1],
                'import_date' => $b[2],
                'expiry_date' => $b[3],
                'quantity' => $b[4],
                'note' => 'Nhập kho định kỳ theo hợp đồng cung ứng.',
            ]);
        }
    }
}
