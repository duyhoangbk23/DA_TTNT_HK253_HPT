@extends('layouts.app')

@section('title', 'Khách hàng')
@section('page-title', 'Khách hàng')
@section('page-subtitle', 'Danh sách khách hàng cá nhân và doanh nghiệp.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Khách hàng</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Thêm khách hàng</button>
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
                    <option value="Hoạt động">Hoạt động</option>
                    <option value="Ngưng hoạt động">Ngưng hoạt động</option>
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblCustomers" data-datatable data-no-sort="0,4,5">
                <thead>
                    <tr>
                        <th></th>
                        <th>Khách hàng</th>
                        <th>Điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $c)
                        <tr>
                            <td><img src="{{ $c['avatar'] }}" class="table-avatar" alt="{{ $c['name'] }}"></td>
                            <td>
                                <div class="cell-title">{{ $c['name'] }}</div>
                                <div class="cell-sub">{{ $c['email'] }} · {{ $c['code'] }}</div>
                            </td>
                            <td>{{ $c['phone'] }}</td>
                            <td>{{ $c['address'] }}</td>
                            <td><x-status-badge :status="$c['status']" /></td>
                            <td class="text-end">
                                <a href="{{ route('customers.show', $c['id']) }}" class="btn btn-sm btn-soft-primary">
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
