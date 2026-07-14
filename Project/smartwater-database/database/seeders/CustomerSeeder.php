<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Hoàng Cường', 'Phạm Thu Dung', 'Võ Minh Đức',
            'Đặng Thị Hoa', 'Bùi Quang Huy', 'Công ty CP Sài Gòn Food', 'Trường Mầm non Hoa Sen', 'Hồ Thị Lan',
            'Ngô Bá Khánh', 'Khách sạn Bình Minh', 'Dương Thị Mai', 'Đỗ Văn Nam', 'Nhà hàng Phố Biển',
            'Lý Thị Oanh', 'Trịnh Công Phúc', 'Chung cư Green Park', 'Vũ Thị Quỳnh', 'Mai Văn Sơn',
            'Phan Thị Trang', 'Cao Minh Tuấn', 'Bệnh viện Đa khoa An Sinh', 'Đinh Thị Vân',
        ];
        $districts = ['Quận 1', 'Quận 3', 'Quận 7', 'Quận Bình Thạnh', 'Quận Tân Bình', 'TP. Thủ Đức', 'Quận Gò Vấp'];

        foreach ($names as $i => $name) {
            $isCompany = str_contains($name, 'Công ty') || str_contains($name, 'Trường')
                || str_contains($name, 'Khách sạn') || str_contains($name, 'Nhà hàng')
                || str_contains($name, 'Chung cư') || str_contains($name, 'Bệnh viện');

            Customer::create([
                'customer_code' => sprintf('KH-%04d', $i + 1),
                'customer_name' => $name,
                'phone' => sprintf('09%02d %03d %03d', $i + 10, 100 + $i, 200 + $i),
                'email' => 'kh' . ($i + 1) . '@email.com',
                'address' => sprintf('%d Nguyễn Trãi, %s, TP.HCM', 12 + $i, $districts[$i % count($districts)]),
                'avatar_path' => 'https://i.pravatar.cc/120?img=' . (($i % 60) + 1),
                'type' => $isCompany ? 'company' : 'individual',
                'status' => ($i % 8 === 0) ? 'inactive' : 'active',
                'joined_at' => now()->subDays(($i + 1) * 9)->format('Y-m-d'),
            ]);
        }
    }
}
