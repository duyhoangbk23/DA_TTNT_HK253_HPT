<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập · SmartWater Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
    {{-- Cột thương hiệu --}}
    <div class="auth-side">
        <div class="d-flex align-items-center gap-2">
            <span class="app-sidebar__logo"><i class="bi bi-droplet-half"></i></span>
            <div class="lh-1">
                <strong class="fs-5">SmartWater</strong><br>
                <small class="opacity-75">Quản lý dịch vụ bảo trì</small>
            </div>
        </div>

        <div style="position: relative; z-index: 1;">
            <h2 class="fw-bold mb-3" style="font-size: 2rem;">Nguồn nước sạch,<br>quản lý thông minh.</h2>
            <p class="opacity-75 mb-4" style="max-width: 420px;">
                Nền tảng quản trị toàn diện cho dịch vụ lắp đặt, bảo trì và chăm sóc
                hệ thống máy lọc nước của doanh nghiệp bạn.
            </p>
            <div class="d-flex gap-4">
                <div><div class="fs-4 fw-bold">1.200+</div><small class="opacity-75">Thiết bị quản lý</small></div>
                <div><div class="fs-4 fw-bold">98%</div><small class="opacity-75">Đúng lịch bảo trì</small></div>
                <div><div class="fs-4 fw-bold">24/7</div><small class="opacity-75">Giám sát</small></div>
            </div>
        </div>

        <small class="opacity-75">© {{ date('Y') }} SmartWater.</small>
    </div>

    {{-- Cột biểu mẫu --}}
    <div class="auth-form-wrap">
        <div class="auth-card">
            <div class="text-center d-lg-none mb-4">
                <span class="app-sidebar__logo mx-auto"><i class="bi bi-droplet-half"></i></span>
            </div>
            <h1 class="h4 fw-bold mb-1">Chào mừng trở lại 👋</h1>
            <p class="text-muted-2 mb-4">Đăng nhập để tiếp tục vào bảng điều khiển.</p>

            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted-2"></i></span>
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                               placeholder="admin@smartwater.vn" value="{{ old('email', 'admin@smartwater.vn') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-lock text-muted-2"></i></span>
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                </button>
            </form>

            <p class="text-center text-muted-2 mt-4 mb-0 small">
                Test accounts: <strong>admin@smartwater.vn</strong> | Password: <strong>password123</strong>
            </p>
        </div>
    </div>
</div>
</body>
</html>
