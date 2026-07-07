<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Batch;
use App\Services\DeviceService;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;

class DeviceController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function index()
    {
        $devices = $this->deviceService->getAllDevices();
        $products = Product::all();
        $customers = Customer::all();
        $contracts = Contract::all();
        $batches = Batch::all();

        return view('devices.index', [
            'devices'  => $devices,
            'products' => $products,
            'customers' => $customers,
            'contracts' => $contracts,
            'batches' => $batches,
            'counts'   => [
                'active'      => $devices->where('status', 'active')->count(),
                'maintenance' => $devices->where('status', 'maintenance')->count(),
                'error'       => $devices->where('status', 'error')->count(),
                'pending'     => $devices->where('status', 'pending')->count(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $device = $this->deviceService->getDeviceById($id);
        return view('devices.show', ['device' => $device]);
    }

    public function store(StoreDeviceRequest $request)
    {
        $device = $this->deviceService->createDevice($request->validated());
        return redirect()->route('devices.index')->with('success', 'Thiết bị đã được tạo');
    }

    public function update(UpdateDeviceRequest $request, $id)
    {
        $device = $this->deviceService->updateDevice($id, $request->validated());
        return redirect()->route('devices.index')->with('success', 'Thiết bị đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->deviceService->deleteDevice($id);
        return redirect()->route('devices.index')->with('success', 'Thiết bị đã được xóa');
    }
}
