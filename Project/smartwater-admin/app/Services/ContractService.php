<?php

namespace App\Services;

use App\Models\Contract;

class ContractService
{
    public function getAllContracts()
    {
        return Contract::with('customer')->get();
    }

    public function getContractById($id)
    {
        return Contract::with('customer')->findOrFail($id);
    }

    public function createContract(array $data)
    {
        return Contract::create([
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
