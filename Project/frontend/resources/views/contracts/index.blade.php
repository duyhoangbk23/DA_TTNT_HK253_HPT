@extends('layouts.app')

@section('title', 'Hợp đồng')
@section('page-title', 'Hợp đồng')
@section('page-subtitle', 'Quản lý hợp đồng lắp đặt, bảo trì và thay thế thiết bị.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Hợp đồng</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Tạo hợp đồng</button>
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
                    <option value="Hoạt động">Hoạt động</option>
                    <option value="Hết hạn">Hết hạn</option>
                    <option value="Đã hủy">Đã hủy</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblContracts" data-datatable data-no-sort="6">
                <thead>
                    <tr>
                        <th>Mã hợp đồng</th>
                        <th>Khách hàng</th>
                        <th>Thiết bị</th>
                        <th>Ngày ký</th>
                        <th>Ngày lắp đặt</th>
                        <th>Chu kỳ bảo trì</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contracts as $c)
                        <tr>
                            <td class="cell-title">{{ $c['code'] }}</td>
                            <td>{{ $c['customer'] }}</td>
                            <td>{{ $c['device_code'] }}</td>
                            <td>{{ $c['sign_date'] }}</td>
                            <td>{{ $c['install_date'] }}</td>
                            <td>{{ $c['cycle'] }}</td>
                            <td><x-status-badge :status="$c['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
