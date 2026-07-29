<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Batch;
use App\Models\Mcu;
use App\Services\DeviceService;
use App\Services\DeviceTelemetryService;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Requests\ReplaceDeviceRequest;

class DeviceController extends Controller
{
    protected $deviceService;
    protected $deviceTelemetryService;

    public function __construct(DeviceService $deviceService, DeviceTelemetryService $deviceTelemetryService)
    {
        $this->deviceService = $deviceService;
        $this->deviceTelemetryService = $deviceTelemetryService;
    }

    // Trang danh sách thiết bị ghép trạng thái sử dụng với các dữ liệu đăng ký cần cho thao tác quản trị.
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

    // Trang chi tiết thiết bị ghép dữ liệu nghiệp vụ với telemetry thật theo mcu_id; phân trang log độc lập với chuỗi biểu đồ.
    public function show(int $id)
    {
        $device = $this->deviceService->getDeviceById($id)->load(['product', 'customer', 'contract', 'mcu']);

        $telemetry = $this->deviceTelemetryService->forMcu($device->mcu?->mcu_id);
        $telemetryLogs = $this->deviceTelemetryService->paginatedLogsForMcu(
            $device->mcu?->mcu_id,
            (int) request('telemetry_page', 1)
        );

        // Lấy maintenance records
        $maintenance = $device->maintenanceRecords()->with('employee')->latest('maintenance_date')->get();

        $availableMcus = app(
            \App\Services\McuService::class
        )->getAvailableMcus();

        return view('devices.show', [
            'device' => $device,
            'telemetry' => $telemetry,
            'telemetryLogs' => $telemetryLogs,
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
