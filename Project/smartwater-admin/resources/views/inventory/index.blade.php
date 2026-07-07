@extends('layouts.app')

@section('title', 'Kho thiết bị')
@section('page-title', 'Kho thiết bị')
@section('page-subtitle', 'Theo dõi tồn kho sản phẩm và thiết bị máy lọc nước.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kho thiết bị</li>
@endsection

@section('page-actions')
    <!-- Nhập kho button (tương lai có thể tạo form nhập số lần) -->
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Tổng mặt hàng" :value="count($inventories)" icon="bi-boxes" color="primary" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Còn hàng" :value="collect($inventories)->filter(fn($i) => $i['stock_status'] === 'ok')->count()" icon="bi-check-circle" color="success" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Sắp hết hàng" :value="collect($inventories)->filter(fn($i) => $i['stock_status'] === 'low')->count()" icon="bi-exclamation-triangle" color="warning" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Hết hàng" :value="collect($inventories)->filter(fn($i) => $i['stock_status'] === 'out')->count()" icon="bi-x-circle" color="danger" />
        </div>
    </div>

    <x-panel flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm thiết bị..." data-dt-search="#tblInventory">
                <select class="form-select form-select-sm" style="width: 160px;"
                        data-dt-filter="#tblInventory" data-dt-column="6">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Còn hàng">Còn hàng</option>
                    <option value="Sắp hết hàng">Sắp hết hàng</option>
                    <option value="Hết hàng">Hết hàng</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblInventory" data-datatable data-no-sort="6">
                <thead>
                    <tr>
                        <th>Mã sản phẩm</th>
                        <th>Model</th>
                        <th>Số lượng</th>
                        <th>Đã giữ chỗ</th>
                        <th>Có thể xuất</th>
                        <th>Cập nhật</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventories as $inv)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $inv['product'] }}</div>
                                <div class="cell-sub">{{ $inv['code'] }}</div>
                            </td>
                            <td>{{ $inv['model'] }}</td>
                            <td>{{ number_format($inv['quantity']) }}</td>
                            <td>{{ number_format($inv['reserved']) }}</td>
                            <td class="fw-semibold">{{ number_format($inv['available']) }}</td>
                            <td>{{ $inv['last_updated'] }}</td>
                            <td><x-status-badge :status="$inv['stock_status']" /></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalAdjustInventory"
                                        data-id="{{ $inv['id'] }}"
                                        data-product="{{ $inv['product'] }}"
                                        data-quantity="{{ $inv['quantity'] }}"
                                        data-reserved="{{ $inv['reserved'] }}">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>

    <!-- Modal Điều chỉnh tồn kho -->
    <div class="modal fade" id="modalAdjustInventory" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Điều chỉnh tồn kho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formAdjustInventory">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sản phẩm</label>
                            <input type="text" class="form-control" id="adjustProductName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng hiện tại</label>
                            <input type="text" class="form-control" id="adjustCurrentQty" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng giữ chỗ</label>
                            <input type="text" class="form-control" id="adjustReservedQty" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Điều chỉnh số lượng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity_change') is-invalid @enderror"
                                   name="quantity_change" id="adjustQuantityChange" required
                                   placeholder="Nhập số dương để tăng, âm để giảm">
                            <small class="form-text text-muted">Ví dụ: +10 để tăng 10 cái, -5 để giảm 5 cái</small>
                            @error('quantity_change')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('note') is-invalid @enderror"
                                      name="note" id="adjustNote" rows="2"></textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalAdjust = document.getElementById('modalAdjustInventory');
            modalAdjust.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const product = button.getAttribute('data-product');
                const quantity = button.getAttribute('data-quantity');
                const reserved = button.getAttribute('data-reserved');

                document.getElementById('adjustProductName').value = product;
                document.getElementById('adjustCurrentQty').value = quantity;
                document.getElementById('adjustReservedQty').value = reserved;
                document.getElementById('adjustQuantityChange').value = '';
                document.getElementById('adjustNote').value = '';

                const form = document.getElementById('formAdjustInventory');
                form.action = `/inventory/${id}`;
            });
        });
    </script>
@endsection
