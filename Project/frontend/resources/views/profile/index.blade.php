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
                    <img src="{{ $user['avatar'] }}" class="avatar-lg mb-3" alt="{{ $user['name'] }}">
                    <h5 class="mb-0">{{ $user['name'] }}</h5>
                    <div class="cell-sub">{{ $user['position'] }}</div>
                    <span class="badge tint-primary mt-2">{{ $user['role'] }}</span>
                </div>
                <button class="btn btn-soft-primary w-100"><i class="bi bi-camera me-1"></i> Đổi ảnh đại diện</button>
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
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên</label>
                            <input type="text" class="form-control" value="{{ $user['name'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chức vụ</label>
                            <input type="text" class="form-control" value="{{ $user['position'] }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" value="{{ $user['email'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" class="form-control" value="{{ $user['phone'] }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" class="form-control" value="{{ $user['address'] }}">
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Lưu thay đổi</button>
                    </div>
                </form>
            </x-panel>

            <x-panel title="Đổi mật khẩu" icon="bi-shield-lock">
                <form>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mật khẩu mới</label>
                            <input type="password" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-primary"><i class="bi bi-shield-check me-1"></i> Cập nhật mật khẩu</button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
@endsection
