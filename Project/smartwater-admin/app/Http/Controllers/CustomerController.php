<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers();
        return view('customers.index', [
            'customers' => $customers,
        ]);
    }

    public function show(int $id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return view('customers.show', [
            'customer'    => $customer,
            'devices'     => $customer->devices,
            'contracts'   => $customer->contracts,
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->createCustomer($request->validated());
        return redirect()->route('customers.index')->with('success', 'Khách hàng đã được tạo');
    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = $this->customerService->updateCustomer($id, $request->validated());
        return redirect()->route('customers.index')->with('success', 'Khách hàng đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);
        return redirect()->route('customers.index')->with('success', 'Khách hàng đã được xóa');
    }
}
