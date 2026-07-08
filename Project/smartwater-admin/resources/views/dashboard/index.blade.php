@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Tổng quan hệ thống')
@section('page-subtitle', 'Theo dõi nhanh tình hình khách hàng, thiết bị và dịch vụ bảo trì.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    {{-- Buttons removed for demo --}}
@endsection

@section('content')
    {{-- KPI --}}
    <div class="row g-3 mb-4">
        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
                <x-kpi-card :label="$kpi['label']" :value="number_format($kpi['value'])"
                            :icon="$kpi['icon']" :color="$kpi['color']"
                            :trend="$kpi['trend']" :up="$kpi['up']" />
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-4">
            <x-panel title="Thiết bị theo trạng thái" icon="bi-pie-chart">
                <div id="chart-device-status" data-height="290"></div>
            </x-panel>
        </div>
        <div class="col-12 col-xl-8">
            <x-panel title="Khách hàng mới theo tháng" icon="bi-graph-up-arrow"
                     subtitle="Số khách hàng đăng ký dịch vụ trong năm">
                <div id="chart-customers" data-height="290"></div>
            </x-panel>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <x-panel title="Lượt bảo trì theo tháng" icon="bi-bar-chart">
                <div id="chart-maintenance" data-height="300"></div>
            </x-panel>
        </div>
        <div class="col-12 col-xl-4">
            {{-- Hợp đồng sắp hết hạn --}}
            <x-panel title="Hợp đồng sắp hết hạn" icon="bi-calendar-x">
                @forelse ($expiringContracts as $c)
                    <div class="list-item">
                        <span class="list-icon tint-warning"><i class="bi bi-file-earmark-text"></i></span>
                        <div class="flex-grow-1">
                            <div class="cell-title">{{ $c['code'] }}</div>
                            <div class="cell-sub">{{ $c['customer'] }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted-2">Hết hạn</div>
                            <div class="fw-semibold" style="font-size: .82rem;">{{ $c['end_date'] }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted-2 mb-0">Không có hợp đồng nào sắp hết hạn.</p>
                @endforelse
            </x-panel>
        </div>
    </div>

    {{-- Widgets: Recent activity + Recent maintenance --}}
    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <x-panel title="Hoạt động gần đây" icon="bi-activity">
                <x-slot:actions>
                    <a href="{{ route('activities.index') }}" class="small link-primary">Xem tất cả</a>
                </x-slot:actions>
                <div class="timeline">
                    @foreach ($recentActivity as $a)
                        <div class="timeline-item">
                            <div class="timeline-time">{{ $a['time'] }}</div>
                            <div class="timeline-title">{{ $a['action'] }}</div>
                            <div class="cell-sub">{{ $a['user'] }} · {{ $a['module'] }}</div>
                        </div>
                    @endforeach
                </div>
            </x-panel>
        </div>
        <div class="col-12 col-xl-6">
            <x-panel title="Lịch bảo trì gần đây" icon="bi-tools" flush>
                <x-slot:actions>
                    <a href="#" class="small link-primary">Xem tất cả</a>
                </x-slot:actions>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr><th>Mã</th><th>Khách hàng</th><th>Kỹ thuật viên</th><th>Ngày</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($recentMaint as $m)
                                <tr>
                                    <td class="cell-title">{{ $m['code'] }}</td>
                                    <td>{{ $m['customer'] }}</td>
                                    <td>{{ $m['employee'] }}</td>
                                    <td>{{ $m['date'] }}</td>
                                    <td><x-status-badge :status="$m['status']" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-panel>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        SW.donutChart(
            document.querySelector('#chart-device-status'),
            @json($deviceStatus['labels']),
            @json($deviceStatus['series'])
        );
        SW.areaChart(
            document.querySelector('#chart-customers'),
            'Khách hàng mới',
            @json($customersMonth['labels']),
            @json($customersMonth['series'])
        );
        SW.barChart(
            document.querySelector('#chart-maintenance'),
            'Lượt bảo trì',
            @json($maintenanceMonth['labels']),
            @json($maintenanceMonth['series'])
        );
    });
</script>
@endpush
