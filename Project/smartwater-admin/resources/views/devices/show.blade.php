@extends('layouts.app')

@section('title', 'Chi tiết thiết bị')
@section('page-title', $device->device_code)
@section('page-subtitle', 'Thông tin thiết bị, dữ liệu cảm biến và nhật ký bảo trì.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('devices.index') }}">Thiết bị</a></li>
    <li class="breadcrumb-item active">{{ $device->device_code }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('devices.index') }}" class="btn btn-white border"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
@endsection

@section('content')
    <div class="row g-3">
        {{-- Thông tin thiết bị --}}
        <div class="col-12 col-xl-4">
            <x-panel title="Thông tin thiết bị" icon="bi-cpu" class="mb-3">
                <div class="mb-2"><x-status-badge :status="$device->status" /></div>
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-box-seam"></i></span>
                    <div><div class="cell-sub">Sản phẩm</div><div class="cell-title">{{ $device->product->product_name ?? 'N/A' }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-info"><i class="bi bi-hash"></i></span>
                    <div><div class="cell-sub">Serial</div><div class="cell-title">{{ $device->serial_number }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-secondary"><i class="bi bi-cpu-fill"></i></span>
<div><div class="cell-sub">MCU</div><div class="cell-title">{{ $device->mcu->mcu_id ?? 'Chưa gắn MCU' }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-success"><i class="bi bi-person"></i></span>
                    <div>
                        <div class="cell-sub">Khách hàng</div>
                        <div class="cell-title">{{ $device->customer->customer_name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-warning"><i class="bi bi-geo-alt"></i></span>
                    <div><div class="cell-sub">Vị trí lắp đặt</div><div class="cell-title">{{ $device->location ?? 'N/A' }}</div></div>
                </div>
                <div class="list-item">
                    <span class="list-icon tint-primary"><i class="bi bi-calendar-check"></i></span>
                    <div><div class="cell-sub">Ngày lắp đặt</div><div class="cell-title">{{ $device->install_date?->format('d/m/Y') ?? 'N/A' }}</div></div>
                </div>
            </x-panel>

            {{-- Lịch sử thiết bị --}}
            @if($device->replaces->count() > 0 || $device->replacedBy)
                <x-panel title="Lịch sử thiết bị" icon="bi-clock-history" class="mb-3">
                    <div class="mb-2" style="font-size: 0.9rem;">
                        @if($device->replacedBy)
                            <div><strong>Thay thế bởi:</strong> <a href="{{ route('devices.show', $device->replacedBy->id) }}">{{ $device->replacedBy->device_code }}</a></div>
                            <div class="text-muted-2">Ngày: {{ $device->replaced_at?->format('d/m/Y H:i') }}</div>
                        @endif
                        @foreach($device->replaces as $replaced)
                            <div><strong>Thay thế:</strong> <a href="{{ route('devices.show', $replaced->id) }}">{{ $replaced->device_code }}</a></div>
                        @endforeach
                    </div>
                </x-panel>
            @endif

            {{-- Nút thay thiết bị --}}
            @if(!$device->replaced_at)
                <div class="mb-3">
                    <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#modalReplaceDevice">
                        <i class="bi bi-arrow-repeat me-1"></i>Thay thiết bị (bảo trì)
                    </button>
                </div>
            @endif
        </div>

        <div class="col-12 col-xl-8">
            {{-- Dashboard dữ liệu cảm biến --}}
            @if(count($telemetry['labels']) > 0)
                <x-panel class="mb-3">
                    <x-slot:title>Dữ liệu cảm biến</x-slot:title>
                    <div class="row g-3">
                        <div class="col-12 col-lg-7">
                            <div class="cell-sub mb-1"><i class="bi bi-droplet me-1"></i>TDS (ppm)</div>
                            <div id="chart-tds" data-height="220"></div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="cell-sub mb-2"><i class="bi bi-exclamation-triangle me-1"></i>Cảnh báo gần đây</div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>TDS</th>
                                            <th>Alert</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($telemetryLogs as $row)
                                            <tr>
                                                <td>{{ $row->timestamp->format('d/m/Y H:i:s') }}</td>
                                                <td>{{ $row->tds ?? '-' }}</td>
                                                <td><x-status-badge :status="$row->alert ?: 'normal'" /></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted-2">Chưa có log telemetry.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($telemetryLogs->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    @if($telemetryLogs->onFirstPage())
                                        <span class="btn btn-sm btn-light border disabled">Trước</span>
                                    @else
                                        <a class="btn btn-sm btn-light border" href="{{ $telemetryLogs->previousPageUrl() }}">Trước</a>
                                    @endif
                                    <span class="text-muted-2 small">Trang {{ $telemetryLogs->currentPage() }}/{{ $telemetryLogs->lastPage() }}</span>
                                    @if($telemetryLogs->hasMorePages())
                                        <a class="btn btn-sm btn-light border" href="{{ $telemetryLogs->nextPageUrl() }}">Sau</a>
                                    @else
                                        <span class="btn btn-sm btn-light border disabled">Sau</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </x-panel>
            @else
                <x-panel class="mb-3">
                    <div class="text-center text-muted-2 py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2">Chưa có dữ liệu cảm biến</p>
                    </div>
                </x-panel>
            @endif

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
                                    <td class="cell-title">{{ $m->maintenance_code }}</td>
                                    <td>{{ $m->maintenance_date->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($m->maintenance_type) }}</td>
                                    <td>{{ $m->employee->full_name ?? 'N/A' }}</td>
                                    <td><x-status-badge :status="$m->status" /></td>
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

{{-- Modal Thay thiết bị --}}
@if(!$device->replaced_at)
    <div class="modal fade" id="modalReplaceDevice" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('devices.replace', $device->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Thay thiết bị (bảo trì)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sản phẩm mới <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach(\App\Models\Product::all() as $p)
                                    <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">MCU mới <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="mcuSearch" class="form-control" placeholder="Gõ mã hoặc serial MCU...">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Lọc</span>
                                <select id="mcuStatusFilter" class="form-select">
                                    <option value="all">Tất cả</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                    <option value="unused">Chưa lắp đặt</option>
                                </select>
                            </div>
                            <select name="mcu_id" class="form-select" id="mcuSelect" required>
                                <option value="">-- Chọn MCU --</option>
                                @foreach($availableMcus as $m)
                <option value="{{ $m->mcu_id }}"
                                        data-status="{{ $m->status }}"
                                        data-installed="{{ $m->current_device_count > 0 ? '1' : '0' }}">
                    {{ $m->mcu_id }} ({{ $m->serial_number }}) - {{ $m->status ? ucfirst($m->status) : 'N/A' }}{{ $m->current_device_count === 0 ? ' - Chưa lắp đặt' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ngày lắp đặt</label>
                            <input type="date" name="install_date" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Thay thiết bị</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const telemetry = @json($telemetry);

        if (telemetry.labels && telemetry.labels.length > 0) {
            const els = {
                tds: document.querySelector('#chart-tds'),
            };

            SW.areaChart(els.tds, 'TDS', telemetry.labels, telemetry.tds, '#1668e3');
        }

        const mcuSearch = document.getElementById('mcuSearch');
        const mcuStatusFilter = document.getElementById('mcuStatusFilter');
        const mcuSelect = document.getElementById('mcuSelect');

        if (mcuSearch && mcuStatusFilter && mcuSelect) {
            const filterMcuOptions = () => {
                const searchValue = mcuSearch.value.toLowerCase();
                const statusValue = mcuStatusFilter.value;

                Array.from(mcuSelect.options).forEach(option => {
                    if (!option.value) {
                        return;
                    }

                    const text = option.text.toLowerCase();
                    const status = option.dataset.status;
                    const installed = option.dataset.installed === '1';
                    const isUnused = !installed;

                    const matchesSearch = text.includes(searchValue);
                    let matchesStatus = true;

                    if (statusValue === 'online') {
                        matchesStatus = status === 'online';
                    } else if (statusValue === 'offline') {
                        matchesStatus = status === 'offline';
                    } else if (statusValue === 'unused') {
                        matchesStatus = isUnused;
                    }

                    option.hidden = !(matchesSearch && matchesStatus);
                });
            };

            mcuSearch.addEventListener('input', filterMcuOptions);
            mcuStatusFilter.addEventListener('change', filterMcuOptions);
            filterMcuOptions();
        }
    });
</script>
@endpush
