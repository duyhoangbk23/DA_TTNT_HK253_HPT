@extends('layouts.app')

@section('title', 'Hợp đồng')
@section('page-title', 'Hợp đồng')
@section('page-subtitle', 'Quản lý hợp đồng lắp đặt, bảo trì và thay thế thiết bị.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Hợp đồng</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddContract">
        <i class="bi bi-plus-lg me-1"></i> Tạo hợp đồng
    </button>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm hợp đồng..." data-dt-search="#tblContracts">
                <select class="form-select form-select-sm" style="width: 160px;"
                        data-dt-filter="#tblContracts" data-dt-column="6">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngưng hoạt động</option>
                    <option value="expired">Hết hạn</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblContracts" data-datatable data-no-sort="6,7">
                <thead>
                    <tr>
                        <th>Mã hợp đồng</th>
                        <th>Khách hàng</th>
                        <th>Loại hợp đồng</th>
                        <th>Ngày ký</th>
                        <th>Ngày kết thúc</th>
                        <th>Giá trị</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contracts as $c)
                        <tr>
                            <td><div class="cell-title">{{ $c->contract_code }}</div></td>
                            <td>{{ $c->customer?->customer_name ?? '-' }}</td>
                            <td>{{ $c->contract_type }}</td>
                            <td>{{ $c->start_date->format('d/m/Y') }}</td>
                            <td>{{ $c->end_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($c->amount) }} ₫</td>
                            <td><x-status-badge :status="$c->status" /></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditContract"
                                        data-id="{{ $c->id }}"
                                        data-code="{{ $c->contract_code }}"
                                        data-customer="{{ $c->customer_id }}"
                                        data-type="{{ $c->contract_type }}"
                                        data-start="{{ $c->start_date->format('Y-m-d') }}"
                                        data-install="{{ $c->install_date?->format('Y-m-d') }}"
                                        data-end="{{ $c->end_date->format('Y-m-d') }}"
                                        data-cycle="{{ $c->maintenance_cycle_months }}"
                                        data-amount="{{ $c->amount }}"
                                        data-status="{{ $c->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('contracts.destroy', $c->id) }}"
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

    <!-- Modal Thêm hợp đồng -->
    <div class="modal fade" id="modalAddContract" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo hợp đồng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('contracts.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mã hợp đồng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contract_code') is-invalid @enderror"
                                       name="contract_code" value="{{ old('contract_code') }}" required>
                                @error('contract_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" name="customer_id" required>
                                    <option value="">-- Chọn khách hàng --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->customer_name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contract_type') is-invalid @enderror"
                                       name="contract_type" value="{{ old('contract_type') }}" placeholder="VD: Bảo trì, Lắp đặt" required>
                                @error('contract_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chu kỳ bảo trì (tháng) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('maintenance_cycle_months') is-invalid @enderror"
                                       name="maintenance_cycle_months" value="{{ old('maintenance_cycle_months', 3) }}" min="1" required>
                                @error('maintenance_cycle_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày ký <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày lắp đặt</label>
                                <input type="date" class="form-control @error('install_date') is-invalid @enderror"
                                       name="install_date" value="{{ old('install_date') }}">
                                @error('install_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá trị hợp đồng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                       name="amount" value="{{ old('amount') }}" min="0" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                                    <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Tạo hợp đồng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa hợp đồng -->
    <div class="modal fade" id="modalEditContract" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditContract">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mã hợp đồng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contract_code" id="editContractCode" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                <select class="form-select" name="customer_id" id="editContractCustomer" required>
                                    <option value="">-- Chọn khách hàng --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contract_type" id="editContractType" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chu kỳ bảo trì (tháng) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="maintenance_cycle_months" id="editContractCycle" min="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày ký <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="editContractStart" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày lắp đặt</label>
                                <input type="date" class="form-control" name="install_date" id="editContractInstall">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" id="editContractEnd" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá trị hợp đồng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" id="editContractAmount" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="editContractStatus" required>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Ngưng hoạt động</option>
                                    <option value="expired">Hết hạn</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật hợp đồng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditContract');
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('editContractCode').value = button.getAttribute('data-code');
                document.getElementById('editContractCustomer').value = button.getAttribute('data-customer');
                document.getElementById('editContractType').value = button.getAttribute('data-type');
                document.getElementById('editContractStart').value = button.getAttribute('data-start');
                document.getElementById('editContractInstall').value = button.getAttribute('data-install') || '';
                document.getElementById('editContractEnd').value = button.getAttribute('data-end');
                document.getElementById('editContractCycle').value = button.getAttribute('data-cycle');
                document.getElementById('editContractAmount').value = button.getAttribute('data-amount');
                document.getElementById('editContractStatus').value = button.getAttribute('data-status');
                document.getElementById('formEditContract').action = `/contracts/${id}`;
            });
        });
    </script>
@endsection
