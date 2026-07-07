<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function getAllEmployees()
    {
        return Employee::with('role')->get();
    }

    public function getEmployeeById($id)
    {
        return Employee::with('role')->findOrFail($id);
    }

    public function createEmployee(array $data)
    {
        return Employee::create([
            'employee_code' => $data['employee_code'],
            'full_name' => $data['full_name'],
            'position' => $data['position'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'] ?? null,
            'hire_date' => $data['hire_date'],
            'role_id' => $data['role_id'],
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function updateEmployee($id, array $data)
    {
        $employee = Employee::findOrFail($id);
        $employee->update([
            'employee_code' => $data['employee_code'],
            'full_name' => $data['full_name'],
            'position' => $data['position'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'] ?? null,
            'hire_date' => $data['hire_date'],
            'role_id' => $data['role_id'],
            'status' => $data['status'] ?? 'active',
        ]);
        return $employee;
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return true;
    }
}
