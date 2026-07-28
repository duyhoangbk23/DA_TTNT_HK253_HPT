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
            RoleSeeder::class,
            EmployeeSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            InventorySeeder::class,
            BatchSeeder::class,
            BatchDetailSeeder::class,
            CustomerSeeder::class,
            ContractSeeder::class,
            ContractServiceSeeder::class,
            McuSeeder::class,
            UnusedMcuSeeder::class,
            DeviceSeeder::class,
            ConnectedTelemetryDemoSeeder::class,
            UnusedDeviceSeeder::class,
            DeviceDashboardDataSeeder::class,
            MaintenanceRecordSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
