<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index', [
            'employees' => MockData::employees(),
            'roles'     => MockData::roles(),
        ]);
    }
}
