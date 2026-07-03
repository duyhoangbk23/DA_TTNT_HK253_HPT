@extends('layouts.app')

@section('title', 'Chi tiết khách hàng')
@section('page-title', $customer['name'])
@section('page-subtitle', 'Thông tin chi tiết, thiết bị, hợp đồng và lịch sử bảo trì.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Khách hàng</a></li>
    <li class="breadcrumb-item active">{{ $customer['name'] }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('customers.index') }}" class="btn btn-white border"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
@endsection

@section('content')
    <div class="row g-3">
        {{-- Thông tin cá nhân --}}
        <div class="col-12 col-xl-4">
            <x-panel>
                <div class="text-center mb-3">
                    <img src="{{ $customer['avatar'] }}" class="avatar-lg mb-3" alt="{{ $customer['name'] }}">
                    <h5 class="mb-0">{{ $customer['name'] }}</h5>
                    <div class="cell-sub">{{ $customer['code'] }}</div>
                    <div class="mt-2"><x-status-badge :status="$customer['status']" /></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-envelope"></i></span>
                    <div><div class="cell-sub">Email</div><div class="cell-title">{{ $customer['email'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-info"><i class="bi bi-telephone"></i></span>
                    <div><div class="cell-sub">Điện thoại</div><div class="cell-title">{{ $customer['phone'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-secondary"><i class="bi bi-geo-alt"></i></span>
                    <div><div class="cell-sub">Địa chỉ</div><div class="cell-title">{{ $customer['address'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-success"><i class="bi bi-person-badge"></i></span>
                    <div><div class="cell-sub">Loại khách hàng</div><div class="cell-title">{{ $customer['type'] === 'company' ? 'Doanh nghiệp' : 'Cá nhân' }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-warning"><i class="bi bi-calendar-check"></i></span>
                    <div><div class="cell-sub">Ngày tham gia</div><div class="cell-title">{{ $customer['joined'] }}</div></div>
                </div>
            </x-panel>
        </div>

        <div class="col-12 col-xl-8">
            {{-- Danh sách thiết bị --}}
            <x-panel title="Thiết bị đang sử dụng" icon="bi-cpu" flush class="mb-3">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Mã thiết bị</th><th>Model</th><th>Ngày lắp đặt</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($devices as $d)
                                <tr>
                                    <td><a href="{{ route('devices.show', $d['id']) }}" class="cell-title link-primary">{{ $d['code'] }}</a></td>
                                    <td>{{ $d['model'] }}</td>
                                    <td>{{ $d['install_date'] }}</td>
                                    <td><x-status-badge :status="$d['status']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted-2 py-4">Chưa có thiết bị nào.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-panel>

            {{-- Danh sách hợp đồng --}}
            <x-panel title="Hợp đồng" icon="bi-file-earmark-text" flush class="mb-3">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Mã hợp đồng</th><th>Loại</th><th>Ngày ký</th><th>Ngày hết hạn</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($contracts as $c)
                                <tr>
                                    <td class="cell-title">{{ $c['code'] }}</td>
                                    <td>{{ $c['type_label'] }}</td>
                                    <td>{{ $c['sign_date'] }}</td>
                                    <td>{{ $c['end_date'] }}</td>
                                    <td><x-status-badge :status="$c['status']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted-2 py-4">Chưa có hợp đồng nào.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-panel>

            {{-- Timeline lịch sử bảo trì --}}
            <x-panel title="Lịch sử bảo trì" icon="bi-clock-history">
                <div class="timeline">
                    @forelse ($maintenance as $m)
                        <div class="timeline-item">
                            <div class="timeline-time">{{ $m['date'] }} · {{ $m['code'] }}</div>
                            <div class="timeline-title">{{ $m['type_label'] }} - {{ $m['device_code'] }}</div>
                            <div class="cell-sub">{{ $m['description'] }} · KTV: {{ $m['employee'] }}</div>
                        </div>
                    @empty
                        <p class="text-muted-2 mb-0">Chưa có lịch sử bảo trì nào.</p>
                    @endforelse
                </div>
            </x-panel>
        </div>
    </div>
@endsection
