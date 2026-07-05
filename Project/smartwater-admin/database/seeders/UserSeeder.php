<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['admin', 'admin@smartwater.vn', 1, 1, 'Administrator'],
            ['employee', 'employee@smartwater.vn', 2, 2, 'Employee'],
            ['technician', 'technician@smartwater.vn', 3, 3, 'Technician'],
        ];

        foreach ($users as $u) {
            User::create([
                'username' => $u[0],
                'email' => $u[1],
                'password' => Hash::make('password123'),
                'role_id' => $u[2],
                'employee_id' => $u[3],
                'status' => 'active',
            ]);
        }
    }
}
