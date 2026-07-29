<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Device;
use App\Models\MaintenanceWorkOrder;
use App\Services\MaintenanceWorkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceWorkOrderController extends Controller
{
    // Trang bảo trì ghép ticket công việc với danh sách thiết bị lỗi nhưng giữ hai bộ phân trang tách biệt.
    public function index(Request $request): View
    {
        $orders = MaintenanceWorkOrder::query()
            ->with(['device', 'contract', 'employee'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('maintenance-work-orders.index', [
            'orders' => $orders,
            'errorDevices' => Device::query()
                ->where('status', 'error')
                ->whereNull('replaced_at')
                ->orderBy('device_code')
                ->paginate(15, ['*'], 'error_page')
                ->withQueryString(),
            'employees' => Employee::query()->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function update(Request $request, MaintenanceWorkOrder $maintenanceWorkOrder, MaintenanceWorkOrderService $service): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:new,assigned,in_progress,awaiting_parts,completed,cancelled',
            'description' => 'nullable|string',
        ]);

        if ($data['status'] === 'completed') {
            $maintenanceWorkOrder->update([
                'employee_id' => $data['employee_id'] ?? $maintenanceWorkOrder->employee_id,
                'description' => $data['description'] ?? $maintenanceWorkOrder->description,
            ]);
            $service->completeWorkOrder($maintenanceWorkOrder->id, now());
        } else {
            $maintenanceWorkOrder->update([
                'employee_id' => $data['employee_id'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
                'open_key' => $data['status'] === 'cancelled' ? null : $maintenanceWorkOrder->open_key,
            ]);
        }

        return back()->with('success', 'Maintenance work order updated.');
    }
}
