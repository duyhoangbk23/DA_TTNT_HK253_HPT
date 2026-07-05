<header class="app-navbar">
    <button class="icon-btn" type="button" data-toggle-sidebar aria-label="Thu gọn menu">
        <i class="bi bi-list"></i>
    </button>

    <form class="app-navbar__search d-none d-sm-block" onsubmit="return false;">
        <i class="bi bi-search"></i>
        <input type="search" placeholder="Tìm kiếm khách hàng, thiết bị, hợp đồng...">
    </form>

    <div class="ms-auto d-flex align-items-center gap-2">
        {{-- Notifications --}}
        <div class="dropdown">
            <button class="icon-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Thông báo">
                <i class="bi bi-bell"></i>
                <span class="dot"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 shadow" style="width: 320px;">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <strong>Thông báo</strong>
                    <span class="badge tint-primary">{{ $navNotifications->count() }} mới</span>
                </div>
                <div class="py-1" style="max-height: 320px; overflow-y: auto;">
                    @foreach ($navNotifications as $n)
                        <a href="#" class="dropdown-item d-flex gap-2 py-2 text-wrap">
                            <span class="list-icon tint-{{ $n['color'] }} flex-shrink-0"><i class="bi {{ $n['icon'] }}"></i></span>
                            <span>
                                <span class="d-block cell-title" style="font-size: .84rem;">{{ $n['title'] }}</span>
                                <small class="text-muted-2">{{ $n['time'] }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
                <a href="#" class="d-block text-center py-2 border-top small link-primary">Xem tất cả</a>
            </div>
        </div>

        {{-- Messages --}}
        <button class="icon-btn d-none d-sm-grid" type="button" aria-label="Tin nhắn">
            <i class="bi bi-envelope"></i>
        </button>

        {{-- Avatar dropdown --}}
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 border-0 p-1" type="button" data-bs-toggle="dropdown">
                <img src="{{ $currentUser['avatar'] }}" alt="avatar" class="navbar-avatar">
                <span class="d-none d-md-block text-start lh-1">
                    <span class="d-block cell-title" style="font-size: .85rem;">{{ $currentUser['name'] }}</span>
                    <small class="text-muted-2">{{ $currentUser['position'] }}</small>
                </span>
                <i class="bi bi-chevron-down small text-muted-2 d-none d-md-block"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="{{ route('login') }}"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</header>
