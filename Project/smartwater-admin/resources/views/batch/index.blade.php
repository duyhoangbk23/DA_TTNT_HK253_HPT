@extends('layouts.app')

@section('title', 'Lô hàng')
@section('page-title', 'Lô hàng')
@section('page-subtitle', 'Quản lý các lô hàng nhập kho từ nhà cung cấp.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Lô hàng</li>
@endsection

@section('page-actions')
    <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Tạo lô hàng</button>
@endsection

@section('content')
    <x-panel flush>
        <x-slot:actions>
            <input type="search" class="form-control form-control-sm" style="width: 220px;"
                   placeholder="Tìm lô hàng..." data-dt-search="#tblBatches">
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table align-middle" id="tblBatches" data-datatable data-no-sort="5">
                <thead>
                    <tr>
                        <th>Mã lô</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Hạn sử dụng</th>
                        <th>Số lượng</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $b)
                        <tr>
                            <td class="cell-title">{{ $b['code'] }}</td>
                            <td>{{ $b['supplier'] }}</td>
                            <td>{{ $b['import_date'] }}</td>
                            <td>{{ $b['expiry_date'] }}</td>
                            <td>{{ number_format($b['quantity']) }}</td>
                            <td class="text-end">
                                <a href="{{ route('batches.show', $b['id']) }}" class="btn btn-sm btn-soft-primary">
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
