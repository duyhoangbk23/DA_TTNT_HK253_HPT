@php
    $menu = [
        ['section' => 'Tổng quan'],
        ['route' => 'dashboard',        'label' => 'Dashboard',        'icon' => 'bi-grid-1x2'],
        ['section' => 'Quản lý'],
        ['route' => 'products.index',   'label' => 'Quản lý sản phẩm',  'icon' => 'bi-box-seam'],
        ['route' => 'inventory.index',  'label' => 'Kho thiết bị',      'icon' => 'bi-boxes'],
        ['route' => 'batches.index',    'label' => 'Lô hàng',           'icon' => 'bi-truck'],
        ['route' => 'customers.index',  'label' => 'Khách hàng',        'icon' => 'bi-people'],
        ['route' => 'contracts.index',  'label' => 'Hợp đồng',          'icon' => 'bi-file-earmark-text'],
        ['route' => 'devices.index',    'label' => 'Thiết bị',          'icon' => 'bi-cpu'],
        ['route' => 'mcus.index',       'label' => 'MCU/Controller',     'icon' => 'bi-microchip'],
        ['route' => 'maintenance-work-orders.index', 'label' => 'Bảo trì', 'icon' => 'bi-tools'],
        ['section' => 'Hệ thống'],
        ['route' => 'employees.index',  'label' => 'Nhân viên',         'icon' => 'bi-person-badge'],
        ['route' => 'activities.index', 'label' => 'Lịch sử hoạt động', 'icon' => 'bi-clock-history'],
        ['route' => 'profile.index',    'label' => 'Hồ sơ cá nhân',     'icon' => 'bi-person-circle'],
    ];

    $isActive = function (string $route) {
        $base = explode('.', $route)[0];
        return request()->routeIs($route) || request()->routeIs($base . '.*');
    };
@endphp

<aside class="app-sidebar">
    <div class="app-sidebar__brand">
        <span class="app-sidebar__logo"><i class="bi bi-droplet-half"></i></span>
        <span class="app-sidebar__title">
            <strong>SmartWater</strong><br>
            <span>Quản lý bảo trì</span>
        </span>
    </div>

    <nav class="app-sidebar__nav">
        @foreach ($menu as $item)
            @if (isset($item['section']))
                <div class="nav-caption">{{ $item['section'] }}</div>
            @else
                <a href="{{ route($item['route']) }}"
                   class="app-nav-link {{ $isActive($item['route']) ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>
