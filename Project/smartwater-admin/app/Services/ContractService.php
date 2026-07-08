<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Mcu;

class ContractService
{
    public function getAllContracts()
    {
        return Contract::with('customer')->get();
    }

    public function getContractById($id)
    {
        return Contract::with(['customer', 'devices.mcu'])->findOrFail($id);
    }

    public function createContract(array $data)
    {
        $customer = null;
        if (!empty($data['customer_name'])) {
            $customer = Customer::firstOrCreate(
                ['customer_name' => $data['customer_name']],
                [
                    'customer_code' => $this->generateCustomerCode($data['customer_name']),
                    'phone' => $data['phone'] ?? '',
                    'email' => $data['email'] ?? null,
                    'address' => $data['address'] ?? null,
                    'type' => $data['customer_type'] ?? 'individual',
                    'status' => 'active',
                    'joined_at' => now(),
                ]
            );
        }

        $contract = Contract::create([
            'contract_code' => $data['contract_code'],
            'customer_id' => $customer?->id,
            'contract_type' => $data['contract_type'],
            'start_date' => $data['start_date'],
            'install_date' => $data['install_date'] ?? null,
            'end_date' => $data['end_date'],
            'maintenance_cycle_months' => $data['maintenance_cycle_months'],
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'active',
        ]);

        if (!empty($data['device_ids']) && !empty($data['mcu_ids'])) {
            $deviceIds = collect($data['device_ids'])->map(fn($id) => (int) $id)->unique();
            $mcuIds = collect($data['mcu_ids'])->map(fn($id) => (int) $id)->unique();
            $pairs = $deviceIds->zip($mcuIds);
            foreach ($pairs as [$deviceId, $mcuId]) {
                $device = Device::where('id', $deviceId)
                    ->whereNull('contract_id')
                    ->whereNull('mcu_id')
                    ->first();
                $mcu = Mcu::where('id', $mcuId)
                    ->whereDoesntHave('devices', function ($query) {
                        $query->whereNull('replaced_at');
                    })->first();

                if ($device && $mcu) {
                    $device->update([
                        'contract_id' => $contract->id,
                        'customer_id' => $customer?->id,
                        'mcu_id' => $mcu->id,
                    ]);
                }
            }
        }

        return $contract;
    }

    protected function generateCustomerCode(string $customerName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $customerName), 0, 3));
        $suffix = str_pad((string) random_int(100, 999), 3, '0', STR_PAD_LEFT);
        return sprintf('CUST-%s-%s', $prefix ?: 'XX', $suffix);
    }

    public function updateContract($id, array $data)
    {
        $contract = Contract::findOrFail($id);
        $contract->update([
            'contract_code' => $data['contract_code'],
            'customer_id' => $data['customer_id'],
            'contract_type' => $data['contract_type'],
            'start_date' => $data['start_date'],
            'install_date' => $data['install_date'] ?? null,
            'end_date' => $data['end_date'],
            'maintenance_cycle_months' => $data['maintenance_cycle_months'],
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'active',
        ]);
        return $contract;
    }

    public function deleteContract($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();
        return true;
    }
}
