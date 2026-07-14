@props(['status'])

@php
    $map = [
        'active'      => 'Hoạt động',
        'inactive'    => 'Ngưng hoạt động',
        'maintenance' => 'Bảo trì',
        'error'       => 'Lỗi',
        'pending'     => 'Chờ lắp đặt',
        'expired'     => 'Hết hạn',
        'cancelled'   => 'Đã hủy',
        'completed'   => 'Hoàn thành',
        'low'         => 'Sắp hết hàng',
        'out'         => 'Hết hàng',
        'ok'          => 'Còn hàng',
    ];
    $label = $map[$status] ?? ucfirst($status);
@endphp

<span class="badge badge-status badge-{{ $status }}">{{ $label }}</span>
