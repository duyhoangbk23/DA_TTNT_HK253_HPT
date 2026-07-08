<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Mcu;
use App\Services\ContractService;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index()
    {
        $contracts = $this->contractService->getAllContracts();
        $customers = Customer::all();
        $unusedDevices = Device::whereNull('contract_id')
            ->whereNull('mcu_id')
            ->whereNull('replaced_at')
            ->get();
        $unusedMcus = Mcu::whereDoesntHave('devices', function ($query) {
            $query->whereNull('replaced_at');
        })->get();

        return view('contracts.index', [
            'contracts' => $contracts,
            'customers' => $customers,
            'unusedDevices' => $unusedDevices,
            'unusedMcus' => $unusedMcus,
        ]);
    }

    public function show(int $id)
    {
        $contract = $this->contractService->getContractById($id);
        return view('contracts.show', ['contract' => $contract]);
    }

    public function store(StoreContractRequest $request)
    {
        $contract = $this->contractService->createContract($request->validated());
        return redirect()->route('contracts.index')->with('success', 'Hợp đồng đã được tạo');
    }

    public function update(UpdateContractRequest $request, $id)
    {
        $contract = $this->contractService->updateContract($id, $request->validated());
        return redirect()->route('contracts.index')->with('success', 'Hợp đồng đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->contractService->deleteContract($id);
        return redirect()->route('contracts.index')->with('success', 'Hợp đồng đã được xóa');
    }
}
