<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['Công ty TNHH Kỹ thuật nước Việt', 'Trần Minh Khoa', '0901 234 567', 'sales@ktnvit.vn', 'KCN Tân Bình, TP.HCM'],
            ['Aqua Components JSC', 'Nguyễn Thu Hà', '0902 345 678', 'contact@aquacomp.vn', 'Bắc Ninh'],
            ['Global Water Supply', 'Lê Quốc Bảo', '0903 456 789', 'info@gws.com.vn', 'Bình Dương'],
        ];

        foreach ($suppliers as $s) {
            Supplier::create([
                'supplier_name' => $s[0],
                'contact_person' => $s[1],
                'phone' => $s[2],
                'email' => $s[3],
                'address' => $s[4],
            ]);
        }
    }
}
