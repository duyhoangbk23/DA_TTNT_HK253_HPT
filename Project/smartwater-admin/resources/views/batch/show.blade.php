@extends('layouts.app')

@section('title', 'Chi tiết lô hàng')
@section('page-title', $batch['code'])
@section('page-subtitle', 'Chi tiết lô hàng và danh sách thiết bị nhập kho.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}">Lô hàng</a></li>
    <li class="breadcrumb-item active">{{ $batch['code'] }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('batches.index') }}" class="btn btn-white border"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Nhà cung cấp" :value="$batch['supplier']" icon="bi-truck" color="primary" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Ngày nhập" :value="$batch['import_date']" icon="bi-calendar-event" color="info" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Hạn sử dụng" :value="$batch['expiry_date']" icon="bi-calendar-x" color="warning" />
        </div>
        <div class="col-6 col-xl-3">
            <x-kpi-card label="Tổng số lượng" :value="number_format($batch['quantity'])" icon="bi-boxes" color="success" />
        </div>
    </div>

    <x-panel title="Ghi chú" icon="bi-sticky" class="mb-3">
        <p class="mb-0">{{ $batch['note'] }}</p>
    </x-panel>

    <x-panel title="Danh sách thiết bị trong lô" icon="bi-list-ul" flush>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Mã</th>
                        <th>Model</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $d)
                        <tr>
                            <td class="cell-title">{{ $d['product'] }}</td>
                            <td>{{ $d['code'] }}</td>
                            <td>{{ $d['model'] }}</td>
                            <td>{{ number_format($d['quantity']) }}</td>
                            <td>{{ number_format($d['unit_cost']) }} đ</td>
                            <td class="text-end fw-semibold">{{ number_format($d['quantity'] * $d['unit_cost']) }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection
