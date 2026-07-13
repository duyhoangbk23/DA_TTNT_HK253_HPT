<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Batch;
use App\Models\Mcu;
use App\Models\DeviceDashboardData;
use App\Services\DeviceService;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Requests\ReplaceDeviceRequest;

class DeviceController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function index()
    {
        $usedDevices = $this->deviceService->getUsedDevices();
        $unusedDevices = $this->deviceService->getUnusedDevices();

        $products = Product::all();
        $customers = Customer::all();
        $contracts = Contract::all();
        $batches = Batch::all();
        $mcus = Mcu::all();

        return view('devices.index', [
            'usedDevices' => $usedDevices,
            'unusedDevices' => $unusedDevices,
            'products' => $products,
            'customers' => $customers,
            'contracts' => $contracts,
            'batches' => $batches,
            'mcus' => $mcus,
            'counts'   => [
                'active'      => $usedDevices->where('status', 'active')->count() + $unusedDevices->where('status', 'active')->count(),
                'maintenance' => $usedDevices->where('status', 'maintenance')->count() + $unusedDevices->where('status', 'maintenance')->count(),
                'error'       => $usedDevices->where('status', 'error')->count() + $unusedDevices->where('status', 'error')->count(),
                'pending'     => $usedDevices->where('status', 'pending')->count() + $unusedDevices->where('status', 'pending')->count(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $device = $this->deviceService->getDeviceById($id)->load(['product', 'customer', 'contract', 'mcu']);

        // Lấy telemetry data
        $telemetryData = DeviceDashboardData::where('device_id', $id)
            ->orderBy('recorded_at')
            ->get();

        $labels = $telemetryData->pluck('recorded_at')->map(fn($d) => $d->format('H:i'))->toArray();
        $telemetry = [
            'labels' => $labels,
            'tds' => $telemetryData->pluck('tds')->toArray(),
            'alerts' => $telemetryData->map(fn ($row) => [
                'time' => $row->recorded_at->format('H:i'),
                'alert' => $row->alert ?? 'normal',
                'tds' => $row->tds,
            ])->toArray(),
        ];

        // Lấy maintenance records
        $maintenance = $device->maintenanceRecords()->with('employee')->latest('maintenance_date')->get();

        $availableMcus = app(
            \App\Services\McuService::class
        )->getAvailableMcus();

        return view('devices.show', [
            'device' => $device,
            'telemetry' => $telemetry,
            'maintenance' => $maintenance,
            'availableMcus' => $availableMcus,
        ]);
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

    public function replace(ReplaceDeviceRequest $request, $id)
    {
        $newDevice = $this->deviceService->replaceDevice($id, $request->validated());
        return redirect()->route('devices.show', $newDevice->id)->with('success', 'Thiết bị đã được thay thế thành công');
    }
}
