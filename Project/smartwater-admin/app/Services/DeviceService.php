<?php

namespace App\Services;

use App\Models\Device;

class DeviceService
{
    public function getAllDevices()
    {
        return Device::with(['product', 'customer', 'contract', 'batch'])->get();
    }

    public function getDeviceById($id)
    {
        return Device::with(['product', 'customer', 'contract', 'batch'])->findOrFail($id);
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
}
