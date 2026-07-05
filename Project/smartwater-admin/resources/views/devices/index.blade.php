@extends('layouts.app')

@section('title', 'Thiết bị')
@section('page-title', 'Thiết bị')
@section('page-subtitle', 'Danh sách thiết bị máy lọc nước đã lắp đặt tại khách hàng.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Thiết bị</li>
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

    <x-panel flush>
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" class="form-control form-control-sm" style="width: 220px;"
                       placeholder="Tìm thiết bị..." data-dt-search="#tblDevices">
                <select class="form-select form-select-sm" style="width: 170px;"
                        data-dt-filter="#tblDevices" data-dt-column="4">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Hoạt động">Hoạt động</option>
                    <option value="Bảo trì">Bảo trì</option>
                    <option value="Lỗi">Lỗi</option>
                    <option value="Chờ lắp đặt">Chờ lắp đặt</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblDevices" data-datatable data-no-sort="4,5">
                <thead>
                    <tr>
                        <th>Device ID</th>
                        <th>Model</th>
                        <th>Firmware</th>
                        <th>Serial</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devices as $d)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $d['code'] }}</div>
                                <div class="cell-sub">{{ $d['customer'] }}</div>
                            </td>
                            <td>{{ $d['model'] }}</td>
                            <td>{{ $d['firmware'] }}</td>
                            <td>{{ $d['serial'] }}</td>
                            <td><x-status-badge :status="$d['status']" /></td>
                            <td class="text-end">
                                <a href="{{ route('devices.show', $d['id']) }}" class="btn btn-sm btn-soft-primary">
                                    <i class="bi bi-eye me-1"></i>Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
