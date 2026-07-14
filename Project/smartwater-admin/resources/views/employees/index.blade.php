@extends('layouts.app')

@section('title', 'Nhân viên')
@section('page-title', 'Nhân viên')
@section('page-subtitle', 'Danh sách nhân viên kỹ thuật và quản lý.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Nhân viên</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddEmployee">
        <i class="bi bi-person-plus me-1"></i> Thêm nhân viên
    </button>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm nhân viên..." data-dt-search="#tblEmployees">
                <select class="form-select form-select-sm" style="width: 190px;"
                        data-dt-filter="#tblEmployees" data-dt-column="2">
                    <option value="">Tất cả vai trò</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblEmployees" data-datatable data-no-sort="0,4">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $e)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $e->full_name }}</div>
                                <div class="cell-sub">{{ $e->employee_code }}</div>
                            </td>
                            <td>{{ $e->phone }}</td>
                            <td>{{ $e->email }}</td>
                            <td>{{ $e->role?->name ?? '-' }}</td>
                            <td><x-status-badge :status="$e->status" /></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditEmployee"
                                        data-id="{{ $e->id }}"
                                        data-code="{{ $e->employee_code }}"
                                        data-name="{{ $e->full_name }}"
                                        data-position="{{ $e->position }}"
                                        data-phone="{{ $e->phone }}"
                                        data-email="{{ $e->email }}"
                                        data-address="{{ $e->address }}"
                                        data-hire-date="{{ $e->hire_date }}"
                                        data-role="{{ $e->role_id }}"
                                        data-status="{{ $e->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('employees.destroy', $e->id) }}"
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

    <!-- Modal Thêm nhân viên -->
    <div class="modal fade" id="modalAddEmployee" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhân viên mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('employees.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã nhân viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror"
                                   name="employee_code" value="{{ old('employee_code') }}" required>
                            @error('employee_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên nhân viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                   name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chức vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror"
                                   name="position" value="{{ old('position') }}" required>
                            @error('position')
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
                            <label class="form-label">Ngày vào làm <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                   name="hire_date" value="{{ old('hire_date') }}" required>
                            @error('hire_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" required>
                                <option value="">-- Chọn vai trò --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
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
                        <button type="submit" class="btn btn-primary">Thêm nhân viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa nhân viên -->
    <div class="modal fade" id="modalEditEmployee" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditEmployee">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã nhân viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror"
                                   name="employee_code" id="editEmployeeCode" value="{{ old('employee_code') }}" required>
                            @error('employee_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên nhân viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                   name="full_name" id="editEmployeeName" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chức vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror"
                                   name="position" id="editEmployeePosition" value="{{ old('position') }}" required>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" id="editEmployeePhone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" id="editEmployeeEmail" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                   name="address" id="editEmployeeAddress" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày vào làm <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                   name="hire_date" id="editEmployeeHireDate" value="{{ old('hire_date') }}" required>
                            @error('hire_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" id="editEmployeeRole" required>
                                <option value="">-- Chọn vai trò --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" id="editEmployeeStatus" required>
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
                        <button type="submit" class="btn btn-primary">Cập nhật nhân viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditEmployee');
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const code = button.getAttribute('data-code');
                const name = button.getAttribute('data-name');
                const position = button.getAttribute('data-position');
                const phone = button.getAttribute('data-phone');
                const email = button.getAttribute('data-email');
                const address = button.getAttribute('data-address');
                const hireDate = button.getAttribute('data-hire-date');
                const role = button.getAttribute('data-role');
                const status = button.getAttribute('data-status');

                document.getElementById('editEmployeeCode').value = code;
                document.getElementById('editEmployeeName').value = name;
                document.getElementById('editEmployeePosition').value = position;
                document.getElementById('editEmployeePhone').value = phone;
                document.getElementById('editEmployeeEmail').value = email;
                document.getElementById('editEmployeeAddress').value = address || '';
                document.getElementById('editEmployeeHireDate').value = hireDate;
                document.getElementById('editEmployeeRole').value = role;
                document.getElementById('editEmployeeStatus').value = status;

                const form = document.getElementById('formEditEmployee');
                form.action = `/employees/${id}`;
            });
        });
    </script>
@endsection
