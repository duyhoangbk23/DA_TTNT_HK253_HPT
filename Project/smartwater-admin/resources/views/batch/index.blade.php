@extends('layouts.app')

@section('title', 'Lô hàng')
@section('page-title', 'Lô hàng')
@section('page-subtitle', 'Quản lý các lô hàng nhập kho từ nhà cung cấp.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Lô hàng</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBatch">
        <i class="bi bi-plus-lg me-1"></i> Tạo lô hàng
    </button>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <input type="search" class="form-control form-control-sm" style="width: 220px;"
                   placeholder="Tìm lô hàng..." data-dt-search="#tblBatches">
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblBatches" data-datatable data-no-sort="5,6">
                <thead>
                    <tr>
                        <th>Mã lô</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Hạn sử dụng</th>
                        <th>Số lượng</th>
                        <th>Ghi chú</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $b)
                        <tr>
                            <td><div class="cell-title">{{ $b->batch_code }}</div></td>
                            <td>{{ $b->supplier?->name ?? '-' }}</td>
                            <td>{{ $b->import_date->format('d/m/Y') }}</td>
                            <td>{{ $b->expiry_date ? $b->expiry_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ number_format($b->quantity) }}</td>
                            <td>{{ $b->note ?? '-' }}</td>
                            <td>
                                <a href="{{ route('batches.show', $b->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditBatch"
                                        data-id="{{ $b->id }}"
                                        data-code="{{ $b->batch_code }}"
                                        data-supplier="{{ $b->supplier_id }}"
                                        data-import="{{ $b->import_date->format('Y-m-d') }}"
                                        data-expiry="{{ $b->expiry_date?->format('Y-m-d') }}"
                                        data-qty="{{ $b->quantity }}"
                                        data-note="{{ $b->note }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('batches.destroy', $b->id) }}"
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

    <!-- Modal Thêm lô hàng -->
    <div class="modal fade" id="modalAddBatch" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo lô hàng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('batches.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã lô <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('batch_code') is-invalid @enderror" name="batch_code" required>
                            @error('batch_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" name="supplier_id" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('import_date') is-invalid @enderror" name="import_date" required>
                            @error('import_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hạn sử dụng</label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date">
                            @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" min="1" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" name="note" rows="2"></textarea>
                            @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Tạo lô hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa lô hàng -->
    <div class="modal fade" id="modalEditBatch" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa lô hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditBatch">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã lô <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="batch_code" id="editBatchCode" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select" name="supplier_id" id="editBatchSupplier" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="import_date" id="editBatchImport" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hạn sử dụng</label>
                            <input type="date" class="form-control" name="expiry_date" id="editBatchExpiry">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="editBatchQty" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="note" id="editBatchNote" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật lô hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = document.getElementById('modalEditBatch');
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('editBatchCode').value = button.getAttribute('data-code');
                document.getElementById('editBatchSupplier').value = button.getAttribute('data-supplier');
                document.getElementById('editBatchImport').value = button.getAttribute('data-import');
                document.getElementById('editBatchExpiry').value = button.getAttribute('data-expiry') || '';
                document.getElementById('editBatchQty').value = button.getAttribute('data-qty');
                document.getElementById('editBatchNote').value = button.getAttribute('data-note') || '';
                document.getElementById('formEditBatch').action = `/batches/${id}`;
            });
        });
    </script>
@endsection
