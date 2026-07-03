<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index', [
            'customers' => MockData::customers(),
        ]);
    }

    public function show(int $id)
    {
        $customer = MockData::findCustomer($id);
        abort_if(! $customer, 404);

        return view('customers.show', [
            'customer'    => $customer,
            'devices'     => MockData::devicesForCustomer($id),
            'contracts'   => MockData::contractsForCustomer($id),
            'maintenance' => MockData::maintenanceForCustomer($customer['name']),
        ]);
    }
}
