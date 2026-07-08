@extends('layouts.app')

@section('title', 'Chi tiết hợp đồng')
@section('page-title', "Hợp đồng #{$contract->contract_code}")
@section('page-subtitle', 'Xem các bộ lọc nước và MCU đã đăng ký theo hợp đồng.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('contracts.index') }}">Hợp đồng</a></li>
    <li class="breadcrumb-item active">{{ $contract->contract_code }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('contracts.index') }}" class="btn btn-white border"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-12 col-xl-4">
            <x-panel title="Thông tin hợp đồng" icon="bi-file-earmark-text" class="mb-3">
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-hash"></i></span>
                    <div>
                        <div class="cell-sub">Mã hợp đồng</div>
                        <div class="cell-title">{{ $contract->contract_code }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-info"><i class="bi bi-person"></i></span>
                    <div>
                        <div class="cell-sub">Khách hàng</div>
                        <div class="cell-title">{{ $contract->customer?->customer_name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-secondary"><i class="bi bi-list-ul"></i></span>
                    <div>
                        <div class="cell-sub">Loại hợp đồng</div>
                        <div class="cell-title">{{ ucfirst($contract->contract_type) }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-success"><i class="bi bi-calendar-check"></i></span>
                    <div>
                        <div class="cell-sub">Ngày ký</div>
                        <div class="cell-title">{{ $contract->start_date?->format('d/m/Y') ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-warning"><i class="bi bi-calendar-x"></i></span>
                    <div>
                        <div class="cell-sub">Ngày kết thúc</div>
                        <div class="cell-title">{{ $contract->end_date?->format('d/m/Y') ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-danger"><i class="bi bi-cash-stack"></i></span>
                    <div>
                        <div class="cell-sub">Giá trị</div>
                        <div class="cell-title">{{ number_format($contract->amount) }} ₫</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-lightning-charge"></i></span>
                    <div>
                        <div class="cell-sub">Trạng thái</div>
                        <div class="cell-title"><x-status-badge :status="$contract->status" /></div>
                    </div>
                </div>
            </x-panel>
        </div>

        <div class="col-12 col-xl-8">
            <x-panel title="Thiết bị và MCU theo hợp đồng" icon="bi-qr-code-scan" class="mb-3">
                @if($contract->devices->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Thiết bị</th>
                                    <th>Serial</th>
                                    <th>MCU</th>
                                    <th>Serial MCU</th>
                                    <th>Ngày lắp</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->devices as $device)
                                    <tr>
                                        <td>{{ $device->device_code }}</td>
                                        <td>{{ $device->serial_number }}</td>
                                        <td>{{ $device->mcu?->mcu_code ?? 'Chưa gắn MCU' }}</td>
                                        <td>{{ $device->mcu?->serial_number ?? '-' }}</td>
                                        <td>{{ $device->install_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                        <td><x-status-badge :status="$device->status" /></td>
                                        <td>
                                            <a href="{{ route('devices.show', $device->id) }}" class="btn btn-sm btn-outline-primary">
                                                Xem dashboard
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted-2 py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2">Hợp đồng này chưa có thiết bị nào được đăng ký.</p>
                    </div>
                @endif
            </x-panel>
        </div>
    </div>
@endsection
