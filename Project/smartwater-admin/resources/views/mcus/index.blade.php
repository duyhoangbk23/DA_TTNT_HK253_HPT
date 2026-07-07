@extends('layouts.app')

@section('title', 'Quản lý MCU')
@section('page-title', 'Quản lý MCU')
@section('page-subtitle', 'Quản lý bộ điều khiển ESP32.')
@section('breadcrumb')
    <li class="breadcrumb-item active">MCU</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddMcu"><i class="bi bi-plus-lg me-1"></i> Thêm MCU</button>
@endsection

@section('content')
    <x-panel class="mb-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="mcuTable">
                <thead>
                    <tr>
                        <th>Mã MCU</th>
                        <th>Serial Number</th>
                        <th>Firmware</th>
                        <th>Trạng thái</th>
                        <th>Kết nối cuối</th>
                        <th>Thiết bị hiện gắn</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mcus as $mcu)
                        <tr>
                            <td class="cell-title">{{ $mcu->mcu_code }}</td>
                            <td>{{ $mcu->serial_number }}</td>
                            <td><small class="text-muted">{{ $mcu->firmware_version ?? 'N/A' }}</small></td>
                            <td>
                                @if($mcu->status === 'online')
                                    <span class="badge bg-success">Online</span>
                                @elseif($mcu->status === 'offline')
                                    <span class="badge bg-secondary">Offline</span>
                                @else
                                    <span class="badge bg-danger">Error</span>
                                @endif
                            </td>
                            <td><small>{{ $mcu->last_connected_at?->format('d/m/Y H:i') ?? 'Chưa kết nối' }}</small></td>
                            <td>
                                @if($mcu->currentDevice())
                                    <a href="{{ route('devices.show', $mcu->currentDevice()->id) }}" class="text-decoration-none">
                                        {{ $mcu->currentDevice()->device_code }}
                                    </a>
                                @else
                                    <span class="text-muted-2">Chưa gắn</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-white border"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditMcu"
                                    data-id="{{ $mcu->id }}"
                                    data-mcu-code="{{ $mcu->mcu_code }}"
                                    data-serial-number="{{ $mcu->serial_number }}"
                                    data-firmware-version="{{ $mcu->firmware_version }}"
                                    data-status="{{ $mcu->status }}">
                                    <i class="bi bi-pencil me-1"></i>Sửa
                                </button>
                                <form method="POST" action="{{ route('mcus.destroy', $mcu->id) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white border text-danger" onclick="return confirm('Xác nhận xoá MCU này?')">
                                        <i class="bi bi-trash me-1"></i>Xoá
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted-2 py-4">Chưa có MCU nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>

    {{-- Modal Thêm MCU --}}
    <div class="modal fade" id="modalAddMcu" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('mcus.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm MCU</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã MCU <span class="text-danger">*</span></label>
                            <input type="text" name="mcu_code" class="form-control @error('mcu_code') is-invalid @enderror" required>
                            @error('mcu_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required>
                            @error('serial_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phiên bản Firmware</label>
                            <input type="text" name="firmware_version" class="form-control @error('firmware_version') is-invalid @enderror">
                            @error('firmware_version')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="offline">Offline</option>
                                <option value="online">Online</option>
                                <option value="error">Error</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Tạo MCU</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Sửa MCU --}}
    <div class="modal fade" id="modalEditMcu" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditMcu">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa MCU</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã MCU <span class="text-danger">*</span></label>
                            <input type="text" name="mcu_code" class="form-control @error('mcu_code') is-invalid @enderror" required>
                            @error('mcu_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" required>
                            @error('serial_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phiên bản Firmware</label>
                            <input type="text" name="firmware_version" class="form-control @error('firmware_version') is-invalid @enderror">
                            @error('firmware_version')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="offline">Offline</option>
                                <option value="online">Online</option>
                                <option value="error">Error</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('modalEditMcu');
    editModal.addEventListener('show.bs.modal', function(e) {
        const button = e.relatedTarget;
        const id = button.getAttribute('data-id');
        const form = document.getElementById('formEditMcu');
        form.action = `/mcus/${id}`;
        form.querySelector('input[name="mcu_code"]').value = button.getAttribute('data-mcu-code');
        form.querySelector('input[name="serial_number"]').value = button.getAttribute('data-serial-number');
        form.querySelector('input[name="firmware_version"]').value = button.getAttribute('data-firmware-version');
        form.querySelector('select[name="status"]').value = button.getAttribute('data-status');
    });

    // DataTable
    new DataTable('#mcuTable', {
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
        }
    });
});
</script>
@endpush
