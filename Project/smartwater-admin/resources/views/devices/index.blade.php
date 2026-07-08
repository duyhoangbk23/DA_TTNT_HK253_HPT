@extends('layouts.app')

@section('title', 'Thiết bị')
@section('page-title', 'Thiết bị')
@section('page-subtitle', 'Danh sách thiết bị máy lọc nước đã lắp đặt tại khách hàng.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Thiết bị</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddDevice">
        <i class="bi bi-plus-lg me-1"></i> Thêm thiết bị
    </button>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Hoạt động" :value="$counts['active']" icon="bi-check-circle" color="success" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Bảo trì" :value="$counts['maintenance']" icon="bi-tools" color="warning" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Lỗi" :value="$counts['error']" icon="bi-exclamation-octagon" color="danger" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Chờ lắp đặt" :value="$counts['pending']" icon="bi-hourglass-split" color="primary" />
        </div>
    </div>

    <x-panel class="mb-3" title="Thiết bị đang sử dụng" icon="bi-hdd-network" flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm thiết bị đang sử dụng..." data-dt-search="#tblUsedDevices">
                <select class="form-select form-select-sm" style="width: 170px;"
                        data-dt-filter="#tblUsedDevices" data-dt-column="6">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="maintenance">Bảo trì</option>
                    <option value="error">Lỗi</option>
                    <option value="pending">Chờ lắp đặt</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblUsedDevices" data-datatable data-no-sort="6,7">
                <thead>
                    <tr>
                        <th>Mã thiết bị</th>
                        <th>Sản phẩm</th>
                        <th>MCU</th>
                        <th>Serial</th>
                        <th>Khách hàng</th>
                        <th>Ngày nhập</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usedDevices as $d)
                        <tr>
                            <td><div class="cell-title">{{ $d->device_code }}</div></td>
                            <td>{{ $d->product?->product_name ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $d->mcu?->mcu_code ?? 'N/A' }}</span></td>
                            <td>{{ $d->serial_number }}</td>
                            <td>{{ $d->customer?->customer_name ?? '-' }}</td>
                            <td>{{ $d->import_date?->format('d/m/Y') ?? '-' }}</td>
                            <td><x-status-badge :status="$d->status" /></td>
                            <td>
                                <a href="{{ route('devices.show', $d->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditDevice"
                                        data-id="{{ $d->id }}"
                                        data-code="{{ $d->device_code }}"
                                        data-serial="{{ $d->serial_number }}"
                                        data-product="{{ $d->product_id }}"
                                        data-mcu="{{ $d->mcu_id }}"
                                        data-customer="{{ $d->customer_id }}"
                                        data-contract="{{ $d->contract_id }}"
                                        data-batch="{{ $d->batch_id }}"
                                        data-import="{{ $d->import_date?->format('Y-m-d') ?? '' }}"
                                        data-install="{{ $d->install_date?->format('Y-m-d') }}"
                                        data-firmware="{{ $d->firmware_version }}"
                                        data-location="{{ $d->location }}"
                                        data-status="{{ $d->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('devices.destroy', $d->id) }}"
                                      style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>

    <x-panel class="mb-3" title="Thiết bị chưa lắp đặt" icon="bi-box-seam" flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm thiết bị chưa lắp đặt..." data-dt-search="#tblUnusedDevices">
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblUnusedDevices" data-datatable data-no-sort="6,7">
                <thead>
                    <tr>
                        <th>Mã thiết bị</th>
                        <th>Sản phẩm</th>
                        <th>MCU</th>
                        <th>Serial</th>
                        <th>Khách hàng</th>
                        <th>Ngày nhập</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unusedDevices as $d)
                        <tr>
                            <td><div class="cell-title">{{ $d->device_code }}</div></td>
                            <td>{{ $d->product?->product_name ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $d->mcu?->mcu_code ?? 'N/A' }}</span></td>
                            <td>{{ $d->serial_number }}</td>
                            <td>{{ $d->customer?->customer_name ?? '-' }}</td>
                            <td>{{ $d->import_date?->format('d/m/Y') ?? '-' }}</td>
                            <td><x-status-badge :status="$d->status" /></td>
                            <td>
                                <a href="{{ route('devices.show', $d->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditDevice"
                                        data-id="{{ $d->id }}"
                                        data-code="{{ $d->device_code }}"
                                        data-serial="{{ $d->serial_number }}"
                                        data-product="{{ $d->product_id }}"
                                        data-mcu="{{ $d->mcu_id }}"
                                        data-customer="{{ $d->customer_id }}"
                                        data-contract="{{ $d->contract_id }}"
                                        data-batch="{{ $d->batch_id }}"
                                        data-import="{{ $d->import_date?->format('Y-m-d') ?? '' }}"
                                        data-install="{{ $d->install_date?->format('Y-m-d') }}"
                                        data-firmware="{{ $d->firmware_version }}"
                                        data-location="{{ $d->location }}"
                                        data-status="{{ $d->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('devices.destroy', $d->id) }}"
                                      style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>

    <!-- Modal Thêm thiết bị -->
    <div class="modal fade" id="modalAddDevice" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thiết bị mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('devices.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mã thiết bị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('device_code') is-invalid @enderror" name="device_code" required>
                                @error('device_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror" name="serial_number" required>
                                @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_id') is-invalid @enderror" name="product_id" required>
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">MCU</label>
                                <select class="form-select @error('mcu_id') is-invalid @enderror" name="mcu_id">
                                    <option value="">-- Chọn MCU --</option>
                                    @foreach ($mcus as $m)
                                        @if(!$m->devices()->whereNull('replaced_at')->exists())
                                            <option value="{{ $m->id }}">{{ $m->mcu_code }} ({{ $m->serial_number }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('mcu_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Khách hàng</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" name="customer_id">
                                    <option value="">-- Chọn khách hàng --</option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hợp đồng</label>
                                <select class="form-select @error('contract_id') is-invalid @enderror" name="contract_id">
                                    <option value="">-- Chọn hợp đồng --</option>
                                    @foreach ($contracts as $ct)
                                        <option value="{{ $ct->id }}">{{ $ct->contract_code }}</option>
                                    @endforeach
                                </select>
                                @error('contract_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lô hàng</label>
                                <select class="form-select @error('batch_id') is-invalid @enderror" name="batch_id">
                                    <option value="">-- Chọn lô hàng --</option>
                                    @foreach ($batches as $b)
                                        <option value="{{ $b->id }}">{{ $b->batch_code }}</option>
                                    @endforeach
                                </select>
                                @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('import_date') is-invalid @enderror" name="import_date" required>
                                @error('import_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày lắp đặt</label>
                                <input type="date" class="form-control @error('install_date') is-invalid @enderror" name="install_date">
                                @error('install_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Firmware</label>
                                <input type="text" class="form-control @error('firmware_version') is-invalid @enderror" name="firmware_version" placeholder="v1.0">
                                @error('firmware_version')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Địa điểm lắp đặt</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" name="location">
                                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="active">Hoạt động</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="error">Lỗi</option>
                                    <option value="pending">Chờ lắp đặt</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm thiết bị</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa thiết bị -->
    <div class="modal fade" id="modalEditDevice" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thiết bị</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditDevice">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mã thiết bị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="device_code" id="editDeviceCode" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="serial_number" id="editDeviceSerial" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                <select class="form-select" name="product_id" id="editDeviceProduct" required>
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">MCU</label>
                                <select class="form-select" name="mcu_id" id="editDeviceMcu">
                                    <option value="">-- Chọn MCU --</option>
                                    @foreach ($mcus as $m)
                                        <option value="{{ $m->id }}">{{ $m->mcu_code }} ({{ $m->serial_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Khách hàng</label>
                                <select class="form-select" name="customer_id" id="editDeviceCustomer">
                                    <option value="">-- Chọn khách hàng --</option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hợp đồng</label>
                                <select class="form-select" name="contract_id" id="editDeviceContract">
                                    <option value="">-- Chọn hợp đồng --</option>
                                    @foreach ($contracts as $ct)
                                        <option value="{{ $ct->id }}">{{ $ct->contract_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lô hàng</label>
                                <select class="form-select" name="batch_id" id="editDeviceBatch">
                                    <option value="">-- Chọn lô hàng --</option>
                                    @foreach ($batches as $b)
                                        <option value="{{ $b->id }}">{{ $b->batch_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="import_date" id="editDeviceImport" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày lắp đặt</label>
                                <input type="date" class="form-control" name="install_date" id="editDeviceInstall">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Firmware</label>
                                <input type="text" class="form-control" name="firmware_version" id="editDeviceFirmware">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Địa điểm lắp đặt</label>
                                <input type="text" class="form-control" name="location" id="editDeviceLocation">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="editDeviceStatus" required>
                                    <option value="active">Hoạt động</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="error">Lỗi</option>
                                    <option value="pending">Chờ lắp đặt</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật thiết bị</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditDevice');
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('editDeviceCode').value = button.getAttribute('data-code');
                document.getElementById('editDeviceSerial').value = button.getAttribute('data-serial');
                document.getElementById('editDeviceProduct').value = button.getAttribute('data-product') || '';
                document.getElementById('editDeviceMcu').value = button.getAttribute('data-mcu') || '';
                document.getElementById('editDeviceCustomer').value = button.getAttribute('data-customer') || '';
                document.getElementById('editDeviceContract').value = button.getAttribute('data-contract') || '';
                document.getElementById('editDeviceBatch').value = button.getAttribute('data-batch') || '';
                document.getElementById('editDeviceImport').value = button.getAttribute('data-import');
                document.getElementById('editDeviceInstall').value = button.getAttribute('data-install') || '';
                document.getElementById('editDeviceFirmware').value = button.getAttribute('data-firmware') || '';
                document.getElementById('editDeviceLocation').value = button.getAttribute('data-location') || '';
                document.getElementById('editDeviceStatus').value = button.getAttribute('data-status');
                document.getElementById('formEditDevice').action = `/devices/${id}`;
            });
        });
    </script>
@endsection
