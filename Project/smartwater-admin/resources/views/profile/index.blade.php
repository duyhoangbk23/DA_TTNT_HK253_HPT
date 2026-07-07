@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')
@section('page-title', 'Hồ sơ cá nhân')
@section('page-subtitle', 'Quản lý thông tin tài khoản và bảo mật.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Hồ sơ cá nhân</li>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-12 col-xl-4">
            <x-panel>
                <div class="text-center mb-3">
                    <div class="avatar-lg mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; width: 100px; height: 100px; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: bold;">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                    <h5 class="mb-0">{{ $user->username }}</h5>
                    <div class="cell-sub">{{ $user->email }}</div>
                    <span class="badge tint-primary mt-2">{{ $user->role?->name ?? 'User' }}</span>
                </div>
            </x-panel>

            <x-panel title="Hoạt động gần đây" icon="bi-activity" class="mt-3">
                <div class="timeline">
                    @foreach ($activities as $a)
                        <div class="timeline-item">
                            <div class="timeline-time">{{ $a['time'] }}</div>
                            <div class="timeline-title">{{ $a['action'] }}</div>
                        </div>
                    @endforeach
                </div>
            </x-panel>
        </div>

        <div class="col-12 col-xl-8">
            <x-panel title="Thông tin cá nhân" icon="bi-person-vcard" class="mb-3">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vai trò</label>
                            <input type="text" class="form-control" value="{{ $user->role?->name ?? 'User' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->status ?? 'active') }}" disabled>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Lưu thay đổi</button>
                    </div>
                </form>
            </x-panel>

            <x-panel title="Đổi mật khẩu" icon="bi-shield-lock">
                <form method="POST" action="{{ route('profile.updatePassword') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                   required placeholder="••••••••">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   required placeholder="••••••••">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   required placeholder="••••••••">
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-shield-check me-1"></i> Cập nhật mật khẩu</button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
@endsection
