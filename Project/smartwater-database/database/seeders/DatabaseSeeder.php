<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // Nhóm nền tảng phải tồn tại trước vì user và dữ liệu nghiệp vụ tham chiếu role/employee.
            RoleSeeder::class,
            EmployeeSeeder::class,
            UserSeeder::class,
            // Nhóm danh mục và kho tạo khóa cha trước batch detail, hợp đồng và thiết bị.
            CategorySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            InventorySeeder::class,
            BatchSeeder::class,
            BatchDetailSeeder::class,
            CustomerSeeder::class,
            ContractSeeder::class,
            ContractServiceSeeder::class,
            // MCU được seed trước thiết bị để devices.mcu_id luôn tham chiếu định danh chuỗi hợp lệ.
            McuSeeder::class,
            UnusedMcuSeeder::class,
            DeviceSeeder::class,
            // Telemetry, bảo trì và activity log được seed cuối vì phụ thuộc toàn bộ quan hệ phía trên.
            ConnectedTelemetryDemoSeeder::class,
            UnusedDeviceSeeder::class,
            DeviceDashboardDataSeeder::class,
            MaintenanceRecordSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
