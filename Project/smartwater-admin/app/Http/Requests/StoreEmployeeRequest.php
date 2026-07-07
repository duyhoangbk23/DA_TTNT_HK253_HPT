<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'full_name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'address' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
