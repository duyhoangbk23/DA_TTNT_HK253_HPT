<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\DB;

class DeviceService
{
    public function getAllDevices()
    {
        return Device::with(['product', 'customer', 'contract', 'batch', 'mcu'])->get();
    }

    public function getDeviceById($id)
    {
        return Device::with(['product', 'customer', 'contract', 'batch', 'mcu'])->findOrFail($id);
    }

    public function createDevice(array $data)
    {
        return Device::create([
            'device_code' => $data['device_code'],
            'serial_number' => $data['serial_number'],
            'product_id' => $data['product_id'],
            'customer_id' => $data['customer_id'] ?? null,
            'contract_id' => $data['contract_id'] ?? null,
            'batch_id' => $data['batch_id'] ?? null,
            'mcu_id' => $data['mcu_id'] ?? null,
            'import_date' => $data['import_date'],
            'install_date' => $data['install_date'] ?? null,
            'firmware_version' => $data['firmware_version'] ?? null,
            'location' => $data['location'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function updateDevice($id, array $data)
    {
        $device = Device::findOrFail($id);
        $device->update([
            'device_code' => $data['device_code'],
            'serial_number' => $data['serial_number'],
            'product_id' => $data['product_id'],
            'customer_id' => $data['customer_id'] ?? null,
            'contract_id' => $data['contract_id'] ?? null,
            'batch_id' => $data['batch_id'] ?? null,
            'mcu_id' => $data['mcu_id'] ?? $device->mcu_id,
            'import_date' => $data['import_date'],
            'install_date' => $data['install_date'] ?? null,
            'firmware_version' => $data['firmware_version'] ?? null,
            'location' => $data['location'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);
        return $device;
    }

    public function deleteDevice($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();
        return true;
    }

    public function replaceDevice(int $oldDeviceId, array $newData)
    {
        return DB::transaction(function () use ($oldDeviceId, $newData) {
            $oldDevice = Device::findOrFail($oldDeviceId);

            // Tạo device mới với product/mcu mới
            $newDevice = Device::create([
                'device_code' => $oldDevice->device_code . '-v' . ($oldDevice->replaces()->count() + 2),
                'serial_number' => $oldDevice->serial_number . '-v' . ($oldDevice->replaces()->count() + 2),
                'product_id' => $newData['product_id'],
                'mcu_id' => $newData['mcu_id'],
                'customer_id' => $oldDevice->customer_id,
                'contract_id' => $oldDevice->contract_id,
                'batch_id' => $oldDevice->batch_id,
                'import_date' => now()->toDateString(),
                'install_date' => $newData['install_date'] ?? now()->toDateString(),
                'location' => $oldDevice->location,
                'status' => 'active',
            ]);

            // Đánh dấu device cũ là replaced
            $oldDevice->update([
                'status' => 'replaced',
                'replaced_at' => now(),
                'replaced_by_device_id' => $newDevice->id,
            ]);

            return $newDevice;
        });
    }
}
