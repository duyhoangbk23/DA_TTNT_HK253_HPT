<?php

namespace Database\Seeders;

use App\Models\MaintenanceRecord;
use App\Models\Device;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class MaintenanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['routine', 'repair', 'replace'];
        $statuses = ['completed', 'completed', 'pending'];
        $devices = Device::all();
        $technicians = Employee::where('role_id', 3)->get();

        // Generate 60+ maintenance records with distribution across months for better dashboard charts
        for ($i = 0; $i < 60; $i++) {
            $device = $devices[$i % $devices->count()];
            $technician = $technicians[$i % $technicians->count()];
            $typeKey = $types[$i % 3];
            
            // Distribute records across months and days for better visualization
            $month = ($i % 12) + 1; // Months 1-12
            $dayOfMonth = (($i / 12) % 28) + 1; // Days 1-28
            
            MaintenanceRecord::create([
                'maintenance_code' => sprintf('BT-%04d', $i + 1),
                'device_id' => $device->id,
                'employee_id' => $technician->id,
                'maintenance_date' => now()->year(now()->year)->month($month)->day($dayOfMonth)->format('Y-m-d'),
                'maintenance_type' => $typeKey,
                'description' => match($typeKey) {
                    'routine' => 'Vệ sinh thiết bị, kiểm tra & thay lõi lọc theo lịch.',
                    'repair' => 'Sửa chữa lỗi: ' . ($i % 2 === 0 ? 'Rò nước' : 'Máy không khởi động'),
                    'replace' => 'Thay thế linh kiện hỏng: ' . ($i % 3 === 0 ? 'Bơm' : ($i % 3 === 1 ? 'Van điều' : 'Cảm biến')),
                },
                'parts_used' => match($typeKey) {
                    'routine' => 'Lõi PP, Lõi Carbon',
                    'repair' => 'Sealant, Keo dán',
                    'replace' => $i % 2 === 0 ? 'Máy bơm 3000l/h' : 'Màng RO 75GPD',
                },
                'cost' => match($typeKey) {
                    'routine' => (2 + $i % 3) * 200_000,
                    'repair' => (3 + $i % 4) * 300_000,
                    'replace' => (5 + $i % 5) * 400_000,
                },
                'status' => $statuses[$i % count($statuses)],
            ]);
        }
    }
}
