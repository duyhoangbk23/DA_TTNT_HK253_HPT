@extends('layouts.app')

@section('title', 'Nhân viên')
@section('page-title', 'Nhân viên')
@section('page-subtitle', 'Danh sách nhân viên kỹ thuật và quản lý.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Nhân viên</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Thêm nhân viên</button>
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
                        <option value="{{ $r['name'] }}">{{ $r['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblEmployees" data-datatable data-no-sort="0,3">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nhân viên</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $e)
                        <tr>
                            <td><img src="{{ $e['avatar'] }}" class="table-avatar" alt="{{ $e['name'] }}"></td>
                            <td>
                                <div class="cell-title">{{ $e['name'] }}</div>
                                <div class="cell-sub">{{ $e['email'] }} · {{ $e['code'] }}</div>
                            </td>
                            <td>{{ $e['role'] }}</td>
                            <td><x-status-badge :status="$e['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
