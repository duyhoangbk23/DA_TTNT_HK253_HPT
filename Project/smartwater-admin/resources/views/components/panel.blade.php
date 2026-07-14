@props(['title' => null, 'subtitle' => null, 'icon' => null, 'flush' => false])

<div {{ $attributes->merge(['class' => 'card h-100']) }}>
    @if ($title || isset($actions))
        <div class="card-header d-flex justify-content-between align-items-center gap-2">
            <span class="d-flex align-items-center gap-2">
                @if ($icon)<i class="bi {{ $icon }} text-primary"></i>@endif
                <span>
                    {{ $title }}
                    @if ($subtitle)<small class="d-block text-muted-2 fw-normal">{{ $subtitle }}</small>@endif
                </span>
            </span>
            @isset($actions)
                <span class="d-flex align-items-center gap-2">{{ $actions }}</span>
            @endisset
        </div>
    @endif
    <div class="{{ $flush ? '' : 'card-body' }}">
        {{ $slot }}
    </div>
</div>
