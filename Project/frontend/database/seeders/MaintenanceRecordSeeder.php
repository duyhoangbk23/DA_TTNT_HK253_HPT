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

        for ($i = 0; $i < 20; $i++) {
            $device = $devices[$i % $devices->count()];
            $technician = $technicians[$i % $technicians->count()];
            $typeKey = $types[$i % 3];

            MaintenanceRecord::create([
                'maintenance_code' => sprintf('BT-%04d', $i + 1),
                'device_id' => $device->id,
                'employee_id' => $technician->id,
                'maintenance_date' => now()->subDays($i * 3)->format('Y-m-d'),
                'maintenance_type' => $typeKey,
                'description' => 'Vệ sinh thiết bị, kiểm tra & thay lõi lọc theo lịch.',
                'parts_used' => $i % 2 === 0 ? 'Lõi PP, Lõi Carbon' : 'Màng RO 75GPD',
                'cost' => (2 + $i % 5) * 250_000,
                'status' => $statuses[$i % count($statuses)],
            ]);
        }
    }
}
