@extends('layouts.app')

@section('title', 'Kho thiết bị')
@section('page-title', 'Kho thiết bị')
@section('page-subtitle', 'Theo dõi tồn kho sản phẩm và thiết bị máy lọc nước.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kho thiết bị</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary"><i class="bi bi-box-arrow-in-down me-1"></i> Nhập kho</button>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Tổng mặt hàng" :value="$inventories->count()" icon="bi-boxes" color="primary" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Còn hàng" :value="$inventories->where('stock_status', 'ok')->count()" icon="bi-check-circle" color="success" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Sắp hết hàng" :value="$inventories->where('stock_status', 'low')->count()" icon="bi-exclamation-triangle" color="warning" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Hết hàng" :value="$inventories->where('stock_status', 'out')->count()" icon="bi-x-circle" color="danger" />
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
