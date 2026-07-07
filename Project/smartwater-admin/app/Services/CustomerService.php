<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function getAllCustomers()
    {
        return Customer::all();
    }

    public function getCustomerById($id)
    {
        return Customer::findOrFail($id);
    }

    public function createCustomer(array $data)
    {
        return Customer::create([
            'customer_code' => $data['customer_code'],
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'] ?? null,
            'type' => $data['type'] ?? 'individual',
            'status' => $data['status'] ?? 'active',
            'joined_at' => $data['joined_at'] ?? now(),
        ]);
    }

    public function updateCustomer($id, array $data)
    {
        $customer = Customer::findOrFail($id);
        $customer->update([
            'customer_code' => $data['customer_code'],
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'] ?? null,
            'type' => $data['type'] ?? 'individual',
            'status' => $data['status'] ?? 'active',
            'joined_at' => $data['joined_at'] ?? now(),
        ]);
        return $customer;
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return true;
    }
}
