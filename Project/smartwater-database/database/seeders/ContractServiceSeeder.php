<?php

namespace Database\Seeders;

use App\Models\ContractService;
use Illuminate\Database\Seeder;

class ContractServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['Vệ sinh & thay lõi lọc số 1-2-3', 90, 'Bảo trì định kỳ 3 tháng/lần'],
            ['Kiểm tra màng RO', 180, 'Kiểm tra & vệ sinh màng lọc RO'],
            ['Thay lõi lọc chức năng', 365, 'Thay lõi Mineral / Alkaline'],
        ];

        foreach ($services as $s) {
            ContractService::create([
                'service_name' => $s[0],
                'service_interval' => $s[1],
                'description' => $s[2],
            ]);
        }
    }
}
