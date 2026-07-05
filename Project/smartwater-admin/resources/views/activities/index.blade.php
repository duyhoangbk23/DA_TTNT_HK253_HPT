@extends('layouts.app')

@section('title', 'Lịch sử hoạt động')
@section('page-title', 'Lịch sử hoạt động')
@section('page-subtitle', 'Nhật ký thao tác của người dùng trong hệ thống.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Lịch sử hoạt động</li>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <input type="search" class="form-control form-control-sm" style="width: 240px;"
                   placeholder="Tìm hoạt động..." data-dt-search="#tblActivities">
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblActivities" data-datatable data-no-sort="0">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người thực hiện</th>
                        <th>Hành động</th>
                        <th>Mô tả</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activities as $a)
                        <tr>
                            <td class="cell-sub">{{ $a['time'] }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $a['avatar'] }}" class="table-avatar" alt="{{ $a['user'] }}">
                                    <span class="cell-title">{{ $a['user'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="d-inline-flex align-items-center gap-2">
                                    <i class="bi {{ $a['icon'] }} text-primary"></i> {{ $a['action'] }}
                                </span>
                            </td>
                            <td class="cell-sub">{{ $a['description'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
