<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\EmployeeService;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        $roles = Role::all();
        return view('employees.index', [
            'employees' => $employees,
            'roles' => $roles,
        ]);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = $this->employeeService->createEmployee($request->validated());
        return redirect()->route('employees.index')->with('success', 'Nhân viên đã được tạo');
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = $this->employeeService->updateEmployee($id, $request->validated());
        return redirect()->route('employees.index')->with('success', 'Nhân viên đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->employeeService->deleteEmployee($id);
        return redirect()->route('employees.index')->with('success', 'Nhân viên đã được xóa');
    }
}
