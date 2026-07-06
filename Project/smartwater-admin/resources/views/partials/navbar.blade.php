<header class="app-navbar">
    <button class="icon-btn" type="button" data-toggle-sidebar aria-label="Thu gọn menu">
        <i class="bi bi-list"></i>
    </button>

    <form class="app-navbar__search d-none d-sm-block" onsubmit="return false;">
        <i class="bi bi-search"></i>
        <input type="search" placeholder="Tìm kiếm khách hàng, thiết bị, hợp đồng...">
    </form>

    <div class="ms-auto d-flex align-items-center gap-2">
        {{-- Avatar dropdown --}}
        @auth
            <div class="dropdown">
                <button class="btn d-flex align-items-center gap-2 border-0 p-1" type="button" data-bs-toggle="dropdown">
                    <span class="avatar avatar-sm bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%;">
                        {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
                    </span>
                    <span class="d-none d-md-block text-start lh-1">
                        <span class="d-block cell-title" style="font-size: .85rem;">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                        <small class="text-muted-2">{{ Auth::user()->email }}</small>
                    </span>
                    <i class="bi bi-chevron-down small text-muted-2 d-none d-md-block"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; cursor: pointer;">
                                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</header>
