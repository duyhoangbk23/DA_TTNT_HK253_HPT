@extends('layouts.app')

@section('title', 'Bảo trì')
@section('page-title', 'Bảo trì')
@section('page-subtitle', 'Quản lý thiết bị gặp lỗi, bảo trì định kỳ theo hợp đồng và cảnh báo telemetry.')

@section('breadcrumb')
    <li class="breadcrumb-item active">Bảo trì</li>
@endsection

@section('content')
    <x-panel class="mb-4" title="Thiết bị gặp lỗi" icon="bi-exclamation-triangle" flush>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mã thiết bị</th>
                        <th>MCU</th>
                        <th>Vị trí</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($errorDevices as $device)
                        <tr>
                            <td class="cell-title">{{ $device->device_code }}</td>
                            <td>{{ $device->mcu_id ?? '-' }}</td>
                            <td>{{ $device->location ?? '-' }}</td>
                            <td><x-status-badge :status="$device->status" /></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-danger" href="{{ route('devices.show', $device->id) }}">Xem thiết bị</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Không có thiết bị gặp lỗi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $errorDevices->links() }}</div>
    </x-panel>

    <x-panel title="Phiếu bảo trì" icon="bi-tools" flush>
        <x-slot:actions>
            <form method="GET" class="d-flex gap-2">
                <select class="form-select form-select-sm" name="type" onchange="this.form.submit()">
                    <option value="">Tất cả nguồn</option>
                    <option value="scheduled" @selected(request('type') === 'scheduled')>Định kỳ</option>
                    <option value="alert" @selected(request('type') === 'alert')>Cảnh báo</option>
                </select>
                <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    @foreach (['new', 'assigned', 'in_progress', 'awaiting_parts', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </form>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Thiết bị</th><th>Nguồn</th><th>Thời điểm</th><th>Ưu tiên</th><th>Kỹ thuật viên</th><th>Trạng thái</th><th></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><div class="cell-title">{{ $order->device?->device_code ?? '-' }}</div><small class="text-muted">{{ $order->contract?->contract_code ?? '-' }}</small></td>
                            <td>{{ $order->type === 'scheduled' ? 'Định kỳ' : ($order->source_alert ?? 'Cảnh báo') }}</td>
                            <td>{{ optional($order->scheduled_for)->format('d/m/Y') ?? optional($order->triggered_at)->format('d/m/Y H:i') ?? '-' }}</td>
                            <td><x-status-badge :status="$order->priority" /></td>
                            <td>{{ $order->employee?->full_name ?? 'Chưa phân công' }}</td>
                            <td><x-status-badge :status="$order->status" /></td>
                            <td class="text-end"><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#workOrder{{ $order->id }}">Cập nhật</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Chưa có phiếu bảo trì.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $orders->links() }}</div>
    </x-panel>

    @foreach ($orders as $order)
        <div class="modal fade" id="workOrder{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
            <form method="POST" action="{{ route('maintenance-work-orders.update', $order) }}">
                @csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title">Work order #{{ $order->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Technician</label><select class="form-select" name="employee_id"><option value="">Unassigned</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected($order->employee_id === $employee->id)>{{ $employee->full_name }}</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status">@foreach (['new', 'assigned', 'in_progress', 'awaiting_parts', 'completed', 'cancelled'] as $status)<option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>@endforeach</select></div>
                    <div><label class="form-label">Notes</label><textarea class="form-control" name="description" rows="3">{{ $order->description }}</textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Save</button></div>
            </form>
        </div></div></div>
    @endforeach
@endsection
