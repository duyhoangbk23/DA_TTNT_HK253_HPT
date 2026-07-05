<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            ['Đăng nhập hệ thống', 'Auth'],
            ['Tạo hợp đồng mới', 'Hợp đồng'],
            ['Cập nhật thông tin khách hàng', 'Khách hàng'],
            ['Hoàn thành phiếu bảo trì', 'Bảo trì'],
            ['Nhập kho lô hàng mới', 'Kho'],
            ['Kích hoạt thiết bị', 'Thiết bị'],
            ['Xuất báo cáo tháng', 'Báo cáo'],
            ['Thêm nhân viên mới', 'Nhân viên'],
        ];
        $users = User::all();

        for ($i = 0; $i < 25; $i++) {
            $action = $actions[$i % count($actions)];
            $user = $users[$i % $users->count()];

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action[0],
                'module' => $action[1],
                'description' => $action[0] . ' - ' . $action[1] . ' bởi ' . $user->username,
                'ip_address' => '192.168.1.' . (10 + $i),
                'created_at' => now()->subMinutes($i * 47),
            ]);
        }
    }
}
