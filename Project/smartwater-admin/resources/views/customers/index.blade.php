@extends('layouts.app')

@section('title', 'Khách hàng')
@section('page-title', 'Khách hàng')
@section('page-subtitle', 'Danh sách khách hàng cá nhân và doanh nghiệp.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Khách hàng</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCustomer">
        <i class="bi bi-person-plus me-1"></i> Thêm khách hàng
    </button>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm khách hàng..." data-dt-search="#tblCustomers">
                <select class="form-select form-select-sm" style="width: 160px;"
                        data-dt-filter="#tblCustomers" data-dt-column="4">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngưng hoạt động</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblCustomers" data-datatable data-no-sort="0,4,5">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $c)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $c->customer_name }}</div>
                                <div class="cell-sub">{{ $c->customer_code }}</div>
                            </td>
                            <td>{{ $c->phone }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->address ?? '-' }}</td>
                            <td>
                                @if($c->type === 'individual')
                                    <span class="badge bg-info">Cá nhân</span>
                                @else
                                    <span class="badge bg-secondary">Doanh nghiệp</span>
                                @endif
                            </td>
                            <td><x-status-badge :status="$c->status" /></td>
                            <td>
                                <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditCustomer"
                                        data-id="{{ $c->id }}"
                                        data-code="{{ $c->customer_code }}"
                                        data-name="{{ $c->customer_name }}"
                                        data-phone="{{ $c->phone }}"
                                        data-email="{{ $c->email }}"
                                        data-address="{{ $c->address }}"
                                        data-type="{{ $c->type }}"
                                        data-status="{{ $c->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('customers.destroy', $c->id) }}"
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

    <!-- Modal Thêm khách hàng -->
    <div class="modal fade" id="modalAddCustomer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm khách hàng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_code') is-invalid @enderror"
                                   name="customer_code" value="{{ old('customer_code') }}" required>
                            @error('customer_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                   name="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                   name="address" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                <option value="">-- Chọn loại --</option>
                                <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>Cá nhân</option>
                                <option value="business" {{ old('type') === 'business' ? 'selected' : '' }}>Doanh nghiệp</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa khách hàng -->
    <div class="modal fade" id="modalEditCustomer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditCustomer">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_code') is-invalid @enderror"
                                   name="customer_code" id="editCustomerCode" value="{{ old('customer_code') }}" required>
                            @error('customer_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                   name="customer_name" id="editCustomerName" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" id="editCustomerPhone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" id="editCustomerEmail" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                   name="address" id="editCustomerAddress" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" id="editCustomerType" required>
                                <option value="">-- Chọn loại --</option>
                                <option value="individual">Cá nhân</option>
                                <option value="business">Doanh nghiệp</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" id="editCustomerStatus" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Ngưng hoạt động</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật khách hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditCustomer');
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const code = button.getAttribute('data-code');
                const name = button.getAttribute('data-name');
                const phone = button.getAttribute('data-phone');
                const email = button.getAttribute('data-email');
                const address = button.getAttribute('data-address');
                const type = button.getAttribute('data-type');
                const status = button.getAttribute('data-status');

                document.getElementById('editCustomerCode').value = code;
                document.getElementById('editCustomerName').value = name;
                document.getElementById('editCustomerPhone').value = phone;
                document.getElementById('editCustomerEmail').value = email;
                document.getElementById('editCustomerAddress').value = address || '';
                document.getElementById('editCustomerType').value = type;
                document.getElementById('editCustomerStatus').value = status;

                const form = document.getElementById('formEditCustomer');
                form.action = `/customers/${id}`;
            });
        });
    </script>
@endsection
