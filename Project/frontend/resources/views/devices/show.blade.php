@extends('layouts.app')

@section('title', 'Chi tiết thiết bị')
@section('page-title', $device['code'])
@section('page-subtitle', 'Thông tin thiết bị, dữ liệu cảm biến (mô phỏng) và nhật ký.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('devices.index') }}">Thiết bị</a></li>
    <li class="breadcrumb-item active">{{ $device['code'] }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('devices.index') }}" class="btn btn-white border"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
@endsection

@section('content')
    <div class="row g-3">
        {{-- Thông tin thiết bị --}}
        <div class="col-12 col-xl-4">
            <x-panel title="Thông tin thiết bị" icon="bi-cpu" class="mb-3">
                <div class="mb-2"><x-status-badge :status="$device['status']" /></div>
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-box-seam"></i></span>
                    <div><div class="cell-sub">Model</div><div class="cell-title">{{ $device['model'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-info"><i class="bi bi-hash"></i></span>
                    <div><div class="cell-sub">Serial</div><div class="cell-title">{{ $device['serial'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-secondary"><i class="bi bi-cpu-fill"></i></span>
                    <div><div class="cell-sub">Firmware</div><div class="cell-title">{{ $device['firmware'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-success"><i class="bi bi-person"></i></span>
                    <div>
                        <div class="cell-sub">Khách hàng</div>
                        <div class="cell-title">{{ $device['customer'] }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-warning"><i class="bi bi-geo-alt"></i></span>
                    <div><div class="cell-sub">Vị trí lắp đặt</div><div class="cell-title">{{ $device['location'] }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-calendar-check"></i></span>
                    <div><div class="cell-sub">Ngày lắp đặt</div><div class="cell-title">{{ $device['install_date'] }}</div></div>
                </div>
            </x-panel>

            {{-- Nhật ký hoạt động --}}
            <x-panel title="Nhật ký hoạt động" icon="bi-activity">
                <div class="timeline">
                    @foreach ($activities as $a)
                        <div class="timeline-item">
                            <div class="timeline-time">{{ $a['time'] }}</div>
                            <div class="timeline-title">{{ $a['action'] }}</div>
                            <div class="cell-sub">{{ $a['module'] }}</div>
                        </div>
                    @endforeach
                </div>
            </x-panel>
        </div>

        <div class="col-12 col-xl-8">
            {{-- Dashboard dữ liệu cảm biến --}}
            <x-panel class="mb-3">
                <x-slot:title>Dữ liệu cảm biến (Mock Data)</x-slot:title>
                <x-slot:actions>
                    <div class="btn-group" role="group" data-range-switch>
                        <button type="button" class="btn btn-sm btn-white border range-btn active" data-range="24h">24 giờ</button>
                        <button type="button" class="btn btn-sm btn-white border range-btn" data-range="7d">7 ngày</button>
                        <button type="button" class="btn btn-sm btn-white border range-btn" data-range="30d">30 ngày</button>
                    </div>
                </x-slot:actions>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="cell-sub mb-1"><i class="bi bi-droplet me-1"></i>TDS (ppm)</div>
                        <div id="chart-tds" data-height="220"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="cell-sub mb-1"><i class="bi bi-thermometer-half me-1"></i>Nhiệt độ (°C)</div>
                        <div id="chart-temperature" data-height="220"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="cell-sub mb-1"><i class="bi bi-water me-1"></i>Lưu lượng nước (L)</div>
                        <div id="chart-flow" data-height="220"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="cell-sub mb-1"><i class="bi bi-moisture me-1"></i>pH</div>
                        <div id="chart-ph" data-height="220"></div>
                    </div>
                </div>
            </x-panel>

            {{-- Nhật ký bảo trì --}}
            <x-panel title="Nhật ký bảo trì" icon="bi-tools" flush>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Mã</th><th>Ngày</th><th>Loại</th><th>Kỹ thuật viên</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($maintenance as $m)
                                <tr>
                                    <td class="cell-title">{{ $m['code'] }}</td>
                                    <td>{{ $m['date'] }}</td>
                                    <td>{{ $m['type_label'] }}</td>
                                    <td>{{ $m['employee'] }}</td>
                                    <td><x-status-badge :status="$m['status']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted-2 py-4">Chưa có lịch sử bảo trì.</td></tr>
                            @endforelse
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
        const telemetry = @json($telemetry);

        const els = {
            tds: document.querySelector('#chart-tds'),
            temperature: document.querySelector('#chart-temperature'),
            flow: document.querySelector('#chart-flow'),
            ph: document.querySelector('#chart-ph'),
        };

        SW.areaChart(els.tds, 'TDS', telemetry.labels, telemetry.tds, '#1668e3');
        SW.lineChart(els.temperature, 'Nhiệt độ', telemetry.labels, telemetry.temperature, '#e0304a');
        SW.areaChart(els.flow, 'Lưu lượng', telemetry.labels, telemetry.water_flow, '#17b6d6');
        SW.lineChart(els.ph, 'pH', telemetry.labels, telemetry.ph.map(v => (v / 10).toFixed(1)), '#16a34a');

        // Chỉ mô phỏng đổi bộ lọc thời gian trên giao diện (mock data tĩnh)
        document.querySelectorAll('[data-range-switch] .range-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
@endpush
