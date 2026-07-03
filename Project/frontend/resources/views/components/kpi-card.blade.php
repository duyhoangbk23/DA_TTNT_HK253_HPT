@props([
    'label'  => '',
    'value'  => '',
    'icon'   => 'bi-graph-up',
    'color'  => 'primary',
    'trend'  => null,
    'up'     => true,
])

<div class="card kpi-card h-100">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="kpi-icon tint-{{ $color }}"><i class="bi {{ $icon }}"></i></div>
        <div class="flex-grow-1">
            <div class="kpi-value">{{ $value }}</div>
            <div class="kpi-label">{{ $label }}</div>
        </div>
        @if ($trend)
            <div class="kpi-trend {{ $up ? 'up' : 'down' }} text-end">
                <i class="bi {{ $up ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i> {{ $trend }}
            </div>
        @endif
    </div>
</div>
