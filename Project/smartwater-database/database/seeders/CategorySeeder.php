<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Máy lọc nước RO', 'Dòng máy lọc thẩm thấu ngược'],
            ['Máy lọc nước Nano', 'Công nghệ lọc Nano tiết kiệm điện'],
            ['Máy lọc nước công nghiệp', 'Công suất lớn cho nhà máy, tòa nhà'],
            ['Lõi lọc & Phụ kiện', 'Lõi lọc thay thế và linh kiện'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat[0],
                'description' => $cat[1],
                'status' => 'active',
            ]);
        }
    }
}
