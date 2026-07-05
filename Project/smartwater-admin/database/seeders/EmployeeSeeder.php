<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['Hoàng Anh Duy', 'Giám đốc kỹ thuật', 1, 'Administrator'],
            ['Nguyễn Thị Mỹ Linh', 'Trưởng phòng dịch vụ', 2, 'Employee'],
            ['Trần Văn Hùng', 'Kỹ thuật viên trưởng', 3, 'Technician'],
            ['Lê Thị Ngọc', 'Kỹ thuật viên', 3, 'Technician'],
            ['Phạm Quốc Việt', 'Kỹ thuật viên', 3, 'Technician'],
            ['Đỗ Thành Long', 'Nhân viên kho', 2, 'Employee'],
            ['Vũ Thị Hạnh', 'Chăm sóc khách hàng', 2, 'Employee'],
            ['Bùi Minh Tú', 'Kỹ thuật viên', 3, 'Technician'],
            ['Ngô Thị Thu', 'Chăm sóc khách hàng', 2, 'Employee'],
        ];

        foreach ($employees as $i => $emp) {
            Employee::create([
                'employee_code' => sprintf('NV-%03d', $i + 1),
                'full_name' => $emp[0],
                'position' => $emp[1],
                'role_id' => $emp[2],
                'phone' => sprintf('098%02d %03d %03d', $i, 100 + $i, 300 + $i),
                'email' => 'nv' . ($i + 1) . '@smartwater.vn',
                'avatar_path' => 'https://i.pravatar.cc/120?img=' . (($i + 30) % 60 + 1),
                'hire_date' => now()->subMonths(($i + 1) * 5)->format('Y-m-d'),
                'status' => ($i % 7 === 0 && $i !== 0) ? 'inactive' : 'active',
            ]);
        }
    }
}
